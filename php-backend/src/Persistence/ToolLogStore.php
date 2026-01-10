<?php
declare(strict_types=1);

namespace App\Persistence;

/**
 * Tool Log Store
 *
 * File-based persistence for tool call logs using JSONL (append-only).
 * Each session has its own tool log file for debugging and auditing.
 */
class ToolLogStore
{
    private string $basePath;

    public function __construct(?string $storagePath = null)
    {
        $this->basePath = ($storagePath ?? STORAGE_PATH) . '/tools';

        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0755, true);
        }
    }

    /**
     * Append a tool call log entry.
     */
    public function append(string $sessionId, array $log): void
    {
        $path = $this->getPath($sessionId);

        // Add metadata if not present
        $log['id'] = $log['id'] ?? bin2hex(random_bytes(8));
        $log['ts'] = $log['ts'] ?? date('c');
        $log['startedAt'] = $log['startedAt'] ?? date('c');

        // Append with file locking
        $fp = fopen($path, 'a');
        if ($fp) {
            flock($fp, LOCK_EX);
            fwrite($fp, json_encode($log) . "\n");
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    /**
     * Get all tool logs for a session.
     */
    public function getAll(string $sessionId): array
    {
        $path = $this->getPath($sessionId);

        if (!file_exists($path)) {
            return [];
        }

        $logs = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded) {
                $logs[] = $decoded;
            }
        }

        return $logs;
    }

    /**
     * Get tool logs filtered by tool name.
     */
    public function getByTool(string $sessionId, string $toolName): array
    {
        $all = $this->getAll($sessionId);
        return array_filter($all, fn($log) => ($log['tool'] ?? '') === $toolName);
    }

    /**
     * Get tool logs filtered by status.
     */
    public function getByStatus(string $sessionId, string $status): array
    {
        $all = $this->getAll($sessionId);
        return array_filter($all, fn($log) => ($log['status'] ?? '') === $status);
    }

    /**
     * Get error logs only.
     */
    public function getErrors(string $sessionId): array
    {
        return $this->getByStatus($sessionId, 'error');
    }

    /**
     * Get summary statistics for a session.
     */
    public function getStats(string $sessionId): array
    {
        $logs = $this->getAll($sessionId);

        $stats = [
            'total' => count($logs),
            'by_tool' => [],
            'by_status' => [],
            'errors' => 0,
        ];

        foreach ($logs as $log) {
            $tool = $log['tool'] ?? 'unknown';
            $status = $log['status'] ?? 'unknown';

            $stats['by_tool'][$tool] = ($stats['by_tool'][$tool] ?? 0) + 1;
            $stats['by_status'][$status] = ($stats['by_status'][$status] ?? 0) + 1;

            if ($status === 'error') {
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Delete all tool logs for a session.
     */
    public function delete(string $sessionId): bool
    {
        $path = $this->getPath($sessionId);

        if (file_exists($path)) {
            return unlink($path);
        }

        return false;
    }

    /**
     * Get the file path for a session's tool logs.
     */
    private function getPath(string $sessionId): string
    {
        // Sanitize session ID to prevent path traversal
        $safeId = preg_replace('/[^a-f0-9]/i', '', $sessionId);
        return "{$this->basePath}/{$safeId}.jsonl";
    }
}
