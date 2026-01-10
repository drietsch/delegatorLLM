<?php
declare(strict_types=1);

namespace App\Api;

use App\Queue\JobQueue;

/**
 * Workflow Controller
 *
 * Handles workflow execution, streaming, and HITL (Human-in-the-Loop) interactions.
 * Workflows are executed asynchronously via the job queue.
 */
class WorkflowController
{
    private JobQueue $queue;
    private string $workflowsPath;

    public function __construct()
    {
        $this->queue = new JobQueue();
        $this->workflowsPath = STORAGE_PATH . '/workflows';

        if (!is_dir($this->workflowsPath . '/runs')) {
            mkdir($this->workflowsPath . '/runs', 0755, true);
        }
        if (!is_dir($this->workflowsPath . '/events')) {
            mkdir($this->workflowsPath . '/events', 0755, true);
        }
    }

    /**
     * Available workflows.
     */
    private const WORKFLOWS = [
        'copilot_orchestrator' => [
            'description' => 'Main copilot chat orchestrator with tool selection',
            'steps' => ['intent', 'tool_selection', 'execution', 'response'],
        ],
        'product_enrichment' => [
            'description' => 'Product data enrichment with HITL approval',
            'steps' => ['retrieve', 'analyze', 'propose', 'approve', 'apply'],
            'hitl' => true,
        ],
        'data_import' => [
            'description' => 'Data import pipeline with validation and mapping',
            'steps' => ['validate', 'parse', 'map', 'import', 'report'],
            'hitl' => true,
        ],
    ];

    /**
     * Start a workflow.
     *
     * @param array $request Request with 'name', 'input', and optional 'async'
     *
     * @return array Run information
     */
    public function run(array $request): array
    {
        $workflowName = $request['name'];
        $input = $request['input'] ?? [];
        $async = $request['async'] ?? true;

        // Validate workflow exists
        if (!isset(self::WORKFLOWS[$workflowName])) {
            throw new \InvalidArgumentException("Unknown workflow: $workflowName");
        }

        $runId = bin2hex(random_bytes(16));
        $workflow = self::WORKFLOWS[$workflowName];

        // Create initial run state
        $runState = [
            'id' => $runId,
            'workflow' => $workflowName,
            'status' => 'queued',
            'progress' => 0,
            'currentStep' => null,
            'steps' => $workflow['steps'],
            'input' => $input,
            'output' => null,
            'error' => null,
            'hitlPending' => false,
            'hitlData' => null,
            'createdAt' => date('c'),
            'updatedAt' => date('c'),
        ];

        $this->saveRunState($runId, $runState);

        // Write initial event
        $this->writeEvent($runId, [
            'type' => 'workflow_start',
            'workflow' => $workflowName,
            'steps' => $workflow['steps'],
        ]);

        if ($async) {
            // Enqueue for background processing
            $jobId = $this->queue->enqueue("workflow:{$workflowName}", [
                'runId' => $runId,
                'input' => $input,
            ]);

            return [
                'runId' => $runId,
                'jobId' => $jobId,
                'status' => 'queued',
                'workflow' => $workflowName,
                'streamUrl' => "/api/workflows/{$runId}/stream",
            ];
        }

        // Synchronous execution
        return $this->executeSynchronously($workflowName, $runId, $input);
    }

    /**
     * Execute workflow synchronously.
     */
    private function executeSynchronously(string $workflowName, string $runId, array $input): array
    {
        $workflow = self::WORKFLOWS[$workflowName];
        $runState = $this->loadRunState($runId);

        $runState['status'] = 'running';
        $this->saveRunState($runId, $runState);

        $stepCount = count($workflow['steps']);
        $context = ['input' => $input, 'results' => []];

        foreach ($workflow['steps'] as $index => $step) {
            $progress = (int) (($index / $stepCount) * 100);

            $runState['currentStep'] = $step;
            $runState['progress'] = $progress;
            $runState['updatedAt'] = date('c');
            $this->saveRunState($runId, $runState);

            $this->writeEvent($runId, [
                'type' => 'step_start',
                'step' => $step,
                'progress' => $progress,
            ]);

            try {
                $result = $this->executeStep($workflowName, $step, $context);
                $context['results'][$step] = $result;

                // Check for HITL pause
                if (isset($result['hitl_required']) && $result['hitl_required']) {
                    $runState['status'] = 'hitl_pending';
                    $runState['hitlPending'] = true;
                    $runState['hitlData'] = $result['hitl_data'] ?? null;
                    $this->saveRunState($runId, $runState);

                    $this->writeEvent($runId, [
                        'type' => 'hitl_required',
                        'step' => $step,
                        'message' => $result['hitl_message'] ?? 'Human approval required',
                        'data' => $result['hitl_data'] ?? null,
                    ]);

                    return [
                        'runId' => $runId,
                        'status' => 'hitl_pending',
                        'workflow' => $workflowName,
                        'currentStep' => $step,
                        'progress' => $progress,
                        'hitlData' => $result['hitl_data'] ?? null,
                    ];
                }

                $this->writeEvent($runId, [
                    'type' => 'step_complete',
                    'step' => $step,
                    'result' => $result,
                ]);
            } catch (\Throwable $e) {
                $runState['status'] = 'failed';
                $runState['error'] = $e->getMessage();
                $this->saveRunState($runId, $runState);

                $this->writeEvent($runId, [
                    'type' => 'step_error',
                    'step' => $step,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'runId' => $runId,
                    'status' => 'failed',
                    'workflow' => $workflowName,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Workflow completed
        $runState['status'] = 'completed';
        $runState['progress'] = 100;
        $runState['output'] = $context['results'];
        $runState['completedAt'] = date('c');
        $this->saveRunState($runId, $runState);

        $this->writeEvent($runId, [
            'type' => 'workflow_complete',
            'progress' => 100,
            'output' => $context['results'],
        ]);

        return [
            'runId' => $runId,
            'status' => 'completed',
            'workflow' => $workflowName,
            'progress' => 100,
            'output' => $context['results'],
        ];
    }

    /**
     * Execute a single workflow step.
     */
    private function executeStep(string $workflow, string $step, array $context): array
    {
        // Simulate step execution - in real implementation, this would call actual step handlers
        usleep(200000); // 200ms per step

        return match ("$workflow:$step") {
            'copilot_orchestrator:intent' => [
                'intent' => 'general_query',
                'confidence' => 0.85,
            ],
            'copilot_orchestrator:tool_selection' => [
                'tools' => ['advanced_search'],
                'reasoning' => 'User query requires search capability',
            ],
            'copilot_orchestrator:execution' => [
                'tool_results' => ['search_results' => []],
            ],
            'copilot_orchestrator:response' => [
                'response' => 'Generated response based on tool results',
            ],
            'product_enrichment:retrieve' => [
                'products' => $context['input']['products'] ?? [],
                'count' => count($context['input']['products'] ?? []),
            ],
            'product_enrichment:analyze' => [
                'missing_fields' => ['description', 'meta_title'],
                'quality_score' => 0.6,
            ],
            'product_enrichment:propose' => [
                'hitl_required' => true,
                'hitl_message' => 'Please review proposed enrichments',
                'hitl_data' => [
                    'proposals' => [
                        'description' => 'AI-generated product description',
                        'meta_title' => 'AI-generated SEO title',
                    ],
                ],
            ],
            'product_enrichment:approve' => [
                'approved' => true,
            ],
            'product_enrichment:apply' => [
                'applied' => true,
                'updated_count' => 1,
            ],
            'data_import:validate' => [
                'valid' => true,
                'format' => $context['input']['format'] ?? 'csv',
            ],
            'data_import:parse' => [
                'rows' => 100,
                'columns' => ['name', 'sku', 'price'],
            ],
            'data_import:map' => [
                'hitl_required' => true,
                'hitl_message' => 'Please confirm field mapping',
                'hitl_data' => [
                    'suggested_mapping' => [
                        'name' => 'product_name',
                        'sku' => 'sku',
                        'price' => 'base_price',
                    ],
                ],
            ],
            'data_import:import' => [
                'imported' => 95,
                'failed' => 5,
            ],
            'data_import:report' => [
                'total' => 100,
                'successful' => 95,
                'failed' => 5,
                'errors' => [],
            ],
            default => ['status' => 'ok'],
        };
    }

    /**
     * Stream workflow events.
     */
    public function stream(string $runId): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        if (ob_get_level()) {
            ob_end_flush();
        }

        $eventsPath = "{$this->workflowsPath}/events/{$runId}.jsonl";
        $lastPosition = 0;
        $maxWait = 300; // 5 minutes max
        $waited = 0;

        while ($waited < $maxWait) {
            clearstatcache(true, $eventsPath);

            if (file_exists($eventsPath)) {
                $fp = fopen($eventsPath, 'r');
                fseek($fp, $lastPosition);

                while ($line = fgets($fp)) {
                    $event = json_decode(trim($line), true);
                    if ($event) {
                        echo "w:" . json_encode($event) . "\n";
                        flush();

                        // Check for terminal events
                        if (in_array($event['type'] ?? '', ['workflow_complete', 'workflow_error'])) {
                            fclose($fp);
                            return;
                        }
                    }
                }

                $lastPosition = ftell($fp);
                fclose($fp);
            }

            usleep(100000); // 100ms poll
            $waited += 0.1;
        }

        // Timeout
        echo "w:" . json_encode(['type' => 'timeout', 'message' => 'Stream timeout']) . "\n";
    }

    /**
     * Resume workflow with HITL feedback.
     */
    public function hitl(string $runId, array $feedback): array
    {
        $runState = $this->loadRunState($runId);

        if (!$runState) {
            throw new \RuntimeException("Workflow run not found: $runId");
        }

        if ($runState['status'] !== 'hitl_pending') {
            throw new \RuntimeException("Workflow is not waiting for HITL: {$runState['status']}");
        }

        // Record HITL feedback
        $this->writeEvent($runId, [
            'type' => 'hitl_response',
            'feedback' => $feedback,
        ]);

        // Update state
        $runState['hitlPending'] = false;
        $runState['hitlFeedback'] = $feedback;
        $runState['status'] = 'running';
        $runState['updatedAt'] = date('c');
        $this->saveRunState($runId, $runState);

        // Re-enqueue for continued processing
        $jobId = $this->queue->enqueue("workflow:{$runState['workflow']}", [
            'runId' => $runId,
            'input' => $runState['input'],
            'resume' => true,
            'resumeFrom' => $runState['currentStep'],
            'hitlFeedback' => $feedback,
        ]);

        return [
            'runId' => $runId,
            'status' => 'resumed',
            'jobId' => $jobId,
        ];
    }

    /**
     * Get workflow status.
     */
    public function status(string $runId): array
    {
        $runState = $this->loadRunState($runId);

        if (!$runState) {
            return ['status' => 'not_found', 'runId' => $runId];
        }

        return $runState;
    }

    /**
     * List available workflows.
     */
    public function listWorkflows(): array
    {
        $workflows = [];

        foreach (self::WORKFLOWS as $name => $config) {
            $workflows[] = [
                'name' => $name,
                'description' => $config['description'],
                'steps' => $config['steps'],
                'hasHitl' => $config['hitl'] ?? false,
            ];
        }

        return $workflows;
    }

    /**
     * Load run state from file.
     */
    private function loadRunState(string $runId): ?array
    {
        $path = "{$this->workflowsPath}/runs/{$runId}.json";

        if (!file_exists($path)) {
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Save run state to file.
     */
    private function saveRunState(string $runId, array $state): void
    {
        $path = "{$this->workflowsPath}/runs/{$runId}.json";
        $tmp = $path . '.tmp.' . getmypid();

        file_put_contents($tmp, json_encode($state, JSON_PRETTY_PRINT));
        rename($tmp, $path);
    }

    /**
     * Write workflow event.
     */
    private function writeEvent(string $runId, array $event): void
    {
        $eventsPath = "{$this->workflowsPath}/events/{$runId}.jsonl";
        $event['ts'] = date('c');

        $fp = fopen($eventsPath, 'a');
        flock($fp, LOCK_EX);
        fwrite($fp, json_encode($event) . "\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
