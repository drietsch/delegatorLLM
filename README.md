# Agent Delegator

A proof-of-concept demonstrating **browser-based semantic routing** to specialized AI agents using multilingual embeddings that run entirely in WebAssembly.

## Overview

Agent Delegator showcases how natural language queries can be automatically routed to the most appropriate AI agent without requiring a server-side LLM for the routing decision. The routing intelligence runs **100% client-side** using a compact multilingual embedding model compiled to WebAssembly.

```
User Query: "Translate hello to German"
    │
    ▼
┌─────────────────────────────────────────┐
│  Browser (WebAssembly + Web Worker)     │
│  ┌───────────────────────────────────┐  │
│  │ MiniLM Multilingual Embeddings    │  │
│  │ (384 dimensions, ~46MB)           │  │
│  └───────────────────────────────────┘  │
│              │                          │
│              ▼                          │
│  ┌───────────────────────────────────┐  │
│  │ Cosine Similarity Search          │  │
│  │ Against Pre-computed Agent        │  │
│  │ Embeddings                        │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
    │
    ▼
Best Match: "translation_agent" (confidence: 0.87)
```

## Architecture

### Core Components

```
┌─────────────────────────────────────────────────────────────────────┐
│                           FRONTEND (React + Vite)                    │
├─────────────────────┬────────────────────┬──────────────────────────┤
│                     │                    │                          │
│  Asset Tree (DAM)   │   Power Strip      │      Chat Stage          │
│  - Folder hierarchy │   - Session tokens │  - Message bubbles       │
│  - Drag & drop      │   - Status dots    │  - Sender with routing   │
│  - Multi-select     │   - Background     │  - Attachment chips      │
│                     │     notifications  │  - Agent confidence      │
│                     │                    │                          │
├─────────────────────┴────────────────────┴──────────────────────────┤
│                                                                      │
│                        Web Worker (router.worker.ts)                 │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │  @huggingface/transformers                                      │ │
│  │  ├── Model: Xenova/paraphrase-multilingual-MiniLM-L12-v2       │ │
│  │  ├── Task: Feature Extraction (384-dim embeddings)              │ │
│  │  └── Runtime: ONNX via WebAssembly                              │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                      │
└──────────────────────────────┬───────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                     BACKEND (PHP Mock Server)                        │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  GET  /api/agents           → Returns agents.json                   │
│  GET  /embeddings/{id}      → Fetch cached embedding bundle          │
│  PUT  /embeddings/{id}      → Store computed embeddings              │
│  POST /api/chat             → Mock streaming agent response          │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### Routing Pipeline

1. **Initialization**
   - Web Worker loads the MiniLM model via `@huggingface/transformers`
   - Model weights are fetched and compiled to WebAssembly (~46MB)
   - Agent definitions are fetched from `/api/agents`
   - Each agent's description + skills are embedded into 384-dim vectors
   - Embeddings are cached server-side (keyed by SHA-256 of agents.json)

2. **Query Routing**
   - User types a query in the Sender component
   - Query is embedded using the same MiniLM model
   - Cosine similarity is computed against all agent embeddings
   - Best match is returned with confidence score

3. **Agent Execution**
   - Selected agent name is sent to `/api/chat`
   - Backend streams a mock response (simulating real agent execution)
   - Response is rendered in real-time using Ant Design X Bubble component

### Key Technologies

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Embeddings** | `@huggingface/transformers` | Browser-native ML inference via ONNX/WASM |
| **Model** | `Xenova/paraphrase-multilingual-MiniLM-L12-v2` | 50+ language support, 384 dimensions |
| **UI Framework** | Ant Design + Ant Design X | Chat bubbles, sender, prompts |
| **State Management** | React Context + useReducer | Multi-session chat state |
| **Build Tool** | Vite | Fast HMR, worker bundling |
| **Backend** | PHP-FPM + Nginx | Lightweight mock server |
| **Deployment** | Docker + Fly.io | Single container with frontend + backend |

## Agent Configuration

Agents are defined in `agents.json`. Each agent has:

```json
{
  "name": "translation_agent",
  "description": "Translates text between languages using neural machine translation...",
  "skills": [
    "translate",
    "language conversion",
    "multilingual",
    "localization"
  ],
  "endpoint": "/api/copilot/translation",
  "function": {
    "name": "translate",
    "parameters": {
      "type": "object",
      "properties": {
        "text": { "type": "string" },
        "target_language": { "type": "string" }
      }
    }
  }
}
```

The routing decision is based on semantic similarity between:
- **Query**: User's natural language input
- **Agent Profile**: `"{name}: {description} Skills: {skills.join(', ')}"`

### Embedding Cache Strategy

To avoid re-computing embeddings on every page load:

1. **Build ID** = SHA-256 of `model_id + chunking_version + file_fingerprint`
2. **File Fingerprint** = SHA-256 of canonicalized `agents.json`
3. On init, worker checks `GET /embeddings/{build_id}`
4. Cache hit → Load pre-computed vectors
5. Cache miss → Compute embeddings, store via `PUT /embeddings/{build_id}`

## Features

### Multi-Session Chat

- **Power Strip**: Vertical rail showing active sessions as tokens
- **Background Execution**: Sessions continue processing when minimized
- **Status Indicators**: Colored dots show session state (routing/streaming/completed/error)
- **Notifications**: Toast pills when background sessions complete

### DAM Integration (POC)

- **Asset Tree**: Hierarchical folder structure with thumbnails
- **Drag & Drop**: Single/multi-select assets or entire folders
- **Attachment Chips**: Visual preview of selected assets in sender
- **Metadata Inclusion**: Attachment info is appended to messages

### Real-time Routing Feedback

- **Auto Mode**: Routes as you type (debounced 300ms)
- **Manual Mode**: Override with specific agent selection
- **Confidence Display**: Color-coded percentage (green >80%, yellow >50%, red <50%)

## Development

### Prerequisites

- Node.js 20+
- PHP 8.2+ with Composer (for backend)

### Local Development

```bash
# Install dependencies
npm install

# Start frontend (port 5174)
npm run dev

# Start PHP backend (port 3001) - in another terminal
cd php-backend
php -S localhost:3001 index.php
```

### Production Build

```bash
npm run build
```

### Docker Build

```bash
docker build -t agent-delegator .
docker run -p 8080:8080 agent-delegator
```

## Deployment

### Fly.io

The app is configured for Fly.io deployment with auto-deploy from GitHub:

```bash
fly deploy
```

Configuration in `fly.toml`:
- Region: Frankfurt (fra)
- Memory: 1GB
- Auto-stop/start enabled

### GitHub Pages

For static deployment (frontend only):

```bash
GITHUB_PAGES=true npm run build
```

This sets `base: '/delegatorLLM/'` in the Vite config.

## Project Structure

```
├── src/
│   ├── components/
│   │   ├── AssetTree/        # DAM tree with drag support
│   │   ├── ChatStage/        # Chat UI, sender, attachments
│   │   ├── Layout/           # PowerStrip layout container
│   │   └── PowerStrip/       # Session tokens, new session button
│   ├── context/
│   │   └── MultiSessionContext.tsx  # Global state management
│   ├── data/
│   │   └── mockAssets.ts     # Sample DAM structure
│   ├── hooks/
│   │   └── useRouter.ts      # Worker communication hook
│   ├── types/
│   │   ├── asset.ts          # Asset/Attachment types
│   │   └── session.ts        # Session/Message types
│   ├── worker/
│   │   └── router.worker.ts  # ML inference in Web Worker
│   └── config/
│       └── api.ts            # API base URL config
├── php-backend/
│   ├── index.php             # Mock API server
│   └── embeddings/           # Cached embedding bundles
├── docker/
│   └── nginx.conf            # Production nginx config
├── agents.json               # Agent definitions
├── Dockerfile                # Multi-stage build
└── fly.toml                  # Fly.io configuration
```

## Why This Approach?

### Benefits

1. **Privacy**: Query content never leaves the browser for routing
2. **Latency**: No network round-trip for routing decisions
3. **Cost**: No LLM API calls for classification
4. **Multilingual**: 50+ languages supported out of the box
5. **Offline-capable**: After model download, routing works offline

### Trade-offs

1. **Initial Load**: ~46MB model download on first visit
2. **Memory**: Model consumes browser memory
3. **Complexity**: More sophisticated than keyword matching

## Future Directions

- **Hybrid Routing**: Combine embedding similarity with LLM refinement
- **Agent Chaining**: Multi-step workflows across agents
- **Fine-tuned Embeddings**: Domain-specific embedding models
- **Real Agent Integration**: Connect to actual AI services

## License

MIT
