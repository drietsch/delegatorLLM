import { pipeline, FeatureExtractionPipeline } from '@huggingface/transformers';

const API_BASE = 'http://localhost:3001';
const MODEL_ID = 'Xenova/paraphrase-multilingual-MiniLM-L12-v2';
const CHUNKING_ID = 'agents:v1';
const DIMS = 384;

interface Agent {
  name: string;
  description: string;
  skills: string[];
}

interface ChunkData {
  i: number;
  meta: { agent: string };
  text: string;
  emb_b64: string;
}

interface EmbeddingsBundle {
  build_id: string;
  model_id: string;
  dims: number;
  dtype: string;
  chunking_id: string;
  file_fingerprint: string;
  chunks: ChunkData[];
}

interface IndexedAgent {
  agent: Agent;
  vector: number[];
}

interface WorkerState {
  extractor: FeatureExtractionPipeline | null;
  agentIndex: IndexedAgent[];
  isReady: boolean;
}

const state: WorkerState = {
  extractor: null,
  agentIndex: [],
  isReady: false,
};

// Canonical JSON: sort keys recursively, stringify without whitespace
function canonicalize(obj: unknown): string {
  if (obj === null || typeof obj !== 'object') {
    return JSON.stringify(obj);
  }
  if (Array.isArray(obj)) {
    return '[' + obj.map(canonicalize).join(',') + ']';
  }
  const sorted = Object.keys(obj as Record<string, unknown>).sort();
  const pairs = sorted.map(k => `${JSON.stringify(k)}:${canonicalize((obj as Record<string, unknown>)[k])}`);
  return '{' + pairs.join(',') + '}';
}

// SHA-256 hash
async function sha256(text: string): Promise<string> {
  const encoder = new TextEncoder();
  const data = encoder.encode(text);
  const hashBuffer = await crypto.subtle.digest('SHA-256', data);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

// Base64 encode/decode for float32 arrays
function float32ToBase64(arr: number[]): string {
  const float32 = new Float32Array(arr);
  const uint8 = new Uint8Array(float32.buffer);
  let binary = '';
  for (let i = 0; i < uint8.length; i++) {
    binary += String.fromCharCode(uint8[i]);
  }
  return btoa(binary);
}

function base64ToFloat32(b64: string): number[] {
  const binary = atob(b64);
  const uint8 = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    uint8[i] = binary.charCodeAt(i);
  }
  const float32 = new Float32Array(uint8.buffer);
  return Array.from(float32);
}

// Cosine similarity (vectors assumed normalized)
function dotProduct(a: number[], b: number[]): number {
  let sum = 0;
  for (let i = 0; i < a.length; i++) {
    sum += a[i] * b[i];
  }
  return sum;
}

async function init() {
  try {
    // Step 1: Fetch agents
    self.postMessage({ type: 'status', status: 'loading', message: 'Loading agent definitions...' });
    const response = await fetch(`${API_BASE}/api/agents`);
    const agentsData = await response.json();
    const agents: Agent[] = agentsData.agents || agentsData;

    // Step 2: Compute build_id
    self.postMessage({ type: 'status', status: 'loading', message: 'Computing cache key...' });
    const canonicalJson = canonicalize(agentsData);
    const fileFingerprint = await sha256(canonicalJson);
    const buildId = await sha256(`${MODEL_ID}\n${CHUNKING_ID}\n${fileFingerprint}`);

    // Step 3: Try to fetch cached bundle
    self.postMessage({ type: 'status', status: 'loading', message: 'Checking embeddings cache...' });
    const cacheResponse = await fetch(`${API_BASE}/embeddings/${buildId}`);

    let bundle: EmbeddingsBundle | null = null;

    if (cacheResponse.ok) {
      // Cache hit!
      self.postMessage({ type: 'status', status: 'loading', message: 'Loading cached embeddings...' });
      bundle = await cacheResponse.json();
    } else {
      // Cache miss - need to generate embeddings
      self.postMessage({ type: 'status', status: 'loading', message: 'Loading embedding model...' });

      // @ts-expect-error - pipeline return type is too complex
      state.extractor = await pipeline('feature-extraction', MODEL_ID, {
        progress_callback: (progress: { status: string; progress?: number; file?: string }) => {
          if (progress.status === 'progress' && progress.progress !== undefined) {
            self.postMessage({
              type: 'progress',
              progress: progress.progress,
              message: `Loading ${progress.file || 'model'}...`,
            });
          }
        },
      });

      self.postMessage({ type: 'status', status: 'loading', message: 'Generating embeddings...' });

      const chunks: ChunkData[] = [];
      for (let i = 0; i < agents.length; i++) {
        const agent = agents[i];
        const text = `${agent.name}: ${agent.description} Skills: ${agent.skills.join(', ')}`;
        const output = await state.extractor(text, { pooling: 'mean', normalize: true });
        const vector = Array.from(output.data as Float32Array);

        chunks.push({
          i,
          meta: { agent: agent.name },
          text,
          emb_b64: float32ToBase64(vector),
        });

        self.postMessage({
          type: 'progress',
          progress: ((i + 1) / agents.length) * 100,
          message: `Embedding ${i + 1}/${agents.length}: ${agent.name}`,
        });
      }

      bundle = {
        build_id: buildId,
        model_id: MODEL_ID,
        dims: DIMS,
        dtype: 'float32',
        chunking_id: CHUNKING_ID,
        file_fingerprint: fileFingerprint,
        chunks,
      };

      // Store bundle on server
      self.postMessage({ type: 'status', status: 'loading', message: 'Caching embeddings...' });
      await fetch(`${API_BASE}/embeddings/${buildId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(bundle),
      });
    }

    // Step 4: Build agent index from bundle
    self.postMessage({ type: 'status', status: 'loading', message: 'Building index...' });
    for (const chunk of bundle!.chunks) {
      const agent = agents.find(a => a.name === chunk.meta.agent);
      if (agent) {
        state.agentIndex.push({
          agent,
          vector: base64ToFloat32(chunk.emb_b64),
        });
      }
    }

    // Step 5: Load model for query embedding (if not already loaded)
    if (!state.extractor) {
      self.postMessage({ type: 'status', status: 'loading', message: 'Loading embedding model for queries...' });
      state.extractor = await pipeline('feature-extraction', MODEL_ID, {
        progress_callback: (progress: { status: string; progress?: number; file?: string }) => {
          if (progress.status === 'progress' && progress.progress !== undefined) {
            self.postMessage({
              type: 'progress',
              progress: progress.progress,
              message: `Loading ${progress.file || 'model'}...`,
            });
          }
        },
      });
    }

    state.isReady = true;
    self.postMessage({
      type: 'status',
      status: 'ready',
      message: `Ready - ${agents.length} agents indexed`,
    });

  } catch (error) {
    const errorMessage = error instanceof Error ? error.message : String(error);
    console.error('Initialization error:', errorMessage, error);
    self.postMessage({ type: 'error', error: errorMessage });
  }
}

async function handleQuery(query: string) {
  if (!state.isReady || !state.extractor) {
    self.postMessage({ type: 'error', error: 'System not ready' });
    return;
  }

  try {
    self.postMessage({ type: 'processing', step: 'routing' });

    // Embed query (normalized)
    const output = await state.extractor(query, { pooling: 'mean', normalize: true });
    const queryVector = Array.from(output.data as Float32Array);

    // Find best match using dot product (vectors are normalized)
    let bestMatch: IndexedAgent | null = null;
    let bestScore = -1;
    const allScores: { name: string; score: number }[] = [];

    for (const indexed of state.agentIndex) {
      const score = dotProduct(queryVector, indexed.vector);
      allScores.push({ name: indexed.agent.name, score });
      if (score > bestScore) {
        bestScore = score;
        bestMatch = indexed;
      }
    }

    allScores.sort((a, b) => b.score - a.score);
    const topMatches = allScores.slice(0, 5);

    if (!bestMatch) {
      throw new Error('No suitable agent found');
    }

    self.postMessage({
      type: 'result',
      data: {
        query,
        agent: bestMatch.agent.name,
        description: bestMatch.agent.description,
        confidence: bestScore,
        topMatches,
      },
    });

  } catch (error) {
    console.error('Query processing error:', error);
    self.postMessage({ type: 'error', error: String(error) });
  }
}

self.onmessage = (e) => {
  const { cmd, text } = e.data;

  if (cmd === 'init') {
    init();
  } else if (cmd === 'query') {
    handleQuery(text);
  }
};
