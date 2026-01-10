<?php
declare(strict_types=1);

namespace App\Api;

use App\Neuron\AgentFactory;
use App\Neuron\Streaming\SseEmitter;
use App\Persistence\SessionStore;
use App\Persistence\MessageStore;
use App\Persistence\ToolLogStore;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Chat\Messages\ToolCallMessage;
use Throwable;

/**
 * Chat Controller
 *
 * Handles chat requests with streaming and non-streaming modes.
 * Integrates with Neuron agents for AI-powered responses.
 */
class ChatController
{
    private SessionStore $sessions;
    private MessageStore $messages;
    private ToolLogStore $toolLogs;

    public function __construct()
    {
        $this->sessions = new SessionStore();
        $this->messages = new MessageStore();
        $this->toolLogs = new ToolLogStore();
    }

    /**
     * Handle non-streaming chat request.
     *
     * @param array $request Request data with agent, messages, session_id, etc.
     * @return array Response data
     */
    public function handle(array $request): array
    {
        $agentId = $request['agent'] ?? 'copilot';
        $userMessages = $request['messages'] ?? [];
        $sessionId = $request['session_id'] ?? null;
        $provider = $request['provider'] ?? null;
        $model = $request['model'] ?? null;
        $ragOptions = $request['rag'] ?? null;

        try {
            // Get or create session
            if ($sessionId) {
                $session = $this->sessions->get($sessionId);
                if (!$session) {
                    $sessionId = $this->sessions->create($agentId);
                }
            } else {
                $sessionId = $this->sessions->create($agentId);
            }

            // Store user message
            $userContent = $userMessages[0]['content'] ?? '';
            $this->messages->append($sessionId, [
                'role' => 'user',
                'content' => $userContent,
            ]);

            // Create agent and get response
            $agent = AgentFactory::create($agentId, $provider, $model);
            $response = $agent->chat(new UserMessage($userContent));
            $content = $response->getContent();

            // Store assistant response
            $this->messages->append($sessionId, [
                'role' => 'assistant',
                'content' => $content,
            ]);

            return [
                'session_id' => $sessionId,
                'agent' => $agentId,
                'content' => $content,
                'finish_reason' => 'stop',
            ];
        } catch (Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'code' => 'chat_error',
            ];
        }
    }

    /**
     * Handle streaming chat request.
     *
     * Streams tokens and tool calls as they are generated.
     *
     * @param array $request Request data
     */
    public function stream(array $request): void
    {
        $agentId = $request['agent'] ?? 'copilot';
        $userMessages = $request['messages'] ?? [];
        $sessionId = $request['session_id'] ?? null;
        $provider = $request['provider'] ?? null;
        $model = $request['model'] ?? null;
        $ragOptions = $request['rag'] ?? null;

        // Initialize SSE emitter
        $emitter = new SseEmitter($sessionId);

        try {
            // Get or create session
            if ($sessionId) {
                $session = $this->sessions->get($sessionId);
                if (!$session) {
                    $sessionId = $this->sessions->create($agentId);
                    $emitter->setSessionId($sessionId);
                }
            } else {
                $sessionId = $this->sessions->create($agentId);
                $emitter->setSessionId($sessionId);
            }

            // Store user message
            $userContent = $userMessages[0]['content'] ?? '';
            $this->messages->append($sessionId, [
                'role' => 'user',
                'content' => $userContent,
            ]);

            // Create agent
            $agent = AgentFactory::create($agentId, $provider, $model);

            // Stream response
            $fullContent = '';
            $message = new UserMessage($userContent);

            foreach ($agent->stream($message) as $chunk) {
                if ($chunk instanceof ToolCallMessage) {
                    // Handle tool calls
                    foreach ($chunk->getTools() as $tool) {
                        $toolCallId = $tool->getCallId();
                        $toolName = $tool->getName();
                        $toolInputs = $tool->getInputs();
                        $toolResult = $tool->getResult();

                        // Emit tool call
                        $emitter->toolCall($toolCallId, $toolName, $toolInputs);

                        // Log tool call
                        $this->toolLogs->append($sessionId, [
                            'toolCallId' => $toolCallId,
                            'tool' => $toolName,
                            'input' => $toolInputs,
                            'output' => $toolResult,
                            'status' => 'ok',
                        ]);

                        // Emit tool result
                        $emitter->toolResult($toolCallId, $toolResult);
                    }
                } else {
                    // Text chunk
                    $fullContent .= $chunk;
                    $emitter->text($chunk);
                }
            }

            // Store assistant response
            $this->messages->append($sessionId, [
                'role' => 'assistant',
                'content' => $fullContent,
            ]);

            // Signal completion
            $emitter->done('stop', [
                'session_id' => $sessionId,
            ]);
        } catch (Throwable $e) {
            $emitter->error($e->getMessage(), 'stream_error');
            $emitter->done('error');
        }
    }

    /**
     * Get chat history for a session.
     */
    public function history(string $sessionId): array
    {
        $session = $this->sessions->get($sessionId);
        if (!$session) {
            return ['error' => 'Session not found', 'code' => 'not_found'];
        }

        $messages = $this->messages->getAll($sessionId);

        return [
            'session_id' => $sessionId,
            'agent' => $session['agentId'] ?? null,
            'messages' => $messages,
            'created_at' => $session['createdAt'] ?? null,
        ];
    }

    /**
     * Get tool call logs for a session.
     */
    public function tools(string $sessionId): array
    {
        $session = $this->sessions->get($sessionId);
        if (!$session) {
            return ['error' => 'Session not found', 'code' => 'not_found'];
        }

        $toolCalls = $this->toolLogs->getAll($sessionId);

        return [
            'session_id' => $sessionId,
            'tool_calls' => $toolCalls,
        ];
    }
}
