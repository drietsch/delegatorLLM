#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Build RAG Indexes
 *
 * Builds inverted indexes for all fixture collections.
 * Run with: php bin/build-indexes.php
 *
 * Options:
 *   --collection=NAME  Build only specific collection
 *   --verbose          Show detailed output
 *   --dry-run          Don't write indexes, just show stats
 */

require_once __DIR__ . '/../src/bootstrap.php';

use App\Rag\RagService;
use App\Rag\InvertedIndex;

echo "=== RAG Index Builder ===\n";
echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";

// Parse arguments
$args = parseArgs($argv);
$targetCollection = $args['collection'] ?? null;
$verbose = isset($args['verbose']);
$dryRun = isset($args['dry-run']);

if ($dryRun) {
    echo "DRY RUN MODE - No indexes will be written\n\n";
}

// Collection configurations
$collections = [
    'products' => [
        'fields' => ['name', 'description', 'category', 'sku'],
        'source' => BASE_PATH . '/fixtures/products.json',
    ],
    'assets' => [
        'fields' => ['filename', 'metadata.alt', 'metadata.tags', 'path'],
        'source' => BASE_PATH . '/fixtures/assets.json',
    ],
    'documents' => [
        'fields' => ['title', 'content', 'snippet', 'path'],
        'source' => BASE_PATH . '/fixtures/documents.json',
    ],
    'customers' => [
        'fields' => ['name', 'email', 'company', 'notes', 'tags'],
        'source' => BASE_PATH . '/fixtures/customers.json',
    ],
];

// Filter to specific collection if requested
if ($targetCollection) {
    if (!isset($collections[$targetCollection])) {
        echo "ERROR: Unknown collection: $targetCollection\n";
        echo "Available collections: " . implode(', ', array_keys($collections)) . "\n";
        exit(1);
    }
    $collections = [$targetCollection => $collections[$targetCollection]];
}

// Ensure index directory exists
$indexPath = STORAGE_PATH . '/rag/index';
if (!is_dir($indexPath)) {
    mkdir($indexPath, 0755, true);
    echo "Created index directory: $indexPath\n\n";
}

$totalDocs = 0;
$totalTerms = 0;
$errors = [];

foreach ($collections as $name => $config) {
    echo "Building index for: $name\n";
    echo str_repeat('-', 40) . "\n";

    $sourcePath = $config['source'];
    $fields = $config['fields'];

    // Check source file
    if (!file_exists($sourcePath)) {
        $errors[] = "Source file not found: $sourcePath";
        echo "  ERROR: Source file not found!\n\n";
        continue;
    }

    // Load data
    $data = json_decode(file_get_contents($sourcePath), true);

    if (!$data || !is_array($data)) {
        $errors[] = "Invalid JSON in: $sourcePath";
        echo "  ERROR: Invalid JSON in source file!\n\n";
        continue;
    }

    $docCount = count($data);
    echo "  Source: $sourcePath\n";
    echo "  Documents: $docCount\n";
    echo "  Fields: " . implode(', ', $fields) . "\n";

    // Build index
    $index = new InvertedIndex();
    $startTime = microtime(true);

    $index->build($data, $fields);

    $buildTime = round((microtime(true) - $startTime) * 1000, 2);
    $stats = $index->getStats();

    echo "  Terms indexed: {$stats['terms']}\n";
    echo "  Build time: {$buildTime}ms\n";

    if ($verbose) {
        echo "  Avg terms/doc: " . round($stats['avgTermsPerDoc'], 2) . "\n";
    }

    // Save index
    if (!$dryRun) {
        $outputPath = "$indexPath/{$name}.index.json";
        $index->save($outputPath);
        $fileSize = formatBytes(filesize($outputPath));
        echo "  Saved to: $outputPath ($fileSize)\n";
    }

    $totalDocs += $docCount;
    $totalTerms += $stats['terms'];

    echo "\n";
}

// Summary
echo str_repeat('=', 40) . "\n";
echo "Summary\n";
echo str_repeat('=', 40) . "\n";
echo "Collections processed: " . count($collections) . "\n";
echo "Total documents: $totalDocs\n";
echo "Total unique terms: $totalTerms\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\nCompleted at: " . date('Y-m-d H:i:s') . "\n";

// Test search if not dry run
if (!$dryRun && !$targetCollection) {
    echo "\n";
    echo str_repeat('=', 40) . "\n";
    echo "Testing RAG Search\n";
    echo str_repeat('=', 40) . "\n";

    $rag = new RagService();

    $testQueries = [
        'laptop professional' => ['products'],
        'brand logo' => ['assets'],
        'shipping delivery free' => ['documents'],
        'enterprise client' => ['customers'],
    ];

    foreach ($testQueries as $query => $searchCollections) {
        echo "\nQuery: \"$query\" (collections: " . implode(', ', $searchCollections) . ")\n";
        $results = $rag->retrieve($query, $searchCollections, 3);

        foreach ($results as $collection => $items) {
            echo "  [$collection] Found " . count($items) . " results:\n";
            foreach ($items as $item) {
                $doc = $item['document'];
                $score = round($item['score'], 3);
                $name = $doc['name'] ?? $doc['title'] ?? $doc['filename'] ?? 'Unknown';
                echo "    - $name (score: $score)\n";
            }
        }
    }
}

echo "\nDone!\n";

/**
 * Parse command line arguments.
 */
function parseArgs(array $argv): array
{
    $args = [];

    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--')) {
            $arg = substr($arg, 2);
            if (str_contains($arg, '=')) {
                [$key, $value] = explode('=', $arg, 2);
                $args[$key] = $value;
            } else {
                $args[$arg] = true;
            }
        }
    }

    return $args;
}

/**
 * Format bytes to human readable.
 */
function formatBytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $unitIndex = 0;

    while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
        $bytes /= 1024;
        $unitIndex++;
    }

    return round($bytes, 2) . ' ' . $units[$unitIndex];
}
