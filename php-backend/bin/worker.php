#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Background Worker
 *
 * Polls the job queue and processes jobs asynchronously.
 * Run with: php bin/worker.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/bootstrap.php';

use App\Queue\JobQueue;
use App\Neuron\AgentFactory;
use NeuronAI\Chat\Messages\UserMessage;

echo "=== Neuron AI Background Worker ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n";
echo "Storage path: " . STORAGE_PATH . "\n\n";

$queue = new JobQueue();
$running = true;
$pollInterval = 1; // seconds
$processedCount = 0;

// Handle graceful shutdown
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, function () use (&$running) {
        echo "\nReceived SIGTERM, shutting down gracefully...\n";
        $running = false;
    });

    pcntl_signal(SIGINT, function () use (&$running) {
        echo "\nReceived SIGINT, shutting down gracefully...\n";
        $running = false;
    });
}

echo "Polling for jobs (press Ctrl+C to stop)...\n\n";

while ($running) {
    // Dispatch signals if available
    if (function_exists('pcntl_signal_dispatch')) {
        pcntl_signal_dispatch();
    }

    // Try to get a job
    $job = $queue->dequeue();

    if ($job === null) {
        sleep($pollInterval);
        continue;
    }

    $jobId = $job['id'];
    $jobType = $job['type'];

    echo "[" . date('H:i:s') . "] Processing job {$jobId}: {$jobType}\n";

    try {
        $result = processJob($job);
        $queue->complete($jobId, $result);
        $processedCount++;

        echo "[" . date('H:i:s') . "] Job {$jobId} completed successfully\n";
    } catch (\Throwable $e) {
        echo "[" . date('H:i:s') . "] Job {$jobId} failed: " . $e->getMessage() . "\n";
        $queue->fail($jobId, $e->getMessage());
    }

    echo "\n";
}

echo "\nWorker stopped. Processed {$processedCount} jobs.\n";

/**
 * Process a job based on its type.
 */
function processJob(array $job): array
{
    $type = $job['type'];
    $payload = $job['payload'] ?? [];

    return match (true) {
        str_starts_with($type, 'workflow:') => processWorkflowJob($type, $payload),
        str_starts_with($type, 'agent:') => processAgentJob($type, $payload),
        $type === 'rag:build_index' => processBuildIndexJob($payload),
        default => throw new \InvalidArgumentException("Unknown job type: $type"),
    };
}

/**
 * Process a workflow job.
 */
function processWorkflowJob(string $type, array $payload): array
{
    $workflowName = str_replace('workflow:', '', $type);
    $runId = $payload['runId'] ?? bin2hex(random_bytes(16));
    $input = $payload['input'] ?? [];

    // Write initial event
    writeWorkflowEvent($runId, [
        'type' => 'workflow_start',
        'workflow' => $workflowName,
        'input' => $input,
    ]);

    // For now, just simulate workflow execution
    // Real implementation would instantiate and run the actual workflow
    writeWorkflowEvent($runId, [
        'type' => 'progress',
        'progress' => 50,
    ]);

    sleep(1); // Simulate work

    writeWorkflowEvent($runId, [
        'type' => 'workflow_complete',
        'progress' => 100,
    ]);

    return [
        'status' => 'completed',
        'runId' => $runId,
        'workflow' => $workflowName,
    ];
}

/**
 * Process an agent job.
 */
function processAgentJob(string $type, array $payload): array
{
    $agentId = str_replace('agent:', '', $type);
    $message = $payload['message'] ?? '';
    $sessionId = $payload['sessionId'] ?? null;

    $agent = AgentFactory::create($agentId);
    $response = $agent->chat(new UserMessage($message));

    return [
        'status' => 'completed',
        'agent' => $agentId,
        'response' => $response->getContent(),
        'sessionId' => $sessionId,
    ];
}

/**
 * Process a RAG index build job.
 */
function processBuildIndexJob(array $payload): array
{
    $collection = $payload['collection'] ?? 'products';
    $sourcePath = $payload['sourcePath'] ?? BASE_PATH . "/fixtures/{$collection}.json";

    if (!file_exists($sourcePath)) {
        throw new \RuntimeException("Source file not found: $sourcePath");
    }

    $data = json_decode(file_get_contents($sourcePath), true) ?? [];

    $rag = new \App\Rag\RagService();
    $rag->buildIndex($collection, $data);

    return [
        'status' => 'completed',
        'collection' => $collection,
        'documents' => count($data),
    ];
}

/**
 * Write a workflow event to the events log.
 */
function writeWorkflowEvent(string $runId, array $event): void
{
    $eventsPath = STORAGE_PATH . "/workflows/events";

    if (!is_dir($eventsPath)) {
        mkdir($eventsPath, 0755, true);
    }

    $event['ts'] = date('c');
    $line = json_encode($event) . "\n";

    file_put_contents(
        "$eventsPath/{$runId}.jsonl",
        $line,
        FILE_APPEND | LOCK_EX
    );
}
