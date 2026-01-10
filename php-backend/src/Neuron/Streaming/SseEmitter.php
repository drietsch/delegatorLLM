<?php
declare(strict_types=1);

namespace App\Neuron\Streaming;

/**
 * Server-Sent Events Emitter for streaming responses.
 *
 * Emits events in a line-prefixed format compatible with the React frontend:
 *   {prefix}:{json}\n
 *
 * Prefixes:
 *   0 = token (text chunk)
 *   9 = tool_call (tool invocation started)
 *   a = tool_result (tool execution result)
 *   w = workflow_step (workflow progress)
 *   e = error
 *   d = done (stream complete)
 */
class SseEmitter
{
    private bool $headersSent = false;
    private ?string $sessionId = null;
    private ?string $runId = null;

    public function __construct(?string $sessionId = null, ?string $runId = null)
    {
        $this->sessionId = $sessionId;
        $this->runId = $runId;
    }

    /**
     * Send SSE headers if not already sent.
     */
    public function sendHeaders(): void
    {
        if ($this->headersSent) {
            return;
        }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable nginx buffering

        // Disable output buffering
        while (ob_get_level()) {
            ob_end_flush();
        }

        $this->headersSent = true;
    }

    /**
     * Emit a generic event.
     *
     * @param string $type    Event type (token, tool_call, tool_result, workflow_step, error, done)
     * @param mixed  $payload Event data (will be JSON encoded)
     */
    public function emit(string $type, mixed $payload): void
    {
        $this->sendHeaders();

        $prefix = match ($type) {
            'token' => '0',
            'tool_call' => '9',
            'tool_result' => 'a',
            'workflow_step' => 'w',
            'state' => 's',
            'error' => 'e',
            'done' => 'd',
            default => 'x',
        };

        // For token events, payload is the raw text chunk
        if ($type === 'token') {
            echo $prefix . ':' . json_encode($payload) . "\n";
        } else {
            // For other events, wrap in envelope with metadata
            $envelope = $payload;
            if (is_array($payload)) {
                if ($this->sessionId) {
                    $envelope['sessionId'] = $this->sessionId;
                }
                if ($this->runId) {
                    $envelope['runId'] = $this->runId;
                }
            }
            echo $prefix . ':' . json_encode($envelope) . "\n";
        }

        flush();
    }

    /**
     * Emit a text token (streaming text content).
     */
    public function text(string $chunk): void
    {
        $this->emit('token', $chunk);
    }

    /**
     * Emit a tool call event (tool invocation started).
     */
    public function toolCall(string $toolCallId, string $toolName, array $args = []): void
    {
        $this->emit('tool_call', [
            'toolCallId' => $toolCallId,
            'toolName' => $toolName,
            'args' => $args,
        ]);
    }

    /**
     * Emit a tool result event (tool execution completed).
     */
    public function toolResult(string $toolCallId, mixed $result, ?string $error = null): void
    {
        $payload = [
            'toolCallId' => $toolCallId,
            'result' => $result,
        ];

        if ($error !== null) {
            $payload['error'] = $error;
        }

        $this->emit('tool_result', $payload);
    }

    /**
     * Emit a workflow step event.
     */
    public function workflowStep(string $nodeName, string $status, array $data = []): void
    {
        $this->emit('workflow_step', [
            'node' => $nodeName,
            'status' => $status,
            'data' => $data,
            'ts' => date('c'),
        ]);
    }

    /**
     * Emit a state update event.
     */
    public function state(array $state): void
    {
        $this->emit('state', $state);
    }

    /**
     * Emit an error event.
     */
    public function error(string $message, ?string $code = null, array $details = []): void
    {
        $payload = [
            'message' => $message,
            'ts' => date('c'),
        ];

        if ($code !== null) {
            $payload['code'] = $code;
        }

        if (!empty($details)) {
            $payload['details'] = $details;
        }

        $this->emit('error', $payload);
    }

    /**
     * Emit a done event (stream complete).
     */
    public function done(string $reason = 'stop', array $metadata = []): void
    {
        $payload = [
            'finishReason' => $reason,
            'ts' => date('c'),
        ];

        if (!empty($metadata)) {
            $payload = array_merge($payload, $metadata);
        }

        $this->emit('done', $payload);
    }

    /**
     * Set session ID for event envelopes.
     */
    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Set run ID for event envelopes.
     */
    public function setRunId(string $runId): self
    {
        $this->runId = $runId;
        return $this;
    }

    /**
     * Keep connection alive with a comment (useful for long-running streams).
     */
    public function keepAlive(): void
    {
        $this->sendHeaders();
        echo ": keepalive\n\n";
        flush();
    }
}
