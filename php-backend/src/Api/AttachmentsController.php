<?php
declare(strict_types=1);

namespace App\Api;

/**
 * Attachments Controller
 *
 * Handles file uploads, metadata storage, and file serving.
 * Files are stored in storage/attachments/ with metadata in JSON.
 */
class AttachmentsController
{
    private string $metaPath;
    private string $binPath;

    // Allowed MIME types
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'application/pdf' => 'pdf',
        'application/json' => 'json',
        'text/plain' => 'txt',
        'text/csv' => 'csv',
        'application/xml' => 'xml',
        'text/xml' => 'xml',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    ];

    // Max file size (10MB)
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;

    public function __construct()
    {
        $this->metaPath = STORAGE_PATH . '/attachments/meta';
        $this->binPath = STORAGE_PATH . '/attachments/bin';

        if (!is_dir($this->metaPath)) {
            mkdir($this->metaPath, 0755, true);
        }
        if (!is_dir($this->binPath)) {
            mkdir($this->binPath, 0755, true);
        }
    }

    /**
     * Upload a file.
     *
     * @param array $file $_FILES array element
     *
     * @return array Upload result with attachment ID
     */
    public function upload(array $file): array
    {
        // Validate upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Upload error: " . $this->getUploadError($file['error']));
        }

        // Validate file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \RuntimeException("File too large. Maximum size is " . (self::MAX_FILE_SIZE / 1024 / 1024) . "MB");
        }

        // Validate MIME type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            throw new \RuntimeException("File type not allowed: $mimeType");
        }

        // Generate attachment ID
        $attachmentId = bin2hex(random_bytes(16));
        $extension = self::ALLOWED_TYPES[$mimeType];
        $originalName = $file['name'];
        $safeName = $this->sanitizeFilename($originalName);

        // Store file
        $storedName = "{$attachmentId}_{$safeName}";
        $storedPath = "{$this->binPath}/{$storedName}";

        if (!move_uploaded_file($file['tmp_name'], $storedPath)) {
            throw new \RuntimeException("Failed to store uploaded file");
        }

        // Extract metadata for images
        $metadata = $this->extractMetadata($storedPath, $mimeType);

        // Store metadata
        $meta = [
            'id' => $attachmentId,
            'originalName' => $originalName,
            'storedName' => $storedName,
            'mimeType' => $mimeType,
            'extension' => $extension,
            'size' => $file['size'],
            'metadata' => $metadata,
            'createdAt' => date('c'),
        ];

        $this->saveMeta($attachmentId, $meta);

        return [
            'id' => $attachmentId,
            'filename' => $originalName,
            'mimeType' => $mimeType,
            'size' => $file['size'],
            'metadata' => $metadata,
            'url' => "/api/attachments/{$attachmentId}/download",
        ];
    }

    /**
     * Get attachment metadata.
     */
    public function get(string $attachmentId): ?array
    {
        return $this->loadMeta($attachmentId);
    }

    /**
     * Download/serve attachment file.
     */
    public function download(string $attachmentId): void
    {
        $meta = $this->loadMeta($attachmentId);

        if (!$meta) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Attachment not found']);
            return;
        }

        $filePath = "{$this->binPath}/{$meta['storedName']}";

        if (!file_exists($filePath)) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'File not found']);
            return;
        }

        // Set headers for download
        header('Content-Type: ' . $meta['mimeType']);
        header('Content-Length: ' . $meta['size']);
        header('Content-Disposition: inline; filename="' . $meta['originalName'] . '"');
        header('Cache-Control: public, max-age=86400');

        readfile($filePath);
    }

    /**
     * Delete attachment.
     */
    public function delete(string $attachmentId): bool
    {
        $meta = $this->loadMeta($attachmentId);

        if (!$meta) {
            return false;
        }

        // Delete file
        $filePath = "{$this->binPath}/{$meta['storedName']}";
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete metadata
        $metaPath = "{$this->metaPath}/{$attachmentId}.json";
        if (file_exists($metaPath)) {
            unlink($metaPath);
        }

        return true;
    }

    /**
     * List all attachments.
     */
    public function listAll(int $limit = 100, int $offset = 0): array
    {
        $files = glob("{$this->metaPath}/*.json");
        rsort($files); // Newest first by filename

        $total = count($files);
        $files = array_slice($files, $offset, $limit);

        $attachments = [];
        foreach ($files as $file) {
            $meta = json_decode(file_get_contents($file), true);
            if ($meta) {
                $attachments[] = [
                    'id' => $meta['id'],
                    'filename' => $meta['originalName'],
                    'mimeType' => $meta['mimeType'],
                    'size' => $meta['size'],
                    'createdAt' => $meta['createdAt'],
                ];
            }
        }

        return [
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'attachments' => $attachments,
        ];
    }

    /**
     * Load metadata from file.
     */
    private function loadMeta(string $attachmentId): ?array
    {
        $path = "{$this->metaPath}/{$attachmentId}.json";

        if (!file_exists($path)) {
            return null;
        }

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Save metadata to file.
     */
    private function saveMeta(string $attachmentId, array $meta): void
    {
        $path = "{$this->metaPath}/{$attachmentId}.json";
        $tmp = $path . '.tmp.' . getmypid();

        file_put_contents($tmp, json_encode($meta, JSON_PRETTY_PRINT));
        rename($tmp, $path);
    }

    /**
     * Extract metadata from file.
     */
    private function extractMetadata(string $filePath, string $mimeType): array
    {
        $metadata = [];

        // Extract image dimensions
        if (str_starts_with($mimeType, 'image/') && $mimeType !== 'image/svg+xml') {
            $imageInfo = @getimagesize($filePath);
            if ($imageInfo) {
                $metadata['width'] = $imageInfo[0];
                $metadata['height'] = $imageInfo[1];
                $metadata['aspectRatio'] = round($imageInfo[0] / $imageInfo[1], 3);
            }
        }

        // Extract EXIF for JPEG
        if ($mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($filePath);
            if ($exif) {
                $metadata['exif'] = [
                    'make' => $exif['Make'] ?? null,
                    'model' => $exif['Model'] ?? null,
                    'datetime' => $exif['DateTime'] ?? null,
                ];
            }
        }

        return $metadata;
    }

    /**
     * Sanitize filename for storage.
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove path components
        $filename = basename($filename);

        // Replace spaces and special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Limit length
        if (strlen($filename) > 100) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 95) . '.' . $ext;
        }

        return $filename;
    }

    /**
     * Get upload error message.
     */
    private function getUploadError(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE form directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
            default => 'Unknown upload error',
        };
    }
}
