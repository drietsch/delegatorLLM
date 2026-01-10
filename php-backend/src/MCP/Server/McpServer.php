<?php
declare(strict_types=1);

namespace App\MCP\Server;

/**
 * MCP Server Base Class
 *
 * Implements the Model Context Protocol (MCP) server specification.
 * Handles JSON-RPC 2.0 communication over HTTP or stdio.
 *
 * @see https://modelcontextprotocol.io/
 */
abstract class McpServer
{
    protected string $name;
    protected string $version = '1.0.0';
    protected array $tools = [];
    protected array $resources = [];
    protected array $prompts = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->registerTools();
    }

    /**
     * Register tools provided by this server.
     * Override in subclasses to define tools.
     */
    abstract protected function registerTools(): void;

    /**
     * Handle an incoming JSON-RPC request.
     */
    public function handleRequest(array $request): array
    {
        $method = $request['method'] ?? '';
        $params = $request['params'] ?? [];
        $id = $request['id'] ?? null;

        try {
            $result = match ($method) {
                'initialize' => $this->handleInitialize($params),
                'tools/list' => $this->handleToolsList(),
                'tools/call' => $this->handleToolCall($params),
                'resources/list' => $this->handleResourcesList(),
                'resources/read' => $this->handleResourceRead($params),
                'prompts/list' => $this->handlePromptsList(),
                'prompts/get' => $this->handlePromptGet($params),
                'ping' => ['pong' => true],
                default => throw new McpException("Unknown method: $method", -32601),
            };

            return $this->successResponse($id, $result);
        } catch (McpException $e) {
            return $this->errorResponse($id, $e->getCode(), $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResponse($id, -32603, $e->getMessage());
        }
    }

    /**
     * Handle initialize request.
     */
    protected function handleInitialize(array $params): array
    {
        return [
            'protocolVersion' => '2024-11-05',
            'serverInfo' => [
                'name' => $this->name,
                'version' => $this->version,
            ],
            'capabilities' => [
                'tools' => !empty($this->tools) ? ['listChanged' => false] : null,
                'resources' => !empty($this->resources) ? ['subscribe' => false, 'listChanged' => false] : null,
                'prompts' => !empty($this->prompts) ? ['listChanged' => false] : null,
            ],
        ];
    }

    /**
     * Handle tools/list request.
     */
    protected function handleToolsList(): array
    {
        $toolList = [];
        foreach ($this->tools as $name => $tool) {
            $toolList[] = [
                'name' => $name,
                'description' => $tool['description'] ?? '',
                'inputSchema' => $tool['inputSchema'] ?? [
                    'type' => 'object',
                    'properties' => [],
                ],
            ];
        }
        return ['tools' => $toolList];
    }

    /**
     * Handle tools/call request.
     */
    protected function handleToolCall(array $params): array
    {
        $toolName = $params['name'] ?? '';
        $arguments = $params['arguments'] ?? [];

        if (!isset($this->tools[$toolName])) {
            throw new McpException("Unknown tool: $toolName", -32602);
        }

        $tool = $this->tools[$toolName];
        $handler = $tool['handler'] ?? null;

        if (!is_callable($handler)) {
            throw new McpException("Tool handler not callable: $toolName", -32603);
        }

        $result = call_user_func($handler, $arguments);

        return [
            'content' => [
                [
                    'type' => 'text',
                    'text' => is_string($result) ? $result : json_encode($result),
                ],
            ],
        ];
    }

    /**
     * Handle resources/list request.
     */
    protected function handleResourcesList(): array
    {
        return ['resources' => array_values($this->resources)];
    }

    /**
     * Handle resources/read request.
     */
    protected function handleResourceRead(array $params): array
    {
        $uri = $params['uri'] ?? '';

        if (!isset($this->resources[$uri])) {
            throw new McpException("Unknown resource: $uri", -32602);
        }

        $resource = $this->resources[$uri];
        $handler = $resource['handler'] ?? null;

        if (!is_callable($handler)) {
            throw new McpException("Resource handler not callable: $uri", -32603);
        }

        $content = call_user_func($handler, $params);

        return [
            'contents' => [
                [
                    'uri' => $uri,
                    'mimeType' => $resource['mimeType'] ?? 'text/plain',
                    'text' => is_string($content) ? $content : json_encode($content),
                ],
            ],
        ];
    }

    /**
     * Handle prompts/list request.
     */
    protected function handlePromptsList(): array
    {
        return ['prompts' => array_values($this->prompts)];
    }

    /**
     * Handle prompts/get request.
     */
    protected function handlePromptGet(array $params): array
    {
        $name = $params['name'] ?? '';

        if (!isset($this->prompts[$name])) {
            throw new McpException("Unknown prompt: $name", -32602);
        }

        $prompt = $this->prompts[$name];
        $handler = $prompt['handler'] ?? null;

        $messages = is_callable($handler)
            ? call_user_func($handler, $params['arguments'] ?? [])
            : [['role' => 'user', 'content' => ['type' => 'text', 'text' => $prompt['template'] ?? '']]];

        return [
            'description' => $prompt['description'] ?? '',
            'messages' => $messages,
        ];
    }

    /**
     * Register a tool.
     */
    protected function registerTool(string $name, array $config): void
    {
        $this->tools[$name] = $config;
    }

    /**
     * Register a resource.
     */
    protected function registerResource(string $uri, array $config): void
    {
        $this->resources[$uri] = array_merge(['uri' => $uri], $config);
    }

    /**
     * Register a prompt.
     */
    protected function registerPrompt(string $name, array $config): void
    {
        $this->prompts[$name] = array_merge(['name' => $name], $config);
    }

    /**
     * Create a success response.
     */
    protected function successResponse(?string $id, $result): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ];
    }

    /**
     * Create an error response.
     */
    protected function errorResponse(?string $id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];
    }

    /**
     * Run server in HTTP mode.
     */
    public function runHttp(): void
    {
        header('Content-Type: application/json');

        $input = file_get_contents('php://input');
        $request = json_decode($input, true);

        if (!$request) {
            echo json_encode($this->errorResponse(null, -32700, 'Parse error'));
            return;
        }

        $response = $this->handleRequest($request);
        echo json_encode($response);
    }

    /**
     * Run server in stdio mode.
     */
    public function runStdio(): void
    {
        while ($line = fgets(STDIN)) {
            $request = json_decode(trim($line), true);

            if (!$request) {
                fwrite(STDOUT, json_encode($this->errorResponse(null, -32700, 'Parse error')) . "\n");
                continue;
            }

            $response = $this->handleRequest($request);
            fwrite(STDOUT, json_encode($response) . "\n");
        }
    }
}
