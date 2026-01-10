<?php
declare(strict_types=1);

namespace App\MCP\Tools;

use App\MCP\Server\McpServer;
use App\Tools\DataObjectTool;

/**
 * DataObject MCP Server
 *
 * Exposes data object management as an MCP server.
 * Provides CRUD operations for products, categories, and other entities.
 */
class DataObjectMcpServer extends McpServer
{
    public function __construct()
    {
        parent::__construct('pimcore-dataobjects');
    }

    protected function registerTools(): void
    {
        $this->registerTool('data_object_create', [
            'description' => 'Create a new data object (product, category, etc.)',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'object_type' => [
                        'type' => 'string',
                        'description' => 'Type of object: product, category, manufacturer',
                    ],
                    'data' => [
                        'type' => 'object',
                        'description' => 'Object data as key-value pairs',
                    ],
                    'parent_path' => [
                        'type' => 'string',
                        'description' => 'Parent folder path',
                    ],
                ],
                'required' => ['object_type', 'data'],
            ],
            'handler' => [$this, 'createObject'],
        ]);

        $this->registerTool('data_object_read', [
            'description' => 'Read a data object by ID or path',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'description' => 'Object ID',
                    ],
                    'path' => [
                        'type' => 'string',
                        'description' => 'Object path',
                    ],
                ],
            ],
            'handler' => [$this, 'readObject'],
        ]);

        $this->registerTool('data_object_update', [
            'description' => 'Update an existing data object',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'description' => 'Object ID to update',
                    ],
                    'data' => [
                        'type' => 'object',
                        'description' => 'Updated data fields',
                    ],
                ],
                'required' => ['id', 'data'],
            ],
            'handler' => [$this, 'updateObject'],
        ]);

        $this->registerTool('data_object_delete', [
            'description' => 'Delete a data object',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'description' => 'Object ID to delete',
                    ],
                ],
                'required' => ['id'],
            ],
            'handler' => [$this, 'deleteObject'],
        ]);

        $this->registerTool('data_object_list', [
            'description' => 'List data objects with optional filters',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'object_type' => [
                        'type' => 'string',
                        'description' => 'Type of objects to list',
                    ],
                    'filters' => [
                        'type' => 'object',
                        'description' => 'Filter criteria',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Maximum results',
                    ],
                    'offset' => [
                        'type' => 'integer',
                        'description' => 'Results offset for pagination',
                    ],
                ],
            ],
            'handler' => [$this, 'listObjects'],
        ]);

        // Register resources for browsing object types
        $this->registerResource('pimcore://objects/products', [
            'name' => 'Products',
            'description' => 'Browse product objects',
            'mimeType' => 'application/json',
            'handler' => fn($p) => $this->listObjects(['object_type' => 'product']),
        ]);

        $this->registerResource('pimcore://objects/categories', [
            'name' => 'Categories',
            'description' => 'Browse category objects',
            'mimeType' => 'application/json',
            'handler' => fn($p) => $this->listObjects(['object_type' => 'category']),
        ]);
    }

    public function createObject(array $args): string
    {
        return DataObjectTool::execute(
            'create',
            $args['object_type'] ?? 'product',
            null,
            json_encode($args['data'] ?? [])
        );
    }

    public function readObject(array $args): string
    {
        return DataObjectTool::execute(
            'read',
            null,
            $args['id'] ?? $args['path'] ?? null,
            null
        );
    }

    public function updateObject(array $args): string
    {
        return DataObjectTool::execute(
            'update',
            null,
            $args['id'] ?? null,
            json_encode($args['data'] ?? [])
        );
    }

    public function deleteObject(array $args): string
    {
        return DataObjectTool::execute(
            'delete',
            null,
            $args['id'] ?? null,
            null
        );
    }

    public function listObjects(array $args): string
    {
        return DataObjectTool::execute(
            'list',
            $args['object_type'] ?? 'product',
            null,
            json_encode([
                'filters' => $args['filters'] ?? [],
                'limit' => $args['limit'] ?? 20,
                'offset' => $args['offset'] ?? 0,
            ])
        );
    }
}
