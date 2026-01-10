<?php
declare(strict_types=1);

namespace App\Persistence;

/**
 * Message Store
 *
 * File-based persistence for chat messages using JSONL (append-only).
 * Each session has its own message log file.
 */
class MessageStore
{
    private string $basePath;

    public function __construct(?string $storagePath = null)
    {
        $this->basePath = ($storagePath ?? STORAGE_PATH) . '/messages';

        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0755, true);
        }
    }

    /**
     * Append a message to a session's history.
     */
    public function append(string $sessionId, array $message): void
    {
        $path = $this->getPath($sessionId);

        // Add metadata if not present
        $message['id'] = $message['id'] ?? bin2hex(random_bytes(8));
        $message['ts'] = $message['ts'] ?? date('c');

        // Append with file locking to prevent interleaving
        $fp = fopen($path, 'a');
        if ($fp) {
            flock($fp, LOCK_EX);
            fwrite($fp, json_encode($message) . "\n");
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    /**
     * Get all messages for a session.
     */
    public function getAll(string $sessionId): array
    {
        $path = $this->getPath($sessionId);

        if (!file_exists($path)) {
            return [];
        }

        $messages = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded) {
                $messages[] = $decoded;
            }
        }

        return $messages;
    }

    /**
     * Get recent messages for a session (with limit).
     */
    public function getRecent(string $sessionId, int $limit = 10): array
    {
        $all = $this->getAll($sessionId);
        return array_slice($all, -$limit);
    }

    /**
     * Get message count for a session.
     */
    public function count(string $sessionId): int
    {
        $path = $this->getPath($sessionId);

        if (!file_exists($path)) {
            return 0;
        }

        $count = 0;
        $fp = fopen($path, 'r');
        if ($fp) {
            while (fgets($fp) !== false) {
                $count++;
            }
            fclose($fp);
        }

        return $count;
    }

    /**
     * Delete all messages for a session.
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
     * Stream messages from a session (memory-efficient for large logs).
     *
     * @return \Generator
     */
    public function stream(string $sessionId): \Generator
    {
        $path = $this->getPath($sessionId);

        if (!file_exists($path)) {
            return;
        }

        $fp = fopen($path, 'r');
        if ($fp) {
            while (($line = fgets($fp)) !== false) {
                $decoded = json_decode(trim($line), true);
                if ($decoded) {
                    yield $decoded;
                }
            }
            fclose($fp);
        }
    }

    /**
     * Get messages by role.
     */
    public function getByRole(string $sessionId, string $role): array
    {
        $all = $this->getAll($sessionId);
        return array_filter($all, fn($m) => ($m['role'] ?? '') === $role);
    }

    /**
     * Get the file path for a session's messages.
     */
    private function getPath(string $sessionId): string
    {
        // Sanitize session ID to prevent path traversal
        $safeId = preg_replace('/[^a-f0-9]/i', '', $sessionId);
        return "{$this->basePath}/{$safeId}.jsonl";
    }
}
