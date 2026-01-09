// Session and Agent state types for multi-session management

export type SessionStatus =
  | 'idle'        // Ready for input, no active task
  | 'routing'     // Finding the right agent
  | 'working'     // Agent is processing (async background task)
  | 'streaming'   // Receiving response in real-time
  | 'completed'   // Task finished, results ready
  | 'error';      // Something went wrong

export interface SessionMessage {
  id: string;
  role: 'user' | 'assistant' | 'system';
  content: string;
  timestamp: number;
}

export interface Session {
  id: string;
  agent: string | null;
  status: SessionStatus;
  title: string;                  // Query preview for tooltip
  messages: SessionMessage[];
  createdAt: number;
  updatedAt: number;
  hasUnread: boolean;             // Unseen results → green dot
  progress?: number;              // 0-100 for progress indicator
  estimatedTime?: string;         // "~2 mins"
  error?: string;
}

export interface Notification {
  id: string;
  sessionId: string;
  message: string;
  type: 'success' | 'error';
  timestamp: number;
}

// Agent info from backend
export interface Agent {
  name: string;
  description: string;
  skills: string[];
}
