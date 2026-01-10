<?php
declare(strict_types=1);

namespace App\Rag;

/**
 * RAG Service
 *
 * Retrieval-Augmented Generation service.
 * Provides context retrieval from multiple collections for agent queries.
 */
class RagService
{
    private array $indexes = [];
    private string $indexPath;

    // Collection configurations
    private const COLLECTIONS = [
        'products' => ['name', 'description', 'category', 'sku'],
        'assets' => ['filename', 'metadata.alt', 'metadata.tags'],
        'documents' => ['title', 'content', 'snippet'],
        'customers' => ['name', 'email', 'company', 'notes'],
    ];

    public function __construct(?string $indexPath = null)
    {
        $this->indexPath = $indexPath ?? STORAGE_PATH . '/rag/index';
        $this->loadIndexes();
    }

    /**
     * Retrieve relevant documents for a query.
     *
     * @param string $query       Search query
     * @param array  $collections Collections to search (empty = all)
     * @param int    $topK        Max results per collection
     *
     * @return array Results grouped by collection
     */
    public function retrieve(
        string $query,
        array $collections = [],
        int $topK = 5
    ): array {
        if (empty($collections)) {
            $collections = array_keys(self::COLLECTIONS);
        }

        $results = [];

        foreach ($collections as $collection) {
            if (!isset($this->indexes[$collection])) {
                continue;
            }

            $matches = $this->indexes[$collection]->search($query, $topK);
            $results[$collection] = $matches;
        }

        return $results;
    }

    /**
     * Format retrieved results as context for LLM.
     *
     * @param array $results Results from retrieve()
     *
     * @return string Formatted context string
     */
    public function formatContext(array $results): string
    {
        if (empty($results)) {
            return '';
        }

        $context = "<EXTRA-CONTEXT>\n";
        $context .= "The following information was retrieved from the database:\n\n";

        foreach ($results as $collection => $items) {
            if (empty($items)) {
                continue;
            }

            $context .= "## " . ucfirst($collection) . "\n";

            foreach ($items as $item) {
                $doc = $item['document'] ?? $item;
                $score = $item['score'] ?? null;

                // Format document summary
                $summary = $this->formatDocumentSummary($doc, $collection);
                $context .= "- $summary";

                if ($score !== null) {
                    $context .= " (relevance: " . round($score, 2) . ")";
                }

                $context .= "\n";
            }

            $context .= "\n";
        }

        $context .= "</EXTRA-CONTEXT>";

        return $context;
    }

    /**
     * Retrieve and format context in one call.
     */
    public function getContext(
        string $query,
        array $collections = [],
        int $topK = 5
    ): string {
        $results = $this->retrieve($query, $collections, $topK);
        return $this->formatContext($results);
    }

    /**
     * Build or rebuild an index for a collection.
     *
     * @param string $collection Collection name
     * @param array  $documents  Documents to index
     * @param array  $fields     Fields to index (or use defaults)
     */
    public function buildIndex(
        string $collection,
        array $documents,
        ?array $fields = null
    ): void {
        $fields = $fields ?? (self::COLLECTIONS[$collection] ?? ['name', 'description']);

        $index = new InvertedIndex();
        $index->build($documents, $fields);

        // Save index
        $path = "{$this->indexPath}/{$collection}.index.json";
        $index->save($path);

        // Update loaded index
        $this->indexes[$collection] = $index;
    }

    /**
     * Load indexes from disk.
     */
    private function loadIndexes(): void
    {
        foreach (array_keys(self::COLLECTIONS) as $collection) {
            $path = "{$this->indexPath}/{$collection}.index.json";

            if (file_exists($path)) {
                try {
                    $index = new InvertedIndex();
                    $index->load($path);
                    $this->indexes[$collection] = $index;
                } catch (\Throwable $e) {
                    // Log error but continue loading other indexes
                    error_log("Failed to load index for $collection: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Check if a collection index exists and is ready.
     */
    public function hasIndex(string $collection): bool
    {
        return isset($this->indexes[$collection]) && $this->indexes[$collection]->isReady();
    }

    /**
     * Get statistics for all loaded indexes.
     */
    public function getStats(): array
    {
        $stats = [];

        foreach ($this->indexes as $collection => $index) {
            $stats[$collection] = $index->getStats();
        }

        return $stats;
    }

    /**
     * Get list of available collections.
     */
    public function getCollections(): array
    {
        return array_keys(self::COLLECTIONS);
    }

    /**
     * Get loaded collection names.
     */
    public function getLoadedCollections(): array
    {
        return array_keys($this->indexes);
    }

    /**
     * Format a document as a brief summary.
     */
    private function formatDocumentSummary(array $doc, string $collection): string
    {
        return match ($collection) {
            'products' => $this->formatProduct($doc),
            'assets' => $this->formatAsset($doc),
            'documents' => $this->formatDocument($doc),
            'customers' => $this->formatCustomer($doc),
            default => json_encode($doc),
        };
    }

    /**
     * Format product document.
     */
    private function formatProduct(array $doc): string
    {
        $id = $doc['id'] ?? 'unknown';
        $name = $doc['name'] ?? 'Unnamed';
        $sku = $doc['sku'] ?? '';
        $price = isset($doc['price']) ? '$' . number_format($doc['price'], 2) : '';
        $category = $doc['category'] ?? '';

        $parts = ["**$name**"];
        if ($sku) $parts[] = "SKU: $sku";
        if ($price) $parts[] = $price;
        if ($category) $parts[] = "($category)";

        return implode(' | ', $parts) . " [id: $id]";
    }

    /**
     * Format asset document.
     */
    private function formatAsset(array $doc): string
    {
        $id = $doc['id'] ?? 'unknown';
        $filename = $doc['filename'] ?? 'unknown';
        $type = $doc['type'] ?? 'file';
        $path = $doc['path'] ?? '/';

        return "**$filename** ($type) at $path [id: $id]";
    }

    /**
     * Format content document.
     */
    private function formatDocument(array $doc): string
    {
        $id = $doc['id'] ?? 'unknown';
        $title = $doc['title'] ?? 'Untitled';
        $type = $doc['type'] ?? 'page';

        return "**$title** ($type) [id: $id]";
    }

    /**
     * Format customer document.
     */
    private function formatCustomer(array $doc): string
    {
        $id = $doc['id'] ?? 'unknown';
        $name = $doc['name'] ?? 'Unknown';
        $email = $doc['email'] ?? '';
        $company = $doc['company'] ?? '';

        $parts = ["**$name**"];
        if ($email) $parts[] = $email;
        if ($company) $parts[] = "at $company";

        return implode(' | ', $parts) . " [id: $id]";
    }
}
