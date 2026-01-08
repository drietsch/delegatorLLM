/**
 * Function parameter schema for FunctionGemma
 */
export interface FunctionParameter {
  type: string;
  description: string;
}

/**
 * Function schema for FunctionGemma function calling
 */
export interface FunctionSchema {
  name: string;
  description: string;
  parameters: {
    type: string;
    properties: Record<string, FunctionParameter>;
    required?: string[];
  };
}

/**
 * Agent descriptor from the manifest
 */
export interface Agent {
  name: string;
  description: string;
  skills: string[];
  endpoint: string;
  function?: FunctionSchema;
}

/**
 * Result of delegation decision from the LLM
 */
export interface DelegationResult {
  agent: string;
  arguments: Record<string, unknown>;
  confidence: number;
  reason: string;
  rawOutput?: string; // For debugging
}

/**
 * Chat message in the conversation
 */
export interface ChatMessage {
  id: string;
  role: 'user' | 'assistant' | 'system';
  content: string;
  timestamp: Date;
  delegation?: DelegationResult;
  agentResponse?: string;
}

/**
 * Model loading progress
 */
export interface LoadingProgress {
  status: string;
  file?: string;
  progress?: number;
  loaded?: number;
  total?: number;
}

/**
 * Inference status states
 */
export type InferenceStatus =
  | 'idle'
  | 'loading'
  | 'ready'
  | 'processing'
  | 'error';

/**
 * Detected runtime backend
 */
export type RuntimeBackend = 'webgpu' | 'cpu';

/**
 * Agent manifest structure
 */
export interface AgentManifest {
  agents: Agent[];
}
