import { pipeline, env, cos_sim } from '@huggingface/transformers';
import type { DelegationResult, LoadingProgress, RuntimeBackend, Agent } from '../types';
import { detectBackend } from './backend-detector';
import agentManifest from '../config/agents.json';

// Configure Transformers.js environment
env.allowLocalModels = false;
env.useBrowserCache = true;

// Using a multilingual embedding model for semantic similarity
// Supports 50+ languages including German, French, Spanish, Chinese, etc.
const EMBEDDING_MODEL_ID = 'Xenova/multilingual-e5-small';

/**
 * Create a searchable text representation of an agent
 * E5 models expect "passage: " prefix for documents
 */
function getAgentSearchText(agent: Agent): string {
  const text = `${agent.name.replace(/_/g, ' ')} ${agent.description} ${agent.skills.join(' ')}`;
  return `passage: ${text}`;
}

/**
 * Format user query for E5 model
 * E5 models expect "query: " prefix for queries
 */
function formatQuery(query: string): string {
  return `query: ${query}`;
}

/**
 * Delegator Service - uses embeddings for semantic similarity routing
 */
class DelegatorService {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private embedder: any = null;
  private backend: RuntimeBackend = 'cpu';
  private isInitializing = false;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private agentEmbeddings: Map<string, any> = new Map();

  /**
   * Check if the model is ready for inference
   */
  get isReady(): boolean {
    return this.embedder !== null && this.agentEmbeddings.size > 0;
  }

  /**
   * Get the current runtime backend
   */
  get currentBackend(): RuntimeBackend {
    return this.backend;
  }

  /**
   * Get available agents
   */
  get agents(): Agent[] {
    return agentManifest.agents as Agent[];
  }

  /**
   * Initialize the delegator with embedding model
   */
  async initialize(
    onProgress?: (progress: LoadingProgress) => void
  ): Promise<void> {
    if (this.embedder || this.isInitializing) {
      return;
    }

    this.isInitializing = true;

    try {
      // Detect best backend
      this.backend = await detectBackend();
      onProgress?.({ status: `Detected ${this.backend} backend, loading embedding model...` });

      // Configure pipeline options
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const pipelineOptions: any = {
        progress_callback: (progress: { status: string; file?: string; progress?: number; loaded?: number; total?: number }) => {
          if (progress.status === 'progress' && progress.file) {
            onProgress?.({
              status: `Loading ${progress.file}`,
              file: progress.file,
              progress: progress.progress,
              loaded: progress.loaded,
              total: progress.total,
            });
          } else if (progress.status) {
            onProgress?.({ status: progress.status });
          }
        },
      };

      // Use WebGPU if available for faster embeddings
      if (this.backend === 'webgpu') {
        pipelineOptions.device = 'webgpu';
      }

      // Initialize the feature extraction (embedding) pipeline
      this.embedder = await pipeline('feature-extraction', EMBEDDING_MODEL_ID, pipelineOptions);

      onProgress?.({ status: 'Computing agent embeddings...' });

      // Pre-compute embeddings for all agents
      const agents = agentManifest.agents as Agent[];
      for (const agent of agents) {
        const searchText = getAgentSearchText(agent);
        const embedding = await this.embedder(searchText, { pooling: 'mean', normalize: true });
        this.agentEmbeddings.set(agent.name, embedding);
      }

      onProgress?.({ status: 'Model loaded successfully!' });
      console.log(`[Delegator] Loaded embeddings for ${this.agentEmbeddings.size} agents`);
    } catch (error) {
      console.error('[Delegator] Initialization failed:', error);
      throw error;
    } finally {
      this.isInitializing = false;
    }
  }

  /**
   * Delegate a user request to the appropriate agent using semantic similarity
   */
  async delegate(userInput: string): Promise<DelegationResult> {
    if (!this.embedder || this.agentEmbeddings.size === 0) {
      throw new Error('Delegator not initialized. Call initialize() first.');
    }

    console.log('[Delegator] User input:', userInput);

    try {
      // Embed the user query (with E5 prefix)
      const formattedQuery = formatQuery(userInput);
      const queryEmbedding = await this.embedder(formattedQuery, { pooling: 'mean', normalize: true });

      // Find the most similar agent
      let bestAgent = '';
      let bestScore = -1;
      const scores: Array<{ agent: string; score: number }> = [];

      for (const [agentName, agentEmbedding] of this.agentEmbeddings) {
        const score = cos_sim(queryEmbedding.data, agentEmbedding.data);
        scores.push({ agent: agentName, score });

        if (score > bestScore) {
          bestScore = score;
          bestAgent = agentName;
        }
      }

      // Sort scores for logging
      scores.sort((a, b) => b.score - a.score);
      console.log('[Delegator] Top 5 matches:', scores.slice(0, 5));

      // Get the agent details for the reason
      const agent = this.agents.find(a => a.name === bestAgent);
      const agentDescription = agent?.description.split('.')[0] || 'Best semantic match';

      // Normalize confidence score (cosine similarity ranges from -1 to 1, we map to 0-1)
      // For this model, typical scores are 0.2-0.6 for good matches
      const confidence = Math.min(1, Math.max(0, (bestScore + 0.2) * 1.2));

      return {
        agent: bestAgent,
        arguments: { query: userInput },
        confidence: Number(confidence.toFixed(2)),
        reason: agentDescription,
        rawOutput: `Similarity: ${bestScore.toFixed(3)} | Top matches: ${scores.slice(0, 3).map(s => `${s.agent}(${s.score.toFixed(2)})`).join(', ')}`,
      };
    } catch (error) {
      console.error('[Delegator] Inference failed:', error);
      throw error;
    }
  }

  /**
   * Dispose of the model and free resources
   */
  dispose(): void {
    this.embedder = null;
    this.agentEmbeddings.clear();
  }
}

// Export singleton instance
export const delegatorService = new DelegatorService();
