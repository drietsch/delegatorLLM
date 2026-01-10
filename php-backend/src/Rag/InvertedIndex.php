<?php
declare(strict_types=1);

namespace App\Rag;

/**
 * Inverted Index
 *
 * Simple keyword-based inverted index for RAG retrieval.
 * Provides fast text search without requiring vector embeddings.
 */
class InvertedIndex
{
    private array $index = [];
    private array $documents = [];
    private array $documentFrequency = [];
    private int $totalDocuments = 0;

    /**
     * Build index from documents.
     *
     * @param array $documents   Array of documents (with unique keys or indexed)
     * @param array $textFields  Fields to index from each document
     */
    public function build(array $documents, array $textFields): void
    {
        $this->index = [];
        $this->documents = [];
        $this->documentFrequency = [];
        $this->totalDocuments = 0;

        foreach ($documents as $id => $doc) {
            // Use document's id field if present, otherwise use array key
            $docId = $doc['id'] ?? (string) $id;
            $this->documents[$docId] = $doc;
            $this->totalDocuments++;

            // Extract text from specified fields
            $text = $this->extractText($doc, $textFields);
            $tokens = $this->tokenize($text);
            $tokenCounts = array_count_values($tokens);

            // Update index
            foreach ($tokenCounts as $token => $count) {
                if (!isset($this->index[$token])) {
                    $this->index[$token] = [];
                }
                $this->index[$token][$docId] = $count;

                // Update document frequency
                if (!isset($this->documentFrequency[$token])) {
                    $this->documentFrequency[$token] = 0;
                }
                $this->documentFrequency[$token]++;
            }
        }
    }

    /**
     * Search for documents matching query.
     *
     * @param string $query Search query
     * @param int    $topK  Maximum results to return
     *
     * @return array Matching documents sorted by relevance
     */
    public function search(string $query, int $topK = 5): array
    {
        $queryTokens = $this->tokenize($query);

        if (empty($queryTokens)) {
            return [];
        }

        $scores = [];

        foreach ($queryTokens as $token) {
            if (!isset($this->index[$token])) {
                continue;
            }

            // Calculate IDF (Inverse Document Frequency)
            $idf = log(1 + $this->totalDocuments / ($this->documentFrequency[$token] ?? 1));

            foreach ($this->index[$token] as $docId => $termFrequency) {
                // TF-IDF scoring
                $tf = 1 + log($termFrequency);
                $scores[$docId] = ($scores[$docId] ?? 0) + ($tf * $idf);
            }
        }

        // Sort by score (highest first)
        arsort($scores);

        // Get top K document IDs
        $topIds = array_slice(array_keys($scores), 0, $topK);

        // Return documents with scores
        $results = [];
        foreach ($topIds as $docId) {
            if (isset($this->documents[$docId])) {
                $results[] = [
                    'document' => $this->documents[$docId],
                    'score' => $scores[$docId],
                ];
            }
        }

        return $results;
    }

    /**
     * Get documents without scoring (just return matches).
     */
    public function getMatches(string $query, int $topK = 5): array
    {
        $results = $this->search($query, $topK);
        return array_map(fn($r) => $r['document'], $results);
    }

    /**
     * Save index to file.
     */
    public function save(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $data = [
            'index' => $this->index,
            'documents' => $this->documents,
            'documentFrequency' => $this->documentFrequency,
            'totalDocuments' => $this->totalDocuments,
            'createdAt' => date('c'),
        ];

        // Atomic write
        $tmp = $path . '.tmp.' . getmypid();
        file_put_contents($tmp, json_encode($data));
        rename($tmp, $path);
    }

    /**
     * Load index from file.
     */
    public function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Index file not found: $path");
        }

        $data = json_decode(file_get_contents($path), true);

        if (!$data) {
            throw new \RuntimeException("Invalid index file: $path");
        }

        $this->index = $data['index'] ?? [];
        $this->documents = $data['documents'] ?? [];
        $this->documentFrequency = $data['documentFrequency'] ?? [];
        $this->totalDocuments = $data['totalDocuments'] ?? 0;
    }

    /**
     * Check if index is loaded/built.
     */
    public function isReady(): bool
    {
        return $this->totalDocuments > 0;
    }

    /**
     * Get index statistics.
     */
    public function getStats(): array
    {
        return [
            'documents' => $this->totalDocuments,
            'terms' => count($this->index),
            'avgTermsPerDoc' => $this->totalDocuments > 0
                ? array_sum(array_map('count', $this->index)) / $this->totalDocuments
                : 0,
        ];
    }

    /**
     * Extract text from document using field paths.
     */
    private function extractText(array $doc, array $fields): string
    {
        $texts = [];

        foreach ($fields as $field) {
            $value = $this->getNestedValue($doc, $field);

            if (is_string($value)) {
                $texts[] = $value;
            } elseif (is_array($value)) {
                // Handle arrays (e.g., tags)
                $texts[] = implode(' ', array_filter($value, 'is_string'));
            }
        }

        return implode(' ', $texts);
    }

    /**
     * Get nested value from array using dot notation.
     */
    private function getNestedValue(array $doc, string $field): mixed
    {
        $keys = explode('.', $field);
        $value = $doc;

        foreach ($keys as $key) {
            if (!is_array($value) || !isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Tokenize text into searchable terms.
     */
    private function tokenize(string $text): array
    {
        // Convert to lowercase
        $text = strtolower($text);

        // Split on non-word characters
        $tokens = preg_split('/\W+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Filter out short tokens and stopwords
        $stopwords = ['the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been',
            'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would',
            'could', 'should', 'may', 'might', 'must', 'shall', 'can', 'need',
            'to', 'of', 'in', 'for', 'on', 'with', 'at', 'by', 'from', 'as',
            'into', 'through', 'during', 'before', 'after', 'above', 'below',
            'and', 'or', 'but', 'if', 'then', 'else', 'when', 'up', 'down',
            'out', 'off', 'over', 'under', 'again', 'further', 'once'];

        return array_filter($tokens, function ($token) use ($stopwords) {
            return strlen($token) > 2 && !in_array($token, $stopwords);
        });
    }
}
