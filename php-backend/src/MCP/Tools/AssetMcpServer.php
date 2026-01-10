<?php
declare(strict_types=1);

namespace App\MCP\Tools;

use App\MCP\Server\McpServer;
use App\Tools\AssetTool;

/**
 * Asset MCP Server
 *
 * Exposes digital asset management as an MCP server.
 * Provides tools for managing images, documents, videos, and other files.
 */
class AssetMcpServer extends McpServer
{
    public function __construct()
    {
        parent::__construct('pimcore-assets');
    }

    protected function registerTools(): void
    {
        $this->registerTool('asset_upload', [
            'description' => 'Upload a new asset (image, document, video)',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'filename' => [
                        'type' => 'string',
                        'description' => 'Filename for the asset',
                    ],
                    'path' => [
                        'type' => 'string',
                        'description' => 'Target folder path',
                    ],
                    'data' => [
                        'type' => 'string',
                        'description' => 'Base64 encoded file data',
                    ],
                    'metadata' => [
                        'type' => 'object',
                        'description' => 'Asset metadata (title, description, tags)',
                    ],
                ],
                'required' => ['filename', 'data'],
            ],
            'handler' => [$this, 'uploadAsset'],
        ]);

        $this->registerTool('asset_download', [
            'description' => 'Download an asset by ID or path',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'description' => 'Asset ID',
                    ],
                    'path' => [
                        'type' => 'string',
                        'description' => 'Asset path',
                    ],
                ],
            ],
            'handler' => [$this, 'downloadAsset'],
        ]);

        $this->registerTool('asset_list', [
            'description' => 'List assets in a folder',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'path' => [
                        'type' => 'string',
                        'description' => 'Folder path to list',
                    ],
                    'type' => [
                        'type' => 'string',
                        'description' => 'Filter by type: image, document, video',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Maximum results',
                    ],
                ],
            ],
            'handler' => [$this, 'listAssets'],
        ]);

        $this->registerTool('asset_delete', [
            'description' => 'Delete an asset',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'description' => 'Asset ID to delete',
                    ],
                ],
                'required' => ['id'],
            ],
            'handler' => [$this, 'deleteAsset'],
        ]);

        $this->registerTool('asset_move', [
            'description' => 'Move an asset to a new location',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'description' => 'Asset ID to move',
                    ],
                    'target_path' => [
                        'type' => 'string',
                        'description' => 'Target folder path',
                    ],
                ],
                'required' => ['id', 'target_path'],
            ],
            'handler' => [$this, 'moveAsset'],
        ]);

        $this->registerTool('asset_metadata', [
            'description' => 'Get or update asset metadata',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'description' => 'Asset ID',
                    ],
                    'metadata' => [
                        'type' => 'object',
                        'description' => 'Metadata to update (optional)',
                    ],
                ],
                'required' => ['id'],
            ],
            'handler' => [$this, 'assetMetadata'],
        ]);

        // Register resources for browsing asset folders
        $this->registerResource('pimcore://assets/images', [
            'name' => 'Images',
            'description' => 'Browse image assets',
            'mimeType' => 'application/json',
            'handler' => fn($p) => $this->listAssets(['type' => 'image']),
        ]);

        $this->registerResource('pimcore://assets/documents', [
            'name' => 'Documents',
            'description' => 'Browse document assets',
            'mimeType' => 'application/json',
            'handler' => fn($p) => $this->listAssets(['type' => 'document']),
        ]);

        $this->registerResource('pimcore://assets/videos', [
            'name' => 'Videos',
            'description' => 'Browse video assets',
            'mimeType' => 'application/json',
            'handler' => fn($p) => $this->listAssets(['type' => 'video']),
        ]);
    }

    public function uploadAsset(array $args): string
    {
        return AssetTool::execute(
            'upload',
            $args['path'] ?? '/',
            $args['filename'] ?? 'untitled',
            json_encode([
                'data' => $args['data'] ?? '',
                'metadata' => $args['metadata'] ?? [],
            ])
        );
    }

    public function downloadAsset(array $args): string
    {
        return AssetTool::execute(
            'download',
            $args['path'] ?? null,
            null,
            $args['id'] ?? null
        );
    }

    public function listAssets(array $args): string
    {
        return AssetTool::execute(
            'list',
            $args['path'] ?? '/',
            null,
            json_encode([
                'type' => $args['type'] ?? null,
                'limit' => $args['limit'] ?? 20,
            ])
        );
    }

    public function deleteAsset(array $args): string
    {
        return AssetTool::execute(
            'delete',
            null,
            null,
            $args['id'] ?? null
        );
    }

    public function moveAsset(array $args): string
    {
        return AssetTool::execute(
            'move',
            $args['target_path'] ?? '/',
            null,
            $args['id'] ?? null
        );
    }

    public function assetMetadata(array $args): string
    {
        $action = isset($args['metadata']) ? 'update_metadata' : 'get_metadata';
        return AssetTool::execute(
            $action,
            null,
            null,
            json_encode([
                'id' => $args['id'] ?? null,
                'metadata' => $args['metadata'] ?? null,
            ])
        );
    }
}
