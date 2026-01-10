<?php
declare(strict_types=1);

namespace App\MCP;

use App\MCP\Server\McpServer;
use App\MCP\Client\McpClient;
use App\MCP\Tools\SearchMcpServer;
use App\MCP\Tools\DataObjectMcpServer;
use App\MCP\Tools\AssetMcpServer;

/**
 * MCP Registry
 *
 * Central registry for MCP servers and clients.
 * Manages local servers and connections to external MCP servers.
 */
class McpRegistry
{
    private static ?self $instance = null;

    /** @var array<string, McpServer> Local MCP servers */
    private array $servers = [];

    /** @var array<string, McpClient> Connected external clients */
    private array $clients = [];

    /** @var array<string, array> All available tools from all sources */
    private array $allTools = [];

    private function __construct()
    {
        $this->registerBuiltInServers();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register built-in MCP servers.
     */
    private function registerBuiltInServers(): void
    {
        $this->registerServer('search', new SearchMcpServer());
        $this->registerServer('dataobjects', new DataObjectMcpServer());
        $this->registerServer('assets', new AssetMcpServer());
    }

    /**
     * Register a local MCP server.
     */
    public function registerServer(string $name, McpServer $server): void
    {
        $this->servers[$name] = $server;
        $this->refreshToolsFromServer($name, $server);
    }

    /**
     * Connect to an external MCP server.
     */
    public function connectClient(string $name, string $url): McpClient
    {
        $client = new McpClient($url);
        $client->initialize();

        $this->clients[$name] = $client;
        $this->refreshToolsFromClient($name, $client);

        return $client;
    }

    /**
     * Disconnect an external MCP client.
     */
    public function disconnectClient(string $name): void
    {
        if (isset($this->clients[$name])) {
            $this->clients[$name]->close();
            unset($this->clients[$name]);

            // Remove tools from this client
            $this->allTools = array_filter(
                $this->allTools,
                fn($tool) => $tool['source'] !== "client:$name"
            );
        }
    }

    /**
     * Refresh tools from a local server.
     */
    private function refreshToolsFromServer(string $name, McpServer $server): void
    {
        $response = $server->handleRequest([
            'id' => 'refresh',
            'method' => 'tools/list',
            'params' => [],
        ]);

        $tools = $response['result']['tools'] ?? [];

        foreach ($tools as $tool) {
            $toolName = $tool['name'];
            $this->allTools[$toolName] = [
                'name' => $toolName,
                'description' => $tool['description'] ?? '',
                'inputSchema' => $tool['inputSchema'] ?? [],
                'source' => "server:$name",
                'server' => $server,
            ];
        }
    }

    /**
     * Refresh tools from an external client.
     */
    private function refreshToolsFromClient(string $name, McpClient $client): void
    {
        $tools = $client->getTools();

        foreach ($tools as $tool) {
            $toolName = $tool['name'];
            $this->allTools[$toolName] = [
                'name' => $toolName,
                'description' => $tool['description'] ?? '',
                'inputSchema' => $tool['inputSchema'] ?? [],
                'source' => "client:$name",
                'client' => $client,
            ];
        }
    }

    /**
     * Get all available tools.
     */
    public function getAllTools(): array
    {
        return array_values($this->allTools);
    }

    /**
     * Get a specific tool by name.
     */
    public function getTool(string $name): ?array
    {
        return $this->allTools[$name] ?? null;
    }

    /**
     * Call a tool by name.
     */
    public function callTool(string $name, array $arguments = []): array
    {
        $tool = $this->getTool($name);

        if (!$tool) {
            throw new \RuntimeException("Tool not found: $name");
        }

        if (isset($tool['server'])) {
            // Local server
            $response = $tool['server']->handleRequest([
                'id' => uniqid('call_'),
                'method' => 'tools/call',
                'params' => [
                    'name' => $name,
                    'arguments' => $arguments,
                ],
            ]);

            return $response['result'] ?? [];
        }

        if (isset($tool['client'])) {
            // External client
            return $tool['client']->callTool($name, $arguments);
        }

        throw new \RuntimeException("Tool has no handler: $name");
    }

    /**
     * Get all registered servers.
     */
    public function getServers(): array
    {
        return $this->servers;
    }

    /**
     * Get a specific server.
     */
    public function getServer(string $name): ?McpServer
    {
        return $this->servers[$name] ?? null;
    }

    /**
     * Get all connected clients.
     */
    public function getClients(): array
    {
        return $this->clients;
    }

    /**
     * Get a specific client.
     */
    public function getClient(string $name): ?McpClient
    {
        return $this->clients[$name] ?? null;
    }

    /**
     * Reset the registry (for testing).
     */
    public static function reset(): void
    {
        if (self::$instance) {
            foreach (self::$instance->clients as $client) {
                $client->close();
            }
        }
        self::$instance = null;
    }
}
