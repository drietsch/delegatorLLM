<?php
declare(strict_types=1);

namespace App\MCP\Client;

/**
 * MCP Client Exception
 *
 * Exception for MCP client errors.
 */
class McpClientException extends \Exception
{
    public function __construct(string $message, int $code = -32603, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
