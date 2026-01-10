import React, { createContext, useContext, useReducer, useCallback, useEffect, useRef } from 'react';
import type { Session, SessionMessage, Notification, ToolCall, ToolResult } from '../types/session';
import { useRouter } from '../hooks/useRouter';
import { API_BASE } from '../config/api';

// Generate unique IDs
const generateId = () => `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

// State shape
interface MultiSessionState {
  sessions: Record<string, Session>;
  activeSessionId: string | null;
  notifications: Notification[];
  isRouterReady: boolean;
  routerLoadingState: {
    status: string;
    progress: number;
    message: string;
  };
}

// Action types
type Action =
  | { type: 'CREATE_SESSION'; payload: Session }
  | { type: 'DELETE_SESSION'; payload: string }
  | { type: 'SET_ACTIVE_SESSION'; payload: string | null }
  | { type: 'UPDATE_SESSION'; payload: { id: string; updates: Partial<Session> } }
  | { type: 'ADD_MESSAGE'; payload: { sessionId: string; message: SessionMessage } }
  | { type: 'UPDATE_MESSAGE'; payload: { sessionId: string; messageId: string; updates: Partial<SessionMessage> } }
  | { type: 'ADD_NOTIFICATION'; payload: Notification }
  | { type: 'DISMISS_NOTIFICATION'; payload: string }
  | { type: 'SET_ROUTER_READY'; payload: boolean }
  | { type: 'SET_ROUTER_LOADING_STATE'; payload: MultiSessionState['routerLoadingState'] };

// Reducer
function reducer(state: MultiSessionState, action: Action): MultiSessionState {
  switch (action.type) {
    case 'CREATE_SESSION':
      return {
        ...state,
        sessions: { ...state.sessions, [action.payload.id]: action.payload },
      };
    case 'DELETE_SESSION': {
      const { [action.payload]: _, ...rest } = state.sessions;
      return {
        ...state,
        sessions: rest,
        activeSessionId: state.activeSessionId === action.payload ? null : state.activeSessionId,
      };
    }
    case 'SET_ACTIVE_SESSION':
      return { ...state, activeSessionId: action.payload };
    case 'UPDATE_SESSION':
      return {
        ...state,
        sessions: {
          ...state.sessions,
          [action.payload.id]: {
            ...state.sessions[action.payload.id],
            ...action.payload.updates,
            updatedAt: Date.now(),
          },
        },
      };
    case 'ADD_MESSAGE':
      return {
        ...state,
        sessions: {
          ...state.sessions,
          [action.payload.sessionId]: {
            ...state.sessions[action.payload.sessionId],
            messages: [...state.sessions[action.payload.sessionId].messages, action.payload.message],
            updatedAt: Date.now(),
          },
        },
      };
    case 'UPDATE_MESSAGE': {
      const session = state.sessions[action.payload.sessionId];
      return {
        ...state,
        sessions: {
          ...state.sessions,
          [action.payload.sessionId]: {
            ...session,
            messages: session.messages.map((msg) =>
              msg.id === action.payload.messageId ? { ...msg, ...action.payload.updates } : msg
            ),
            updatedAt: Date.now(),
          },
        },
      };
    }
    case 'ADD_NOTIFICATION':
      return { ...state, notifications: [...state.notifications, action.payload] };
    case 'DISMISS_NOTIFICATION':
      return {
        ...state,
        notifications: state.notifications.filter((n) => n.id !== action.payload),
      };
    case 'SET_ROUTER_READY':
      return { ...state, isRouterReady: action.payload };
    case 'SET_ROUTER_LOADING_STATE':
      return { ...state, routerLoadingState: action.payload };
    default:
      return state;
  }
}

// Initial state
const initialState: MultiSessionState = {
  sessions: {},
  activeSessionId: null,
  notifications: [],
  isRouterReady: false,
  routerLoadingState: { status: 'idle', progress: 0, message: '' },
};

// Context type
interface MultiSessionContextType extends MultiSessionState {
  // Session lifecycle
  createSession: () => string;
  switchSession: (id: string) => void;
  deleteSession: (id: string) => void;
  minimizeToRail: () => void;

  // Messaging
  sendMessage: (content: string, sessionId?: string) => Promise<void>;

  // Notifications
  dismissNotification: (id: string) => void;
  markSessionRead: (id: string) => void;

  // Getters
  activeSession: Session | null;
  sessionList: Session[];
}

const MultiSessionContext = createContext<MultiSessionContextType | null>(null);

export function MultiSessionProvider({ children }: { children: React.ReactNode }) {
  const [state, dispatch] = useReducer(reducer, initialState);
  const { route, isReady: isRouterReady, loadingState: routerLoadingState } = useRouter();

  // Track active executions for background sessions
  const executionAbortControllers = useRef<Record<string, AbortController>>({});

  // Sync router state
  useEffect(() => {
    dispatch({ type: 'SET_ROUTER_READY', payload: isRouterReady });
  }, [isRouterReady]);

  useEffect(() => {
    dispatch({
      type: 'SET_ROUTER_LOADING_STATE',
      payload: {
        status: routerLoadingState.status,
        progress: routerLoadingState.progress,
        message: routerLoadingState.message || '',
      },
    });
  }, [routerLoadingState]);

  // Create a new session
  const createSession = useCallback((): string => {
    const id = generateId();
    const session: Session = {
      id,
      agent: null,
      status: 'idle',
      title: 'New Chat',
      messages: [],
      createdAt: Date.now(),
      updatedAt: Date.now(),
      hasUnread: false,
    };
    dispatch({ type: 'CREATE_SESSION', payload: session });
    dispatch({ type: 'SET_ACTIVE_SESSION', payload: id });
    return id;
  }, []);

  // Switch to a session
  const switchSession = useCallback((id: string) => {
    dispatch({ type: 'SET_ACTIVE_SESSION', payload: id });
    // Mark as read when switching to it
    dispatch({ type: 'UPDATE_SESSION', payload: { id, updates: { hasUnread: false } } });
  }, []);

  // Delete a session
  const deleteSession = useCallback((id: string) => {
    // Abort any ongoing execution
    if (executionAbortControllers.current[id]) {
      executionAbortControllers.current[id].abort();
      delete executionAbortControllers.current[id];
    }
    dispatch({ type: 'DELETE_SESSION', payload: id });
  }, []);

  // Minimize current session to rail (start new session)
  const minimizeToRail = useCallback(() => {
    const newId = generateId();
    const session: Session = {
      id: newId,
      agent: null,
      status: 'idle',
      title: 'New Chat',
      messages: [],
      createdAt: Date.now(),
      updatedAt: Date.now(),
      hasUnread: false,
    };
    dispatch({ type: 'CREATE_SESSION', payload: session });
    dispatch({ type: 'SET_ACTIVE_SESSION', payload: newId });
  }, []);

  // Send a message in the active session (or specified session)
  const sendMessage = useCallback(
    async (content: string, targetSessionId?: string) => {
      let sessionId = targetSessionId || state.activeSessionId;

      // Create a new session if none exists
      if (!sessionId) {
        sessionId = generateId();
        const newSession: Session = {
          id: sessionId,
          agent: null,
          status: 'idle',
          title: 'New Chat',
          messages: [],
          createdAt: Date.now(),
          updatedAt: Date.now(),
          hasUnread: false,
        };
        dispatch({ type: 'CREATE_SESSION', payload: newSession });
        dispatch({ type: 'SET_ACTIVE_SESSION', payload: sessionId });
      }

      if (!isRouterReady) {
        console.warn('sendMessage: Router not ready yet, waiting...');
        // Don't return - proceed anyway but router might fail
      }

      // Get session from state, or use defaults for newly created session
      const session = state.sessions[sessionId];
      const currentMessages = session?.messages || [];

      // Update title from first message
      const isFirstMessage = currentMessages.length === 0;
      const title = isFirstMessage ? content.slice(0, 50) + (content.length > 50 ? '...' : '') : (session?.title || 'New Chat');

      // Add user message
      const userMessage: SessionMessage = {
        id: generateId(),
        role: 'user',
        content,
        timestamp: Date.now(),
      };
      dispatch({ type: 'ADD_MESSAGE', payload: { sessionId, message: userMessage } });
      dispatch({
        type: 'UPDATE_SESSION',
        payload: { id: sessionId, updates: { status: 'routing', title } },
      });

      try {
        // Route the query
        const routingResult = await route(content);
        const agent = routingResult?.agent;

        if (!agent) {
          throw new Error('No agent found for this query');
        }

        dispatch({
          type: 'UPDATE_SESSION',
          payload: { id: sessionId, updates: { agent, status: 'streaming' } },
        });

        // Create assistant message placeholder
        const assistantMessageId = generateId();
        const assistantMessage: SessionMessage = {
          id: assistantMessageId,
          role: 'assistant',
          content: '',
          timestamp: Date.now(),
          toolCalls: [],
          toolResults: [],
          isStreaming: true,
        };
        dispatch({ type: 'ADD_MESSAGE', payload: { sessionId, message: assistantMessage } });

        // Create abort controller for this execution
        const abortController = new AbortController();
        executionAbortControllers.current[sessionId] = abortController;

        // Execute agent via streaming endpoint
        const response = await fetch(`${API_BASE}/api/chat/stream`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            agent,
            messages: [{ role: 'user', content }],
          }),
          signal: abortController.signal,
        });

        if (!response.ok) {
          throw new Error(`HTTP error: ${response.status}`);
        }

        const reader = response.body?.getReader();
        if (!reader) throw new Error('No response body');

        const decoder = new TextDecoder();
        let assistantContent = '';
        const toolCalls: ToolCall[] = [];
        const toolResults: ToolResult[] = [];

        while (true) {
          const { done, value } = await reader.read();
          if (done) break;

          const chunk = decoder.decode(value, { stream: true });
          const lines = chunk.split('\n').filter(Boolean);

          for (const line of lines) {
            const prefix = line[0];
            const jsonData = line.slice(2);

            try {
              switch (prefix) {
                case '0': {
                  // Text token
                  assistantContent += JSON.parse(jsonData);
                  dispatch({
                    type: 'UPDATE_MESSAGE',
                    payload: {
                      sessionId,
                      messageId: assistantMessageId,
                      updates: { content: assistantContent, toolCalls: [...toolCalls], toolResults: [...toolResults] },
                    },
                  });
                  break;
                }
                case '9': {
                  // Tool call start
                  const tc = JSON.parse(jsonData);
                  toolCalls.push({
                    id: tc.toolCallId,
                    name: tc.toolName,
                    args: tc.args,
                    status: 'executing',
                  });
                  dispatch({
                    type: 'UPDATE_MESSAGE',
                    payload: {
                      sessionId,
                      messageId: assistantMessageId,
                      updates: { content: assistantContent, toolCalls: [...toolCalls], toolResults: [...toolResults] },
                    },
                  });
                  break;
                }
                case 'a': {
                  // Tool result
                  const tr = JSON.parse(jsonData);
                  toolResults.push({
                    toolCallId: tr.toolCallId,
                    result: tr.result,
                    error: tr.error,
                  });
                  // Update the corresponding tool call status
                  const toolIndex = toolCalls.findIndex((t) => t.id === tr.toolCallId);
                  if (toolIndex >= 0) {
                    toolCalls[toolIndex].status = tr.error ? 'error' : 'completed';
                  }
                  dispatch({
                    type: 'UPDATE_MESSAGE',
                    payload: {
                      sessionId,
                      messageId: assistantMessageId,
                      updates: { content: assistantContent, toolCalls: [...toolCalls], toolResults: [...toolResults] },
                    },
                  });
                  break;
                }
                case 'e': {
                  // Error
                  const errData = JSON.parse(jsonData);
                  console.error('Stream error:', errData.message);
                  break;
                }
                case 'd': {
                  // Done - stream complete
                  // Final update with isStreaming: false
                  dispatch({
                    type: 'UPDATE_MESSAGE',
                    payload: {
                      sessionId,
                      messageId: assistantMessageId,
                      updates: {
                        content: assistantContent,
                        toolCalls: [...toolCalls],
                        toolResults: [...toolResults],
                        isStreaming: false,
                      },
                    },
                  });
                  break;
                }
              }
            } catch {
              // Ignore parse errors
            }
          }
        }

        // Check if this session is still the active one
        const isBackground = state.activeSessionId !== sessionId;

        dispatch({
          type: 'UPDATE_SESSION',
          payload: {
            id: sessionId,
            updates: {
              status: 'completed',
              hasUnread: isBackground,
            },
          },
        });

        // Show notification if completed in background
        if (isBackground) {
          const notification: Notification = {
            id: generateId(),
            sessionId,
            message: `${agent} completed`,
            type: 'success',
            timestamp: Date.now(),
          };
          dispatch({ type: 'ADD_NOTIFICATION', payload: notification });
        }

        // Cleanup abort controller
        delete executionAbortControllers.current[sessionId];
      } catch (error) {
        if ((error as Error).name === 'AbortError') {
          return; // Session was deleted, ignore
        }

        dispatch({
          type: 'UPDATE_SESSION',
          payload: {
            id: sessionId,
            updates: {
              status: 'error',
              error: (error as Error).message,
            },
          },
        });
      }
    },
    [state.activeSessionId, state.sessions, isRouterReady, route]
  );

  // Dismiss a notification
  const dismissNotification = useCallback((id: string) => {
    dispatch({ type: 'DISMISS_NOTIFICATION', payload: id });
  }, []);

  // Mark a session as read
  const markSessionRead = useCallback((id: string) => {
    dispatch({ type: 'UPDATE_SESSION', payload: { id, updates: { hasUnread: false } } });
  }, []);

  // Computed values
  const activeSession = state.activeSessionId ? state.sessions[state.activeSessionId] : null;
  const sessionList = Object.values(state.sessions).sort((a, b) => b.updatedAt - a.updatedAt);

  const contextValue: MultiSessionContextType = {
    ...state,
    createSession,
    switchSession,
    deleteSession,
    minimizeToRail,
    sendMessage,
    dismissNotification,
    markSessionRead,
    activeSession,
    sessionList,
  };

  return (
    <MultiSessionContext.Provider value={contextValue}>
      {children}
    </MultiSessionContext.Provider>
  );
}

export function useMultiSession() {
  const context = useContext(MultiSessionContext);
  if (!context) {
    throw new Error('useMultiSession must be used within MultiSessionProvider');
  }
  return context;
}
