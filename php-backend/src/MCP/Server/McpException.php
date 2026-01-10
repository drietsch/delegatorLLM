<?php
declare(strict_types=1);

namespace App\MCP\Server;

/**
 * MCP Exception
 *
 * Exception for MCP protocol errors.
 */
class McpException extends \Exception
{
    // JSON-RPC 2.0 error codes
    public const PARSE_ERROR = -32700;
    public const INVALID_REQUEST = -32600;
    public const METHOD_NOT_FOUND = -32601;
    public const INVALID_PARAMS = -32602;
    public const INTERNAL_ERROR = -32603;

    public function __construct(string $message, int $code = self::INTERNAL_ERROR, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
