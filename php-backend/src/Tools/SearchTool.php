<?php
declare(strict_types=1);

namespace App\Tools;

/**
 * Search Tool
 *
 * Provides search functionality across products, assets, documents, and customers.
 * Reads from fixture files and supports keyword matching with filters.
 */
class SearchTool
{
    /**
     * Execute a search query.
     *
     * @param string      $query       Search query string
     * @param string|null $object_type Type to search: product, asset, document, customer
     * @param string|null $filters     JSON string of filters
     * @param int|null    $limit       Maximum results
     *
     * @return string JSON encoded results
     */
    public static function execute(
        string $query,
        ?string $object_type = 'product',
        ?string $filters = null,
        ?int $limit = 10
    ): string {
        $collection = self::getCollectionName($object_type ?? 'product');
        $data = self::loadCollection($collection);

        if (empty($data)) {
            return json_encode([
                'total' => 0,
                'results' => [],
                'query' => $query,
                'collection' => $collection,
            ]);
        }

        $filterArray = [];
        if ($filters) {
            $filterArray = json_decode($filters, true) ?? [];
        }

        $results = self::search($data, $query, $filterArray, $limit ?? 10);

        return json_encode([
            'total' => count($results),
            'results' => $results,
            'query' => $query,
            'collection' => $collection,
            'filters' => $filterArray,
        ]);
    }

    /**
     * Map object type to collection name.
     */
    private static function getCollectionName(string $objectType): string
    {
        return match (strtolower($objectType)) {
            'product', 'products' => 'products',
            'asset', 'assets' => 'assets',
            'document', 'documents' => 'documents',
            'customer', 'customers' => 'customers',
            default => 'products',
        };
    }

    /**
     * Load collection data from fixture file.
     */
    private static function loadCollection(string $collection): array
    {
        $fixturePath = BASE_PATH . "/fixtures/{$collection}.json";

        if (!file_exists($fixturePath)) {
            // Try storage path for shadow data
            $storagePath = STORAGE_PATH . "/pimcore_shadow/{$collection}.json";
            if (file_exists($storagePath)) {
                return json_decode(file_get_contents($storagePath), true) ?? [];
            }
            return [];
        }

        return json_decode(file_get_contents($fixturePath), true) ?? [];
    }

    /**
     * Perform search with keyword matching and filters.
     */
    private static function search(array $data, string $query, array $filters, int $limit): array
    {
        $query = strtolower(trim($query));
        $results = [];

        foreach ($data as $item) {
            // Skip if doesn't match filters
            if (!self::matchesFilters($item, $filters)) {
                continue;
            }

            // Score the item based on query match
            $score = self::scoreMatch($item, $query);

            if ($score > 0) {
                $results[] = [
                    'item' => $item,
                    'score' => $score,
                ];
            }
        }

        // Sort by score (highest first)
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        // Extract items and limit
        $results = array_slice(
            array_map(fn($r) => $r['item'], $results),
            0,
            $limit
        );

        return $results;
    }

    /**
     * Check if item matches all filters.
     */
    private static function matchesFilters(array $item, array $filters): bool
    {
        foreach ($filters as $key => $value) {
            $itemValue = self::getNestedValue($item, $key);

            if ($itemValue === null) {
                return false;
            }

            // Handle different filter types
            if (is_array($value)) {
                // Array means "in" filter
                if (!in_array($itemValue, $value)) {
                    return false;
                }
            } elseif (is_string($value) && str_starts_with($value, '>')) {
                // Greater than filter
                $threshold = (float) substr($value, 1);
                if ((float) $itemValue <= $threshold) {
                    return false;
                }
            } elseif (is_string($value) && str_starts_with($value, '<')) {
                // Less than filter
                $threshold = (float) substr($value, 1);
                if ((float) $itemValue >= $threshold) {
                    return false;
                }
            } else {
                // Exact match (case-insensitive for strings)
                if (is_string($itemValue) && is_string($value)) {
                    if (strtolower($itemValue) !== strtolower($value)) {
                        return false;
                    }
                } elseif ($itemValue != $value) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Score how well an item matches the query.
     */
    private static function scoreMatch(array $item, string $query): int
    {
        if (empty($query)) {
            return 1; // Match all if no query
        }

        $score = 0;
        $text = strtolower(json_encode($item));

        // Split query into words
        $words = preg_split('/\s+/', $query);

        foreach ($words as $word) {
            if (empty($word)) continue;

            // Exact word match
            if (str_contains($text, $word)) {
                $score += 1;
            }

            // Bonus for title/name match
            $name = strtolower($item['name'] ?? $item['title'] ?? '');
            if (str_contains($name, $word)) {
                $score += 3;
            }

            // Bonus for SKU match
            $sku = strtolower($item['sku'] ?? '');
            if (str_contains($sku, $word)) {
                $score += 5;
            }
        }

        return $score;
    }

    /**
     * Get nested value from array using dot notation.
     */
    private static function getNestedValue(array $item, string $key): mixed
    {
        $keys = explode('.', $key);
        $value = $item;

        foreach ($keys as $k) {
            if (!is_array($value) || !isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }
}
