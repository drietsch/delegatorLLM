<?php
declare(strict_types=1);

/**
 * Bootstrap file for Neuron AI Backend
 *
 * Handles autoloading, environment setup, and configuration initialization.
 */

// Load environment variables from .env if available
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Remove quotes if present
        if (preg_match('/^["\'](.*)["\']\s*$/', $value, $m)) {
            $value = $m[1];
        }

        if (!getenv($key)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Register PSR-4 autoloader for App namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Ensure storage directories exist
$storagePath = getenv('STORAGE_PATH') ?: __DIR__ . '/../storage';
$storageDirs = [
    'sessions',
    'messages',
    'workflows/runs',
    'workflows/events',
    'tools',
    'rag/collections',
    'rag/index',
    'queue/jobs',
    'queue/leases',
    'attachments/meta',
    'attachments/bin',
    'pimcore_shadow',
];

foreach ($storageDirs as $dir) {
    $path = "$storagePath/$dir";
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// Define storage path constant for easy access
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', $storagePath);
}

// Define base path constant
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
