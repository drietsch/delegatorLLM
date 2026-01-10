<?php
/**
 * Neuron AI Backend Router
 *
 * Main API endpoints:
 * - GET  /api/agents                   - Returns agents.json
 * - GET  /api/health                   - Health check
 * - GET  /embeddings/{build_id}        - Fetch cached embedding bundle
 * - PUT  /embeddings/{build_id}        - Store embedding bundle
 * - POST /api/chat                     - Non-streaming chat
 * - POST /api/chat/stream              - Streaming chat (SSE)
 * - POST /api/workflows/run            - Start a workflow
 * - GET  /api/workflows/{id}/stream    - Stream workflow events
 * - POST /api/workflows/{id}/hitl      - Resume workflow with HITL feedback
 * - GET  /api/workflows/{id}/status    - Get workflow status
 * - POST /api/attachments              - Upload file attachment
 * - GET  /api/attachments/{id}         - Get attachment metadata
 * - POST /api/copilot/{tool}           - Direct tool execution
 * - GET  /api/sessions/{id}            - Get session data
 * - GET  /api/sessions/{id}/messages   - Get session messages
 * - GET  /api/queue/stats              - Queue statistics
 * - GET  /api/rag/stats                - RAG index statistics
 * - POST /api/rag/search               - RAG search
 */

// Bootstrap Neuron AI application
require_once __DIR__ . '/src/bootstrap.php';

use App\Api\ChatController;
use App\Api\WorkflowController;
use App\Api\AttachmentsController;
use App\Neuron\AgentFactory;
use App\Neuron\ToolRegistry;
use App\Persistence\SessionStore;
use App\Persistence\MessageStore;
use App\Queue\JobQueue;
use App\Rag\RagService;

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Session-Id');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Embeddings storage directory (legacy)
$embeddingsDir = __DIR__ . '/embeddings';
if (!is_dir($embeddingsDir)) {
    mkdir($embeddingsDir, 0755, true);
}

// ============================================================================
// AGENT ENDPOINTS
// ============================================================================

// GET /api/agents - Return agents.json
if ($method === 'GET' && $uri === '/api/agents') {
    header('Content-Type: application/json');
    $agentsFile = __DIR__ . '/../agents.json';
    if (file_exists($agentsFile)) {
        readfile($agentsFile);
    } else {
        echo json_encode(['agents' => []]);
    }
    exit;
}

// GET /api/health - Health check
if ($method === 'GET' && $uri === '/api/health') {
    header('Content-Type: application/json');

    $status = [
        'status' => 'ok',
        'service' => 'Neuron AI Backend',
        'version' => '1.0.0',
        'timestamp' => date('c'),
        'checks' => [
            'storage' => is_writable(STORAGE_PATH),
            'providers' => !empty(getenv('ANTHROPIC_API_KEY')) || !empty(getenv('OPENAI_API_KEY')),
        ],
    ];

    // Add RAG stats if available
    try {
        $rag = new RagService();
        $status['checks']['rag_collections'] = count($rag->getLoadedCollections());
    } catch (\Throwable $e) {
        $status['checks']['rag_collections'] = 0;
    }

    // Add queue stats
    try {
        $queue = new JobQueue();
        $queueStats = $queue->getStats();
        $status['checks']['queue_jobs'] = $queueStats['total'];
    } catch (\Throwable $e) {
        $status['checks']['queue_jobs'] = 0;
    }

    echo json_encode($status, JSON_PRETTY_PRINT);
    exit;
}

// GET /api/dictionary - Serve multilingual dictionary
if ($method === 'GET' && $uri === '/api/dictionary') {
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: public, max-age=86400');
    $dictFile = __DIR__ . '/dictionaries/multilingual_combined.txt';
    if (file_exists($dictFile)) {
        readfile($dictFile);
    } else {
        http_response_code(404);
        echo "Dictionary not found";
    }
    exit;
}

// ============================================================================
// EMBEDDINGS CACHE (Legacy)
// ============================================================================

// GET /embeddings/{build_id} - Fetch cached bundle
if ($method === 'GET' && preg_match('#^/embeddings/([a-f0-9]{64})$#', $uri, $m)) {
    $buildId = $m[1];
    $bundleFile = "$embeddingsDir/$buildId.json";

    if (file_exists($bundleFile)) {
        header('Content-Type: application/json');
        readfile($bundleFile);
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Bundle not found']);
    }
    exit;
}

// PUT /embeddings/{build_id} - Store bundle
if ($method === 'PUT' && preg_match('#^/embeddings/([a-f0-9]{64})$#', $uri, $m)) {
    $buildId = $m[1];
    $bundleFile = "$embeddingsDir/$buildId.json";

    $input = file_get_contents('php://input');
    $bundle = json_decode($input, true);

    if (!$bundle || !isset($bundle['build_id']) || $bundle['build_id'] !== $buildId) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid bundle or build_id mismatch']);
        exit;
    }

    if (!isset($bundle['dims']) || $bundle['dims'] !== 384) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid dims, expected 384']);
        exit;
    }

    file_put_contents($bundleFile, $input);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'build_id' => $buildId]);
    exit;
}

// ============================================================================
// CHAT ENDPOINTS
// ============================================================================

// POST /api/chat - Non-streaming chat
if ($method === 'POST' && $uri === '/api/chat') {
    $request = json_decode(file_get_contents('php://input'), true);

    if (!$request) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid JSON body']);
        exit;
    }

    try {
        $controller = new ChatController();
        $controller->handle($request);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// POST /api/chat/stream - Streaming chat (SSE)
if ($method === 'POST' && $uri === '/api/chat/stream') {
    $request = json_decode(file_get_contents('php://input'), true);

    if (!$request) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid JSON body']);
        exit;
    }

    try {
        $controller = new ChatController();
        $controller->stream($request);
    } catch (\Throwable $e) {
        // If streaming already started, emit error in stream format
        echo 'e:' . json_encode(['error' => $e->getMessage()]) . "\n";
    }
    exit;
}

// ============================================================================
// WORKFLOW ENDPOINTS
// ============================================================================

// POST /api/workflows/run - Start a workflow
if ($method === 'POST' && $uri === '/api/workflows/run') {
    $request = json_decode(file_get_contents('php://input'), true);

    if (!$request || !isset($request['name'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing workflow name']);
        exit;
    }

    try {
        $controller = new WorkflowController();
        $result = $controller->run($request);
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// GET /api/workflows/{runId}/stream - Stream workflow events
if ($method === 'GET' && preg_match('#^/api/workflows/([a-f0-9]{32})/stream$#', $uri, $m)) {
    $runId = $m[1];

    try {
        $controller = new WorkflowController();
        $controller->stream($runId);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// POST /api/workflows/{runId}/hitl - Resume workflow with HITL feedback
if ($method === 'POST' && preg_match('#^/api/workflows/([a-f0-9]{32})/hitl$#', $uri, $m)) {
    $runId = $m[1];
    $feedback = json_decode(file_get_contents('php://input'), true);

    if (!$feedback) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid feedback JSON']);
        exit;
    }

    try {
        $controller = new WorkflowController();
        $result = $controller->hitl($runId, $feedback);
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// GET /api/workflows/{runId}/status - Get workflow status
if ($method === 'GET' && preg_match('#^/api/workflows/([a-f0-9]{32})/status$#', $uri, $m)) {
    $runId = $m[1];

    try {
        $controller = new WorkflowController();
        $result = $controller->status($runId);
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// ATTACHMENT ENDPOINTS
// ============================================================================

// POST /api/attachments - Upload file
if ($method === 'POST' && $uri === '/api/attachments') {
    if (empty($_FILES['file'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No file uploaded']);
        exit;
    }

    try {
        $controller = new AttachmentsController();
        $result = $controller->upload($_FILES['file']);
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// GET /api/attachments/{id} - Get attachment metadata
if ($method === 'GET' && preg_match('#^/api/attachments/([a-f0-9]{32})$#', $uri, $m)) {
    $attachmentId = $m[1];

    try {
        $controller = new AttachmentsController();
        $result = $controller->get($attachmentId);

        if ($result === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Attachment not found']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// GET /api/attachments/{id}/download - Download attachment
if ($method === 'GET' && preg_match('#^/api/attachments/([a-f0-9]{32})/download$#', $uri, $m)) {
    $attachmentId = $m[1];

    try {
        $controller = new AttachmentsController();
        $controller->download($attachmentId);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// TOOL EXECUTION ENDPOINTS
// ============================================================================

// POST /api/copilot/{tool} - Direct tool execution
if ($method === 'POST' && preg_match('#^/api/copilot/([a-z_]+)$#', $uri, $m)) {
    $toolName = $m[1];
    $request = json_decode(file_get_contents('php://input'), true) ?? [];

    try {
        $registry = new ToolRegistry();
        $tool = $registry->getTool($toolName);

        if ($tool === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => "Tool not found: $toolName"]);
            exit;
        }

        $result = $registry->executeTool($toolName, $request);
        header('Content-Type: application/json');
        echo json_encode([
            'tool' => $toolName,
            'result' => json_decode($result, true) ?? $result,
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// SESSION ENDPOINTS
// ============================================================================

// GET /api/sessions/{id} - Get session data
if ($method === 'GET' && preg_match('#^/api/sessions/([a-f0-9]{32})$#', $uri, $m)) {
    $sessionId = $m[1];

    try {
        $store = new SessionStore();
        $session = $store->get($sessionId);

        if ($session === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Session not found']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($session);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// GET /api/sessions/{id}/messages - Get session messages
if ($method === 'GET' && preg_match('#^/api/sessions/([a-f0-9]{32})/messages$#', $uri, $m)) {
    $sessionId = $m[1];

    try {
        $store = new MessageStore();
        $messages = $store->getAll($sessionId);

        header('Content-Type: application/json');
        echo json_encode([
            'sessionId' => $sessionId,
            'count' => count($messages),
            'messages' => $messages,
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// POST /api/sessions - Create new session
if ($method === 'POST' && $uri === '/api/sessions') {
    $request = json_decode(file_get_contents('php://input'), true) ?? [];

    try {
        $store = new SessionStore();
        $sessionId = $store->create(
            $request['agentId'] ?? 'copilot',
            $request['context'] ?? []
        );

        header('Content-Type: application/json');
        http_response_code(201);
        echo json_encode([
            'sessionId' => $sessionId,
            'agentId' => $request['agentId'] ?? 'copilot',
            'createdAt' => date('c'),
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// QUEUE ENDPOINTS
// ============================================================================

// GET /api/queue/stats - Queue statistics
if ($method === 'GET' && $uri === '/api/queue/stats') {
    try {
        $queue = new JobQueue();
        $stats = $queue->getStats();

        header('Content-Type: application/json');
        echo json_encode($stats);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// GET /api/queue/jobs/{id} - Get job status
if ($method === 'GET' && preg_match('#^/api/queue/jobs/([a-f0-9]{32})$#', $uri, $m)) {
    $jobId = $m[1];

    try {
        $queue = new JobQueue();
        $job = $queue->getStatus($jobId);

        if ($job === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Job not found']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($job);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// POST /api/queue/cleanup - Cleanup old jobs
if ($method === 'POST' && $uri === '/api/queue/cleanup') {
    $request = json_decode(file_get_contents('php://input'), true) ?? [];
    $maxAge = $request['maxAge'] ?? 86400; // 24 hours default

    try {
        $queue = new JobQueue();
        $cleaned = $queue->cleanup($maxAge);

        header('Content-Type: application/json');
        echo json_encode([
            'cleaned' => $cleaned,
            'maxAge' => $maxAge,
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// RAG ENDPOINTS
// ============================================================================

// GET /api/rag/stats - RAG index statistics
if ($method === 'GET' && $uri === '/api/rag/stats') {
    try {
        $rag = new RagService();
        $stats = $rag->getStats();

        header('Content-Type: application/json');
        echo json_encode([
            'collections' => $rag->getCollections(),
            'loaded' => $rag->getLoadedCollections(),
            'stats' => $stats,
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// POST /api/rag/search - RAG search
if ($method === 'POST' && $uri === '/api/rag/search') {
    $request = json_decode(file_get_contents('php://input'), true);

    if (!$request || !isset($request['query'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing query']);
        exit;
    }

    try {
        $rag = new RagService();
        $results = $rag->retrieve(
            $request['query'],
            $request['collections'] ?? [],
            $request['topK'] ?? 5
        );

        header('Content-Type: application/json');
        echo json_encode([
            'query' => $request['query'],
            'results' => $results,
            'context' => $rag->formatContext($results),
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// POST /api/rag/build - Build RAG index (for specific collection)
if ($method === 'POST' && $uri === '/api/rag/build') {
    $request = json_decode(file_get_contents('php://input'), true);

    if (!$request || !isset($request['collection'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing collection name']);
        exit;
    }

    try {
        $collection = $request['collection'];
        $sourcePath = $request['sourcePath'] ?? BASE_PATH . "/fixtures/{$collection}.json";

        if (!file_exists($sourcePath)) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => "Source file not found: $sourcePath"]);
            exit;
        }

        $data = json_decode(file_get_contents($sourcePath), true) ?? [];

        $rag = new RagService();
        $rag->buildIndex($collection, $data);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'collection' => $collection,
            'documents' => count($data),
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// MCP ENDPOINTS
// ============================================================================

use App\MCP\McpRegistry;

// POST /api/mcp/{server} - MCP server endpoint
if ($method === 'POST' && preg_match('#^/api/mcp/([a-z]+)$#', $uri, $m)) {
    $serverName = $m[1];
    $request = json_decode(file_get_contents('php://input'), true);

    if (!$request) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32700, 'message' => 'Parse error']]);
        exit;
    }

    try {
        $registry = McpRegistry::getInstance();
        $server = $registry->getServer($serverName);

        if (!$server) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32601, 'message' => "Server not found: $serverName"]]);
            exit;
        }

        header('Content-Type: application/json');
        $response = $server->handleRequest($request);
        echo json_encode($response);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['jsonrpc' => '2.0', 'error' => ['code' => -32603, 'message' => $e->getMessage()]]);
    }
    exit;
}

// GET /api/mcp/tools - List all MCP tools
if ($method === 'GET' && $uri === '/api/mcp/tools') {
    try {
        $registry = McpRegistry::getInstance();
        $tools = $registry->getAllTools();

        header('Content-Type: application/json');
        echo json_encode([
            'tools' => array_map(fn($t) => [
                'name' => $t['name'],
                'description' => $t['description'],
                'source' => $t['source'],
                'inputSchema' => $t['inputSchema'],
            ], $tools),
        ], JSON_PRETTY_PRINT);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// POST /api/mcp/tools/call - Call an MCP tool
if ($method === 'POST' && $uri === '/api/mcp/tools/call') {
    $request = json_decode(file_get_contents('php://input'), true);

    if (!$request || !isset($request['name'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing tool name']);
        exit;
    }

    try {
        $registry = McpRegistry::getInstance();
        $result = $registry->callTool($request['name'], $request['arguments'] ?? []);

        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// GET /api/mcp/servers - List registered MCP servers
if ($method === 'GET' && $uri === '/api/mcp/servers') {
    try {
        $registry = McpRegistry::getInstance();
        $servers = $registry->getServers();

        $serverList = [];
        foreach ($servers as $name => $server) {
            $info = $server->handleRequest([
                'id' => 'info',
                'method' => 'initialize',
                'params' => [],
            ]);

            $serverList[] = [
                'name' => $name,
                'endpoint' => "/api/mcp/$name",
                'info' => $info['result'] ?? [],
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['servers' => $serverList], JSON_PRETTY_PRINT);
    } catch (\Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// 404 - Not Found
// ============================================================================

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not found', 'uri' => $uri, 'method' => $method]);
