import React, { createContext, useContext, useReducer, useCallback, useEffect, useRef } from 'react';
import type { Session, SessionMessage, Notification } from '../types/session';
import { useRouter } from '../hooks/useRouter';

const API_BASE = 'http://localhost:3001';

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
  | { type: 'UPDATE_MESSAGE'; payload: { sessionId: string; messageId: string; content: string } }
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
              msg.id === action.payload.messageId ? { ...msg, content: action.payload.content } : msg
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
  sendMessage: (content: string) => Promise<void>;

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

  // Send a message in the active session
  const sendMessage = useCallback(
    async (content: string) => {
      const sessionId = state.activeSessionId;
      if (!sessionId || !isRouterReady) return;

      const session = state.sessions[sessionId];
      if (!session) return;

      // Update title from first message
      const isFirstMessage = session.messages.length === 0;
      const title = isFirstMessage ? content.slice(0, 50) + (content.length > 50 ? '...' : '') : session.title;

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
        };
        dispatch({ type: 'ADD_MESSAGE', payload: { sessionId, message: assistantMessage } });

        // Create abort controller for this execution
        const abortController = new AbortController();
        executionAbortControllers.current[sessionId] = abortController;

        // Execute agent
        const response = await fetch(`${API_BASE}/api/chat`, {
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
                assistantContent += JSON.parse(data);
                dispatch({
                  type: 'UPDATE_MESSAGE',
                  payload: { sessionId, messageId: assistantMessageId, content: assistantContent },
                });
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
