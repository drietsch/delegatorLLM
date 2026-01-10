<?php
declare(strict_types=1);

namespace App\MCP\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * MCP Client
 *
 * Client for connecting to MCP servers via HTTP or stdio.
 * Implements the Model Context Protocol (MCP) client specification.
 */
class McpClient
{
    private string $serverUrl;
    private ?Client $httpClient = null;
    private ?resource $stdinHandle = null;
    private ?resource $stdoutHandle = null;
    private array $serverInfo = [];
    private array $capabilities = [];
    private array $tools = [];
    private array $resources = [];
    private array $prompts = [];

    public function __construct(string $serverUrl)
    {
        $this->serverUrl = $serverUrl;

        if (str_starts_with($serverUrl, 'http')) {
            $this->httpClient = new Client([
                'base_uri' => $serverUrl,
                'timeout' => 30,
            ]);
        }
    }

    /**
     * Connect to stdio-based server.
     */
    public static function fromStdio(string $command): self
    {
        $client = new self('stdio://' . $command);

        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException("Failed to start MCP server: $command");
        }

        $client->stdinHandle = $pipes[0];
        $client->stdoutHandle = $pipes[1];

        return $client;
    }

    /**
     * Initialize connection to the server.
     */
    public function initialize(): array
    {
        $response = $this->request('initialize', [
            'protocolVersion' => '2024-11-05',
            'clientInfo' => [
                'name' => 'pimcore-mcp-client',
                'version' => '1.0.0',
            ],
            'capabilities' => [
                'roots' => ['listChanged' => false],
                'sampling' => [],
            ],
        ]);

        $this->serverInfo = $response['serverInfo'] ?? [];
        $this->capabilities = $response['capabilities'] ?? [];

        // Notify initialized
        $this->notify('notifications/initialized', []);

        // Load available tools, resources, prompts
        $this->loadTools();
        $this->loadResources();
        $this->loadPrompts();

        return $response;
    }

    /**
     * Load available tools from server.
     */
    public function loadTools(): void
    {
        if (!($this->capabilities['tools'] ?? false)) {
            return;
        }

        $response = $this->request('tools/list', []);
        $this->tools = $response['tools'] ?? [];
    }

    /**
     * Load available resources from server.
     */
    public function loadResources(): void
    {
        if (!($this->capabilities['resources'] ?? false)) {
            return;
        }

        $response = $this->request('resources/list', []);
        $this->resources = $response['resources'] ?? [];
    }

    /**
     * Load available prompts from server.
     */
    public function loadPrompts(): void
    {
        if (!($this->capabilities['prompts'] ?? false)) {
            return;
        }

        $response = $this->request('prompts/list', []);
        $this->prompts = $response['prompts'] ?? [];
    }

    /**
     * Get available tools.
     */
    public function getTools(): array
    {
        return $this->tools;
    }

    /**
     * Get available resources.
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Get available prompts.
     */
    public function getPrompts(): array
    {
        return $this->prompts;
    }

    /**
     * Call a tool.
     */
    public function callTool(string $name, array $arguments = []): array
    {
        return $this->request('tools/call', [
            'name' => $name,
            'arguments' => $arguments,
        ]);
    }

    /**
     * Read a resource.
     */
    public function readResource(string $uri): array
    {
        return $this->request('resources/read', [
            'uri' => $uri,
        ]);
    }

    /**
     * Get a prompt.
     */
    public function getPrompt(string $name, array $arguments = []): array
    {
        return $this->request('prompts/get', [
            'name' => $name,
            'arguments' => $arguments,
        ]);
    }

    /**
     * Ping the server.
     */
    public function ping(): bool
    {
        try {
            $response = $this->request('ping', []);
            return ($response['pong'] ?? false) === true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Send a request to the server.
     */
    public function request(string $method, array $params = []): array
    {
        $id = uniqid('req_', true);

        $request = [
            'jsonrpc' => '2.0',
            'id' => $id,
            'method' => $method,
            'params' => $params,
        ];

        $response = $this->sendRequest($request);

        if (isset($response['error'])) {
            throw new McpClientException(
                $response['error']['message'] ?? 'Unknown error',
                $response['error']['code'] ?? -32603
            );
        }

        return $response['result'] ?? [];
    }

    /**
     * Send a notification (no response expected).
     */
    public function notify(string $method, array $params = []): void
    {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
        ];

        $this->sendRequest($request, false);
    }

    /**
     * Send request via HTTP or stdio.
     */
    private function sendRequest(array $request, bool $expectResponse = true): array
    {
        if ($this->httpClient) {
            return $this->sendHttpRequest($request);
        }

        if ($this->stdinHandle && $this->stdoutHandle) {
            return $this->sendStdioRequest($request, $expectResponse);
        }

        throw new \RuntimeException('No connection to MCP server');
    }

    /**
     * Send request via HTTP.
     */
    private function sendHttpRequest(array $request): array
    {
        try {
            $response = $this->httpClient->post('', [
                'json' => $request,
            ]);

            return json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (GuzzleException $e) {
            throw new McpClientException('HTTP request failed: ' . $e->getMessage(), -32603);
        }
    }

    /**
     * Send request via stdio.
     */
    private function sendStdioRequest(array $request, bool $expectResponse = true): array
    {
        $json = json_encode($request) . "\n";
        fwrite($this->stdinHandle, $json);
        fflush($this->stdinHandle);

        if (!$expectResponse) {
            return [];
        }

        $response = fgets($this->stdoutHandle);
        if ($response === false) {
            throw new McpClientException('Failed to read response from server', -32603);
        }

        return json_decode(trim($response), true) ?? [];
    }

    /**
     * Close the connection.
     */
    public function close(): void
    {
        if ($this->stdinHandle) {
            fclose($this->stdinHandle);
            $this->stdinHandle = null;
        }

        if ($this->stdoutHandle) {
            fclose($this->stdoutHandle);
            $this->stdoutHandle = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
