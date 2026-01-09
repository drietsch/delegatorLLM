import { useState, useCallback } from 'react';
import { useRouter } from './useRouter';

export interface RoutingResult {
  query: string;
  agent: string;
  description: string;
  confidence: number;
  topMatches: { name: string; score: number }[];
}

export interface ChatMessage {
  key: string;
  role: 'user' | 'assistant';
  content: string;
}

const API_BASE = 'http://localhost:3001';

export function useAgentChat() {
  const { route, isReady: isRouterReady, loadingState } = useRouter();
  const [currentAgent, setCurrentAgent] = useState<string | null>(null);
  const [routingResult, setRoutingResult] = useState<RoutingResult | null>(null);
  const [isRouting, setIsRouting] = useState(false);
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  // Route the query locally (just routing, no execution)
  const routeQuery = useCallback(async (query: string): Promise<RoutingResult | null> => {
    if (!isRouterReady || isRouting) return null;

    setIsRouting(true);
    setError(null);

    try {
      const result = await route(query);
      setRoutingResult(result);
      return result;
    } catch (err) {
      console.error('Routing error:', err);
      setError(err instanceof Error ? err : new Error(String(err)));
      return null;
    } finally {
      setIsRouting(false);
    }
  }, [isRouterReady, isRouting, route]);

  // Execute the selected agent
  const executeAgent = useCallback(async (agent: string, query: string) => {
    if (isLoading) return;

    setCurrentAgent(agent);
    setIsLoading(true);
    setError(null);

    // Add user message
    const userMessage: ChatMessage = {
      key: `user_${Date.now()}`,
      role: 'user',
      content: query,
    };
    setMessages([userMessage]);

    try {
      const response = await fetch(`${API_BASE}/api/chat`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          agent,
          messages: [{ role: 'user', content: query }],
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP error: ${response.status}`);
      }

      // Parse streaming response
      const reader = response.body?.getReader();
      if (!reader) throw new Error('No response body');

      const decoder = new TextDecoder();
      let assistantContent = '';

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        const chunk = decoder.decode(value, { stream: true });
        const lines = chunk.split('\n').filter(Boolean);

        for (const line of lines) {
          const prefix = line[0];
          const data = line.slice(2);

          try {
            if (prefix === '0') {
              // Text chunk
              assistantContent += JSON.parse(data);
            }
          } catch {
            // Ignore parse errors
          }
        }

        // Update messages with current state
        setMessages([
          userMessage,
          {
            key: `assistant_${Date.now()}`,
            role: 'assistant',
            content: assistantContent,
          },
        ]);
      }

    } catch (err) {
      console.error('Execution error:', err);
      setError(err instanceof Error ? err : new Error(String(err)));
    } finally {
      setIsLoading(false);
    }
  }, [isLoading]);

  const clearChat = useCallback(() => {
    setMessages([]);
    setCurrentAgent(null);
    setError(null);
  }, []);

  const clearRouting = useCallback(() => {
    setRoutingResult(null);
  }, []);

  return {
    // Router state
    loadingState,
    isRouterReady,
    isRouting,
    routingResult,
    currentAgent,

    // Chat state
    messages,
    isLoading,
    error,

    // Actions
    routeQuery,
    executeAgent,
    clearChat,
    clearRouting,
  };
}
