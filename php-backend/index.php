<?php
/**
 * NeuronAI Mock Backend with Embeddings Cache
 *
 * GET  /api/agents              - Returns agents.json
 * GET  /embeddings/{build_id}   - Fetch cached embedding bundle
 * PUT  /embeddings/{build_id}   - Store embedding bundle
 * POST /api/chat                - Mock agent responses
 */

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Embeddings storage directory
$embeddingsDir = __DIR__ . '/embeddings';
if (!is_dir($embeddingsDir)) {
    mkdir($embeddingsDir, 0755, true);
}

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

// GET /api/health
if ($method === 'GET' && $uri === '/api/health') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'service' => 'NeuronAI PHP Mock']);
    exit;
}

// GET /api/dictionary - Serve multilingual dictionary
if ($method === 'GET' && $uri === '/api/dictionary') {
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: public, max-age=86400'); // Cache for 24h
    $dictFile = __DIR__ . '/dictionaries/multilingual_combined.txt';
    if (file_exists($dictFile)) {
        readfile($dictFile);
    } else {
        http_response_code(404);
        echo "Dictionary not found";
    }
    exit;
}

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

    // Validate bundle
    if (!$bundle || !isset($bundle['build_id']) || $bundle['build_id'] !== $buildId) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid bundle or build_id mismatch']);
        exit;
    }

    // Validate dims (384 for MiniLM)
    if (!isset($bundle['dims']) || $bundle['dims'] !== 384) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid dims, expected 384']);
        exit;
    }

    // Store bundle
    file_put_contents($bundleFile, $input);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'build_id' => $buildId]);
    exit;
}

// POST /api/chat - Mock agent execution
if ($method === 'POST' && $uri === '/api/chat') {
    $input = json_decode(file_get_contents('php://input'), true);
    $agentName = $input['agent'] ?? 'unknown';
    $messages = $input['messages'] ?? [];
    $query = end($messages)['content'] ?? '';

    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-cache');
    if (ob_get_level()) ob_end_flush();

    $toolCallId = 'call_' . time();

    // Tool call
    echo '9:' . json_encode([
        'toolCallId' => $toolCallId,
        'toolName' => $agentName,
        'args' => ['query' => $query],
    ]) . "\n";
    flush();
    usleep(200000);

    // Tool result
    echo 'a:' . json_encode([
        'toolCallId' => $toolCallId,
        'result' => ['status' => 'success', 'data' => "Mock result from $agentName"],
    ]) . "\n";
    flush();
    usleep(200000);

    // Stream text response
    $response = "This is a mock response from the **$agentName** agent for query: \"$query\"";
    foreach (explode(' ', $response) as $word) {
        echo '0:' . json_encode($word . ' ') . "\n";
        flush();
        usleep(30000);
    }

    echo 'd:' . json_encode(['finishReason' => 'stop']) . "\n";
    exit;
}

// 404
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not found']);
