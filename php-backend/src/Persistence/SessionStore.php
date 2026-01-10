<?php
declare(strict_types=1);

namespace App\Persistence;

/**
 * Session Store
 *
 * File-based persistence for chat sessions.
 * Each session is stored as a JSON file.
 */
class SessionStore
{
    private string $basePath;

    public function __construct(?string $storagePath = null)
    {
        $this->basePath = ($storagePath ?? STORAGE_PATH) . '/sessions';

        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0755, true);
        }
    }

    /**
     * Get a session by ID.
     */
    public function get(string $sessionId): ?array
    {
        $path = $this->getPath($sessionId);

        if (!file_exists($path)) {
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Save a session.
     */
    public function save(string $sessionId, array $data): void
    {
        $data['updatedAt'] = date('c');
        $path = $this->getPath($sessionId);

        $this->atomicWrite($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Create a new session.
     */
    public function create(string $agentId, array $context = []): string
    {
        $sessionId = bin2hex(random_bytes(16));

        $this->save($sessionId, [
            'id' => $sessionId,
            'agentId' => $agentId,
            'context' => $context,
            'createdAt' => date('c'),
        ]);

        return $sessionId;
    }

    /**
     * Update session context.
     */
    public function updateContext(string $sessionId, array $context): bool
    {
        $session = $this->get($sessionId);

        if (!$session) {
            return false;
        }

        $session['context'] = array_merge($session['context'] ?? [], $context);
        $this->save($sessionId, $session);

        return true;
    }

    /**
     * Delete a session.
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
     * List all sessions (optionally by agent).
     */
    public function list(?string $agentId = null, int $limit = 100): array
    {
        $files = glob("{$this->basePath}/*.json");
        $sessions = [];

        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);

            if ($agentId && ($data['agentId'] ?? null) !== $agentId) {
                continue;
            }

            $sessions[] = $data;

            if (count($sessions) >= $limit) {
                break;
            }
        }

        // Sort by creation date (newest first)
        usort($sessions, fn($a, $b) =>
            strtotime($b['createdAt'] ?? '0') - strtotime($a['createdAt'] ?? '0')
        );

        return $sessions;
    }

    /**
     * Check if a session exists.
     */
    public function exists(string $sessionId): bool
    {
        return file_exists($this->getPath($sessionId));
    }

    /**
     * Get the file path for a session.
     */
    private function getPath(string $sessionId): string
    {
        // Sanitize session ID to prevent path traversal
        $safeId = preg_replace('/[^a-f0-9]/i', '', $sessionId);
        return "{$this->basePath}/{$safeId}.json";
    }

    /**
     * Atomic file write (write to tmp, then rename).
     */
    private function atomicWrite(string $path, string $content): void
    {
        $tmp = $path . '.tmp.' . getmypid();
        file_put_contents($tmp, $content);
        rename($tmp, $path);
    }
}
