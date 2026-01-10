<?php
declare(strict_types=1);

namespace App\Tools;

/**
 * Asset Tool
 *
 * Digital asset management operations.
 * Handles file uploads, metadata, and asset organization.
 */
class AssetTool
{
    /**
     * Execute an asset operation.
     *
     * @param string      $action   Action: upload, download, list, delete, move, search
     * @param string|null $asset_id Asset ID (for download, delete, move)
     * @param string|null $path     Asset path or destination
     * @param string|null $metadata JSON metadata (for upload)
     *
     * @return string JSON encoded result
     */
    public static function execute(
        string $action,
        ?string $asset_id = null,
        ?string $path = null,
        ?string $metadata = null
    ): string {
        return match (strtolower($action)) {
            'upload' => self::upload($path, $metadata),
            'download', 'get' => self::download($asset_id),
            'list' => self::list($path),
            'delete' => self::delete($asset_id),
            'move' => self::move($asset_id, $path),
            'search' => self::search($path ?? ''),
            'update_metadata' => self::updateMetadata($asset_id, $metadata),
            default => json_encode(['error' => "Unknown action: $action"]),
        };
    }

    /**
     * Upload a new asset (mock - creates metadata entry).
     */
    private static function upload(?string $path, ?string $metadata): string
    {
        if (!$path) {
            return json_encode(['error' => 'No path provided for upload']);
        }

        $assetId = 'asset_' . bin2hex(random_bytes(8));
        $metaArray = $metadata ? (json_decode($metadata, true) ?? []) : [];

        $asset = [
            'id' => $assetId,
            'filename' => basename($path),
            'path' => dirname($path) ?: '/',
            'type' => self::getAssetType($path),
            'mimeType' => self::getMimeType($path),
            'size' => $metaArray['size'] ?? 0,
            'metadata' => $metaArray,
            'createdAt' => date('c'),
            'updatedAt' => date('c'),
        ];

        // Save metadata
        self::saveAssetMetadata($assetId, $asset);

        // Add to assets collection
        $assets = self::loadAssetsCollection();
        $assets[] = $asset;
        self::saveAssetsCollection($assets);

        return json_encode([
            'success' => true,
            'action' => 'upload',
            'asset' => $asset,
        ]);
    }

    /**
     * Download/get an asset.
     */
    private static function download(?string $assetId): string
    {
        if (!$assetId) {
            return json_encode(['error' => 'No asset_id provided']);
        }

        $asset = self::getAssetById($assetId);

        if (!$asset) {
            return json_encode([
                'error' => 'Asset not found',
                'asset_id' => $assetId,
            ]);
        }

        return json_encode([
            'success' => true,
            'action' => 'download',
            'asset' => $asset,
            'download_url' => "/api/assets/{$assetId}/download", // Mock URL
        ]);
    }

    /**
     * List assets in a path.
     */
    private static function list(?string $path): string
    {
        $assets = self::loadAssetsCollection();

        if ($path) {
            $assets = array_filter($assets, function ($asset) use ($path) {
                $assetPath = $asset['path'] ?? '/';
                return str_starts_with($assetPath, $path);
            });
        }

        return json_encode([
            'success' => true,
            'action' => 'list',
            'path' => $path ?? '/',
            'total' => count($assets),
            'assets' => array_values($assets),
        ]);
    }

    /**
     * Delete an asset.
     */
    private static function delete(?string $assetId): string
    {
        if (!$assetId) {
            return json_encode(['error' => 'No asset_id provided']);
        }

        $assets = self::loadAssetsCollection();
        $found = false;
        $deletedAsset = null;

        foreach ($assets as $index => $asset) {
            if (($asset['id'] ?? null) === $assetId) {
                $deletedAsset = $asset;
                unset($assets[$index]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return json_encode([
                'error' => 'Asset not found',
                'asset_id' => $assetId,
            ]);
        }

        self::saveAssetsCollection(array_values($assets));

        // Delete metadata file
        $metaPath = STORAGE_PATH . "/attachments/meta/{$assetId}.json";
        if (file_exists($metaPath)) {
            unlink($metaPath);
        }

        return json_encode([
            'success' => true,
            'action' => 'delete',
            'asset_id' => $assetId,
            'deleted_asset' => $deletedAsset,
        ]);
    }

    /**
     * Move an asset to a new path.
     */
    private static function move(?string $assetId, ?string $newPath): string
    {
        if (!$assetId) {
            return json_encode(['error' => 'No asset_id provided']);
        }

        if (!$newPath) {
            return json_encode(['error' => 'No destination path provided']);
        }

        $assets = self::loadAssetsCollection();
        $found = false;
        $movedAsset = null;

        foreach ($assets as $index => $asset) {
            if (($asset['id'] ?? null) === $assetId) {
                $assets[$index]['path'] = $newPath;
                $assets[$index]['updatedAt'] = date('c');
                $movedAsset = $assets[$index];
                $found = true;
                break;
            }
        }

        if (!$found) {
            return json_encode([
                'error' => 'Asset not found',
                'asset_id' => $assetId,
            ]);
        }

        self::saveAssetsCollection($assets);

        return json_encode([
            'success' => true,
            'action' => 'move',
            'asset' => $movedAsset,
        ]);
    }

    /**
     * Search assets by query.
     */
    private static function search(string $query): string
    {
        $assets = self::loadAssetsCollection();
        $query = strtolower($query);

        $results = array_filter($assets, function ($asset) use ($query) {
            $searchText = strtolower(json_encode($asset));
            return str_contains($searchText, $query);
        });

        return json_encode([
            'success' => true,
            'action' => 'search',
            'query' => $query,
            'total' => count($results),
            'assets' => array_values($results),
        ]);
    }

    /**
     * Update asset metadata.
     */
    private static function updateMetadata(?string $assetId, ?string $metadata): string
    {
        if (!$assetId) {
            return json_encode(['error' => 'No asset_id provided']);
        }

        if (!$metadata) {
            return json_encode(['error' => 'No metadata provided']);
        }

        $metaArray = json_decode($metadata, true);
        if (!$metaArray) {
            return json_encode(['error' => 'Invalid JSON metadata']);
        }

        $assets = self::loadAssetsCollection();
        $found = false;
        $updatedAsset = null;

        foreach ($assets as $index => $asset) {
            if (($asset['id'] ?? null) === $assetId) {
                $assets[$index]['metadata'] = array_merge(
                    $asset['metadata'] ?? [],
                    $metaArray
                );
                $assets[$index]['updatedAt'] = date('c');
                $updatedAsset = $assets[$index];
                $found = true;
                break;
            }
        }

        if (!$found) {
            return json_encode([
                'error' => 'Asset not found',
                'asset_id' => $assetId,
            ]);
        }

        self::saveAssetsCollection($assets);

        return json_encode([
            'success' => true,
            'action' => 'update_metadata',
            'asset' => $updatedAsset,
        ]);
    }

    /**
     * Load assets collection.
     */
    private static function loadAssetsCollection(): array
    {
        // Try storage first
        $storagePath = STORAGE_PATH . "/pimcore_shadow/assets.json";
        if (file_exists($storagePath)) {
            return json_decode(file_get_contents($storagePath), true) ?? [];
        }

        // Fall back to fixtures
        $fixturePath = BASE_PATH . "/fixtures/assets.json";
        if (file_exists($fixturePath)) {
            return json_decode(file_get_contents($fixturePath), true) ?? [];
        }

        return [];
    }

    /**
     * Save assets collection.
     */
    private static function saveAssetsCollection(array $assets): void
    {
        $path = STORAGE_PATH . "/pimcore_shadow/assets.json";
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmp = $path . '.tmp.' . getmypid();
        file_put_contents($tmp, json_encode($assets, JSON_PRETTY_PRINT));
        rename($tmp, $path);
    }

    /**
     * Get asset by ID.
     */
    private static function getAssetById(string $assetId): ?array
    {
        $assets = self::loadAssetsCollection();

        foreach ($assets as $asset) {
            if (($asset['id'] ?? null) === $assetId) {
                return $asset;
            }
        }

        return null;
    }

    /**
     * Save asset metadata to individual file.
     */
    private static function saveAssetMetadata(string $assetId, array $data): void
    {
        $dir = STORAGE_PATH . "/attachments/meta";
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = "{$dir}/{$assetId}.json";
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Determine asset type from filename.
     */
    private static function getAssetType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' => 'image',
            'mp4', 'webm', 'mov', 'avi' => 'video',
            'mp3', 'wav', 'ogg' => 'audio',
            'pdf' => 'document',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx' => 'document',
            'zip', 'tar', 'gz' => 'archive',
            default => 'file',
        };
    }

    /**
     * Determine MIME type from filename.
     */
    private static function getMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'pdf' => 'application/pdf',
            'json' => 'application/json',
            default => 'application/octet-stream',
        };
    }
}
