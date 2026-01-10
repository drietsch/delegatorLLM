<?php
declare(strict_types=1);

namespace App\Tools;

/**
 * Data Object Tool
 *
 * CRUD operations for data objects (products, categories, etc.).
 * Uses file-based storage in the pimcore_shadow directory.
 */
class DataObjectTool
{
    /**
     * Execute a data object operation.
     *
     * @param string      $action      Action: create, read, update, delete, list
     * @param string|null $object_type Object type: product, category, customer, etc.
     * @param string|null $object_id   Object ID (for read, update, delete)
     * @param string|null $data        JSON data (for create, update)
     *
     * @return string JSON encoded result
     */
    public static function execute(
        string $action,
        ?string $object_type = 'product',
        ?string $object_id = null,
        ?string $data = null
    ): string {
        $type = $object_type ?? 'product';

        return match (strtolower($action)) {
            'create' => self::create($type, $data),
            'read', 'get' => self::read($type, $object_id),
            'update' => self::update($type, $object_id, $data),
            'delete' => self::delete($type, $object_id),
            'list' => self::list($type),
            default => json_encode(['error' => "Unknown action: $action"]),
        };
    }

    /**
     * Create a new data object.
     */
    private static function create(string $type, ?string $data): string
    {
        if (!$data) {
            return json_encode(['error' => 'No data provided for create']);
        }

        $objectData = json_decode($data, true);
        if (!$objectData) {
            return json_encode(['error' => 'Invalid JSON data']);
        }

        // Generate ID if not provided
        $objectData['id'] = $objectData['id'] ?? self::generateId($type);
        $objectData['createdAt'] = date('c');
        $objectData['updatedAt'] = date('c');

        // Load existing collection
        $collection = self::loadCollection($type);
        $collection[] = $objectData;

        // Save collection
        self::saveCollection($type, $collection);

        return json_encode([
            'success' => true,
            'action' => 'create',
            'object_type' => $type,
            'object' => $objectData,
        ]);
    }

    /**
     * Read a data object by ID.
     */
    private static function read(string $type, ?string $objectId): string
    {
        if (!$objectId) {
            return json_encode(['error' => 'No object_id provided for read']);
        }

        $collection = self::loadCollection($type);

        foreach ($collection as $item) {
            if (($item['id'] ?? null) === $objectId) {
                return json_encode([
                    'success' => true,
                    'action' => 'read',
                    'object_type' => $type,
                    'object' => $item,
                ]);
            }
        }

        return json_encode([
            'error' => 'Object not found',
            'object_type' => $type,
            'object_id' => $objectId,
        ]);
    }

    /**
     * Update a data object.
     */
    private static function update(string $type, ?string $objectId, ?string $data): string
    {
        if (!$objectId) {
            return json_encode(['error' => 'No object_id provided for update']);
        }

        if (!$data) {
            return json_encode(['error' => 'No data provided for update']);
        }

        $updateData = json_decode($data, true);
        if (!$updateData) {
            return json_encode(['error' => 'Invalid JSON data']);
        }

        $collection = self::loadCollection($type);
        $found = false;
        $updatedObject = null;

        foreach ($collection as $index => $item) {
            if (($item['id'] ?? null) === $objectId) {
                // Merge update data
                $collection[$index] = array_merge($item, $updateData);
                $collection[$index]['id'] = $objectId; // Preserve ID
                $collection[$index]['updatedAt'] = date('c');
                $updatedObject = $collection[$index];
                $found = true;
                break;
            }
        }

        if (!$found) {
            return json_encode([
                'error' => 'Object not found',
                'object_type' => $type,
                'object_id' => $objectId,
            ]);
        }

        self::saveCollection($type, $collection);

        return json_encode([
            'success' => true,
            'action' => 'update',
            'object_type' => $type,
            'object' => $updatedObject,
        ]);
    }

    /**
     * Delete a data object.
     */
    private static function delete(string $type, ?string $objectId): string
    {
        if (!$objectId) {
            return json_encode(['error' => 'No object_id provided for delete']);
        }

        $collection = self::loadCollection($type);
        $found = false;
        $deletedObject = null;

        foreach ($collection as $index => $item) {
            if (($item['id'] ?? null) === $objectId) {
                $deletedObject = $item;
                unset($collection[$index]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return json_encode([
                'error' => 'Object not found',
                'object_type' => $type,
                'object_id' => $objectId,
            ]);
        }

        // Re-index array
        $collection = array_values($collection);
        self::saveCollection($type, $collection);

        return json_encode([
            'success' => true,
            'action' => 'delete',
            'object_type' => $type,
            'object_id' => $objectId,
            'deleted_object' => $deletedObject,
        ]);
    }

    /**
     * List all objects of a type.
     */
    private static function list(string $type): string
    {
        $collection = self::loadCollection($type);

        return json_encode([
            'success' => true,
            'action' => 'list',
            'object_type' => $type,
            'total' => count($collection),
            'objects' => $collection,
        ]);
    }

    /**
     * Load collection from storage.
     */
    private static function loadCollection(string $type): array
    {
        $path = self::getCollectionPath($type);

        if (!file_exists($path)) {
            // Try loading from fixtures
            $fixturePath = BASE_PATH . "/fixtures/{$type}s.json";
            if (file_exists($fixturePath)) {
                return json_decode(file_get_contents($fixturePath), true) ?? [];
            }
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    /**
     * Save collection to storage.
     */
    private static function saveCollection(string $type, array $collection): void
    {
        $path = self::getCollectionPath($type);
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Atomic write
        $tmp = $path . '.tmp.' . getmypid();
        file_put_contents($tmp, json_encode($collection, JSON_PRETTY_PRINT));
        rename($tmp, $path);
    }

    /**
     * Get storage path for a collection.
     */
    private static function getCollectionPath(string $type): string
    {
        return STORAGE_PATH . "/pimcore_shadow/{$type}s.json";
    }

    /**
     * Generate a unique ID for an object.
     */
    private static function generateId(string $type): string
    {
        $prefix = substr($type, 0, 4);
        return $prefix . '_' . bin2hex(random_bytes(8));
    }
}
