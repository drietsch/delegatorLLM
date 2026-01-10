<?php
declare(strict_types=1);

namespace App\Queue;

/**
 * Job Queue
 *
 * File-based job queue for background processing.
 * Uses atomic file operations for concurrent safety.
 */
class JobQueue
{
    private string $jobsPath;
    private string $leasesPath;

    public function __construct(?string $storagePath = null)
    {
        $basePath = $storagePath ?? STORAGE_PATH;
        $this->jobsPath = "$basePath/queue/jobs";
        $this->leasesPath = "$basePath/queue/leases";

        if (!is_dir($this->jobsPath)) {
            mkdir($this->jobsPath, 0755, true);
        }
        if (!is_dir($this->leasesPath)) {
            mkdir($this->leasesPath, 0755, true);
        }
    }

    /**
     * Add a job to the queue.
     *
     * @param string $type    Job type (e.g., 'workflow:copilot_orchestrator')
     * @param array  $payload Job data
     *
     * @return string Job ID
     */
    public function enqueue(string $type, array $payload): string
    {
        $jobId = bin2hex(random_bytes(16));

        $job = [
            'id' => $jobId,
            'type' => $type,
            'payload' => $payload,
            'status' => 'queued',
            'createdAt' => date('c'),
            'attempts' => 0,
        ];

        $this->saveJob($jobId, $job);

        return $jobId;
    }

    /**
     * Get the next available job from the queue.
     *
     * @return array|null Job data or null if queue is empty
     */
    public function dequeue(): ?array
    {
        $files = glob("{$this->jobsPath}/*.json");
        sort($files); // FIFO order by filename

        foreach ($files as $file) {
            $jobId = basename($file, '.json');
            $lockFile = "{$this->leasesPath}/{$jobId}.lock";

            // Try to acquire exclusive lock (non-blocking)
            $fp = fopen($lockFile, 'c');
            if (!$fp) continue;

            if (flock($fp, LOCK_EX | LOCK_NB)) {
                $job = $this->loadJob($jobId);

                if ($job && $job['status'] === 'queued') {
                    // Mark as processing
                    $job['status'] = 'processing';
                    $job['startedAt'] = date('c');
                    $job['attempts']++;
                    $this->saveJob($jobId, $job);

                    // Keep the lock file handle in the job for later release
                    $job['_lock_fp'] = $fp;
                    $job['_lock_file'] = $lockFile;

                    return $job;
                }

                flock($fp, LOCK_UN);
            }

            fclose($fp);
        }

        return null;
    }

    /**
     * Mark a job as completed.
     */
    public function complete(string $jobId, array $result = []): void
    {
        $job = $this->loadJob($jobId);

        if ($job) {
            $job['status'] = 'completed';
            $job['result'] = $result;
            $job['completedAt'] = date('c');
            $this->saveJob($jobId, $job);
        }

        $this->releaseLock($jobId);
    }

    /**
     * Mark a job as failed.
     */
    public function fail(string $jobId, string $error): void
    {
        $job = $this->loadJob($jobId);

        if ($job) {
            $maxRetries = 3;

            if ($job['attempts'] < $maxRetries) {
                // Retry - reset to queued
                $job['status'] = 'queued';
                $job['lastError'] = $error;
            } else {
                // Max retries exceeded
                $job['status'] = 'failed';
                $job['error'] = $error;
                $job['failedAt'] = date('c');
            }

            $this->saveJob($jobId, $job);
        }

        $this->releaseLock($jobId);
    }

    /**
     * Get job status.
     */
    public function getStatus(string $jobId): ?array
    {
        return $this->loadJob($jobId);
    }

    /**
     * Get queue statistics.
     */
    public function getStats(): array
    {
        $files = glob("{$this->jobsPath}/*.json");
        $stats = [
            'total' => count($files),
            'queued' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0,
        ];

        foreach ($files as $file) {
            $job = json_decode(file_get_contents($file), true);
            $status = $job['status'] ?? 'unknown';
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        return $stats;
    }

    /**
     * Clean up old completed/failed jobs.
     *
     * @param int $maxAge Max age in seconds (default 24 hours)
     */
    public function cleanup(int $maxAge = 86400): int
    {
        $files = glob("{$this->jobsPath}/*.json");
        $cleaned = 0;
        $cutoff = time() - $maxAge;

        foreach ($files as $file) {
            $job = json_decode(file_get_contents($file), true);
            $status = $job['status'] ?? '';

            if (in_array($status, ['completed', 'failed'])) {
                $completedAt = strtotime($job['completedAt'] ?? $job['failedAt'] ?? '');
                if ($completedAt && $completedAt < $cutoff) {
                    unlink($file);
                    $cleaned++;
                }
            }
        }

        return $cleaned;
    }

    /**
     * Load job from file.
     */
    private function loadJob(string $jobId): ?array
    {
        $path = "{$this->jobsPath}/{$jobId}.json";

        if (!file_exists($path)) {
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Save job to file (atomic write).
     */
    private function saveJob(string $jobId, array $job): void
    {
        $path = "{$this->jobsPath}/{$jobId}.json";
        $tmp = $path . '.tmp.' . getmypid();

        file_put_contents($tmp, json_encode($job, JSON_PRETTY_PRINT));
        rename($tmp, $path);
    }

    /**
     * Release job lock.
     */
    private function releaseLock(string $jobId): void
    {
        $lockFile = "{$this->leasesPath}/{$jobId}.lock";

        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
    }
}
