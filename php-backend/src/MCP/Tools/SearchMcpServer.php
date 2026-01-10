<?php
declare(strict_types=1);

namespace App\MCP\Tools;

use App\MCP\Server\McpServer;
use App\Tools\SearchTool;

/**
 * Search MCP Server
 *
 * Exposes search functionality as an MCP server.
 * Provides tools for searching products, assets, documents, and customers.
 */
class SearchMcpServer extends McpServer
{
    public function __construct()
    {
        parent::__construct('pimcore-search');
    }

    protected function registerTools(): void
    {
        $this->registerTool('advanced_search', [
            'description' => 'Search for products, assets, documents, or customers by query and filters',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Search query string (e.g., "laptops", "blue shoes")',
                    ],
                    'object_type' => [
                        'type' => 'string',
                        'description' => 'Type to search: product, asset, document, customer',
                        'enum' => ['product', 'asset', 'document', 'customer'],
                    ],
                    'filters' => [
                        'type' => 'string',
                        'description' => 'JSON string of attribute filters',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Maximum results to return (default: 10)',
                    ],
                ],
                'required' => ['query'],
            ],
            'handler' => [$this, 'executeSearch'],
        ]);

        $this->registerTool('search_products', [
            'description' => 'Search for products by keywords',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Product search keywords',
                    ],
                    'category' => [
                        'type' => 'string',
                        'description' => 'Filter by category',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Maximum results (default: 10)',
                    ],
                ],
                'required' => ['query'],
            ],
            'handler' => [$this, 'executeProductSearch'],
        ]);

        $this->registerTool('search_assets', [
            'description' => 'Search for digital assets (images, documents, videos)',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Asset search keywords',
                    ],
                    'type' => [
                        'type' => 'string',
                        'description' => 'Asset type: image, document, video',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Maximum results (default: 10)',
                    ],
                ],
                'required' => ['query'],
            ],
            'handler' => [$this, 'executeAssetSearch'],
        ]);

        // Register resources for browsing collections
        $this->registerResource('pimcore://products', [
            'name' => 'Products Collection',
            'description' => 'Browse all products',
            'mimeType' => 'application/json',
            'handler' => [$this, 'browseProducts'],
        ]);

        $this->registerResource('pimcore://assets', [
            'name' => 'Assets Collection',
            'description' => 'Browse all digital assets',
            'mimeType' => 'application/json',
            'handler' => [$this, 'browseAssets'],
        ]);
    }

    /**
     * Execute advanced search.
     */
    public function executeSearch(array $args): string
    {
        return SearchTool::execute(
            $args['query'] ?? '',
            $args['object_type'] ?? 'product',
            $args['filters'] ?? null,
            $args['limit'] ?? 10
        );
    }

    /**
     * Execute product-specific search.
     */
    public function executeProductSearch(array $args): string
    {
        $filters = null;
        if (!empty($args['category'])) {
            $filters = json_encode(['category' => $args['category']]);
        }

        return SearchTool::execute(
            $args['query'] ?? '',
            'product',
            $filters,
            $args['limit'] ?? 10
        );
    }

    /**
     * Execute asset-specific search.
     */
    public function executeAssetSearch(array $args): string
    {
        $filters = null;
        if (!empty($args['type'])) {
            $filters = json_encode(['type' => $args['type']]);
        }

        return SearchTool::execute(
            $args['query'] ?? '',
            'asset',
            $filters,
            $args['limit'] ?? 10
        );
    }

    /**
     * Browse products collection.
     */
    public function browseProducts(array $params): string
    {
        return SearchTool::execute('', 'product', null, 50);
    }

    /**
     * Browse assets collection.
     */
    public function browseAssets(array $params): string
    {
        return SearchTool::execute('', 'asset', null, 50);
    }
}
