import { useState, useCallback } from 'react';
import type { ChatMessage } from '../types';
import { delegatorService } from '../services/delegator';
import { executeAgent } from '../services/agent-gateway';

interface UseDelegatorResult {
  messages: ChatMessage[];
  isProcessing: boolean;
  error: Error | null;
  sendMessage: (content: string) => Promise<void>;
  clearMessages: () => void;
}

/**
 * Generate a unique message ID
 */
function generateId(): string {
  return `msg_${Date.now()}_${Math.random().toString(36).slice(2, 9)}`;
}

/**
 * Hook for managing chat and delegation
 */
export function useDelegator(): UseDelegatorResult {
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [isProcessing, setIsProcessing] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const sendMessage = useCallback(async (content: string) => {
    if (!content.trim()) return;

    setError(null);
    setIsProcessing(true);

    // Add user message
    const userMessage: ChatMessage = {
      id: generateId(),
      role: 'user',
      content: content.trim(),
      timestamp: new Date(),
    };

    setMessages((prev) => [...prev, userMessage]);

    try {
      // Delegate to appropriate agent using local LLM
      const delegation = await delegatorService.delegate(content);

      // Add assistant message with delegation info (include raw output for debugging)
      const rawDebug = delegation.rawOutput
        ? `\n\n---\n**Raw LLM output:** \`${delegation.rawOutput}\``
        : '';
      const delegationMessage: ChatMessage = {
        id: generateId(),
        role: 'assistant',
        content: `Routing to **${delegation.agent}** (confidence: ${(delegation.confidence * 100).toFixed(0)}%)\n\n*Reason: ${delegation.reason}*${rawDebug}`,
        timestamp: new Date(),
        delegation,
      };

      setMessages((prev) => [...prev, delegationMessage]);

      // Execute the delegated agent (mock)
      const agentResponse = await executeAgent(delegation);

      // Add agent response
      const responseMessage: ChatMessage = {
        id: generateId(),
        role: 'assistant',
        content: agentResponse,
        timestamp: new Date(),
        agentResponse: agentResponse,
      };

      setMessages((prev) => [...prev, responseMessage]);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Unknown error';
      setError(err instanceof Error ? err : new Error(errorMessage));

      // Add error message to chat
      const errorChatMessage: ChatMessage = {
        id: generateId(),
        role: 'system',
        content: `Error: ${errorMessage}`,
        timestamp: new Date(),
      };

      setMessages((prev) => [...prev, errorChatMessage]);
    } finally {
      setIsProcessing(false);
    }
  }, []);

  const clearMessages = useCallback(() => {
    setMessages([]);
    setError(null);
  }, []);

  return {
    messages,
    isProcessing,
    error,
    sendMessage,
    clearMessages,
  };
}
