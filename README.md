# Agent Delegator

A proof-of-concept demonstrating **browser-based semantic routing** to specialized AI agents with a **Neuron AI-powered PHP backend** for real agent execution.

## Overview

Agent Delegator showcases how natural language queries can be automatically routed to the most appropriate AI agent. The routing intelligence runs **100% client-side** using a compact multilingual embedding model compiled to WebAssembly, while agent execution happens server-side via the Neuron AI PHP framework.

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
Best Match: "ai_translator" (confidence: 0.87)
    │
    ▼
┌─────────────────────────────────────────┐
│  Neuron AI Backend                      │
│  TranslatorAgent → Claude/OpenAI        │
│  Real LLM execution with streaming      │
└─────────────────────────────────────────┘
```

## Architecture

### System Overview

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
│                     PHP BACKEND (Neuron AI)                          │
│  Nginx → PHP-FPM (port 8080)                                        │
│                                                                      │
│  src/                                                                │
│  ├── Api/                    # HTTP Controllers                      │
│  │   ├── ChatController.php      (streaming chat)                   │
│  │   ├── WorkflowController.php  (HITL workflows)                   │
│  │   └── AttachmentsController.php                                  │
│  ├── Neuron/                 # Core AI Runtime                       │
│  │   ├── ProviderFactory.php     (Anthropic/OpenAI/Ollama)          │
│  │   ├── AgentFactory.php        (agent instantiation)              │
│  │   ├── ToolRegistry.php        (dynamic tool loading)             │
│  │   ├── Agents/                 (CopilotAgent, TranslatorAgent...) │
│  │   └── Streaming/SseEmitter.php                                   │
│  ├── Tools/                  # Tool Implementations                  │
│  │   ├── SearchTool.php                                             │
│  │   ├── DataObjectTool.php                                         │
│  │   ├── AssetTool.php                                              │
│  │   └── AiTools/ (TextGenerator, Translator)                       │
│  ├── Persistence/            # File-based State                      │
│  │   ├── SessionStore.php                                           │
│  │   ├── MessageStore.php                                           │
│  │   └── ToolLogStore.php                                           │
│  ├── Queue/                  # Async Processing                      │
│  │   └── JobQueue.php                                               │
│  └── Rag/                    # Retrieval System                      │
│      ├── RagService.php                                             │
│      └── InvertedIndex.php                                          │
│                                                                      │
│  storage/                    # Runtime Data                          │
│  ├── sessions/               # Session JSON snapshots                │
│  ├── messages/               # Chat history JSONL                    │
│  ├── workflows/              # Workflow state + events               │
│  ├── rag/                    # RAG indexes                           │
│  └── queue/                  # Background job queue                  │
└─────────────────────────────────────────────────────────────────────┘
```

### API Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/agents` | List all agents |
| GET | `/api/health` | Health check with system stats |
| POST | `/api/chat` | Non-streaming chat |
| POST | `/api/chat/stream` | Streaming chat (SSE) |
| POST | `/api/workflows/run` | Start a workflow |
| GET | `/api/workflows/{id}/stream` | Stream workflow events |
| POST | `/api/workflows/{id}/hitl` | Resume workflow with HITL feedback |
| POST | `/api/attachments` | Upload file |
| GET | `/api/sessions/{id}` | Get session data |
| GET | `/api/rag/stats` | RAG index statistics |
| POST | `/api/rag/search` | RAG context retrieval |
| POST | `/api/copilot/{tool}` | Direct tool execution |
| GET | `/api/queue/stats` | Background queue stats |

### Key Technologies

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Embeddings** | `@huggingface/transformers` | Browser-native ML inference via ONNX/WASM |
| **Model** | `Xenova/paraphrase-multilingual-MiniLM-L12-v2` | 50+ language support, 384 dimensions |
| **UI Framework** | Ant Design + Ant Design X | Chat bubbles, sender, prompts |
| **State Management** | React Context + useReducer | Multi-session chat state |
| **Build Tool** | Vite | Fast HMR, worker bundling |
| **Backend Framework** | Neuron AI (PHP) | Agentic AI with tools, RAG, workflows |
| **LLM Providers** | Anthropic Claude, OpenAI, Ollama | Multi-provider support |
| **Persistence** | JSON/JSONL files | No database required |
| **Deployment** | Docker + Fly.io | Single container with frontend + backend |

## Features

### Neuron AI Backend

The backend is powered by [Neuron AI](https://github.com/inspector-apm/neuron-ai), a PHP framework for building agentic AI applications:

- **Multi-Provider Support**: Anthropic Claude (default), OpenAI, Ollama
- **Streaming Responses**: Real-time SSE streaming compatible with React frontend
- **Tool Calling**: Dynamic tool registry loaded from `agents.json`
- **RAG Integration**: Keyword-based retrieval for products, assets, documents, customers
- **Workflows**: HITL (Human-in-the-Loop) support for approval workflows
- **Async Processing**: Background worker for long-running tasks
- **File Persistence**: JSON snapshots + JSONL append logs (no database needed)

### Available Agents

| Agent | Description |
|-------|-------------|
| `copilot` | Main orchestrator with access to all tools |
| `ai_text_generator` | Content generation with tone/length control |
| `ai_translator` | Multi-language translation (50+ languages) |
| `advanced_search` | RAG-powered search across collections |
| `data_object_manager` | CRUD operations for data objects |
| `asset_manager` | Digital asset management |

### RAG Collections

Pre-built indexes for semantic search:

- **Products**: 15 sample electronics & office equipment
- **Assets**: 15 sample images, documents, videos
- **Documents**: 12 sample pages, articles, landing pages
- **Customers**: 15 sample B2B/B2C customer profiles

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

## Development

### Prerequisites

- Node.js 20+
- PHP 8.2+ with Composer
- Anthropic API key (or OpenAI/Ollama)

### Local Development

```bash
# Install frontend dependencies
npm install

# Install backend dependencies
cd php-backend
composer install

# Build RAG indexes
php bin/build-indexes.php

# Start frontend (Terminal 1) - port 5174
npm run dev

# Start backend (Terminal 2) - port 3001
cd php-backend
php -S localhost:3001 index.php

# Optional: Start background worker (Terminal 3)
cd php-backend
php bin/worker.php
```

### Environment Variables

Create `php-backend/.env`:

```env
# Required: At least one LLM provider
ANTHROPIC_API_KEY=sk-ant-...
# OPENAI_API_KEY=sk-...
# OLLAMA_URL=http://localhost:11434

# Optional
DEFAULT_PROVIDER=anthropic
DEFAULT_MODEL=claude-sonnet-4-20250514
```

### Production Build

```bash
npm run build
```

### Docker Build

```bash
docker build -t agent-delegator .
docker run -p 8080:8080 \
  -e ANTHROPIC_API_KEY=sk-ant-... \
  agent-delegator
```

## Deployment

### Fly.io

The app is configured for Fly.io deployment:

```bash
# Set secrets
fly secrets set ANTHROPIC_API_KEY=sk-ant-...

# Deploy
fly deploy
```

Configuration in `fly.toml`:
- Region: Frankfurt (fra)
- Memory: 1GB
- Auto-stop/start enabled

### GitHub Pages

For static deployment (frontend only, no backend):

```bash
GITHUB_PAGES=true npm run build
```

## Project Structure

```
├── src/                          # Frontend (React)
│   ├── components/
│   │   ├── AssetTree/            # DAM tree with drag support
│   │   ├── ChatStage/            # Chat UI, sender, attachments
│   │   ├── Layout/               # PowerStrip layout container
│   │   └── PowerStrip/           # Session tokens, new session button
│   ├── context/
│   │   └── MultiSessionContext.tsx
│   ├── hooks/
│   │   └── useRouter.ts          # Worker communication hook
│   ├── worker/
│   │   └── router.worker.ts      # ML inference in Web Worker
│   └── config/
│       └── api.ts                # API base URL config
│
├── php-backend/                  # Backend (Neuron AI)
│   ├── index.php                 # Router with all endpoints
│   ├── src/
│   │   ├── Api/                  # Controllers
│   │   ├── Neuron/               # AI runtime
│   │   │   ├── Agents/           # Agent implementations
│   │   │   └── Streaming/        # SSE emitter
│   │   ├── Tools/                # Tool implementations
│   │   ├── Persistence/          # File-based stores
│   │   ├── Queue/                # Job queue
│   │   └── Rag/                  # RAG system
│   ├── fixtures/                 # Sample data
│   │   ├── products.json
│   │   ├── assets.json
│   │   ├── documents.json
│   │   └── customers.json
│   ├── bin/
│   │   ├── build-indexes.php     # RAG index builder
│   │   └── worker.php            # Background worker
│   └── storage/                  # Runtime data (auto-created)
│
├── agents.json                   # Agent definitions (30 agents)
├── Dockerfile                    # Multi-stage build
└── fly.toml                      # Fly.io configuration
```

## Why This Approach?

### Benefits

1. **Privacy**: Query content never leaves the browser for routing
2. **Latency**: No network round-trip for routing decisions
3. **Cost**: No LLM API calls for classification
4. **Multilingual**: 50+ languages supported out of the box
5. **Real AI**: Neuron backend provides actual LLM execution
6. **No Database**: File-based persistence simplifies deployment
7. **Portable**: Single Docker container with everything included

### Trade-offs

1. **Initial Load**: ~46MB model download on first visit
2. **Memory**: Model consumes browser memory
3. **API Keys**: Requires LLM provider credentials for execution

## Future Directions

- **Vector Embeddings**: Replace TF-IDF with semantic vector search
- **Workflow UI**: Visual workflow builder for HITL processes
- **Agent Chaining**: Multi-step workflows across agents
- **Fine-tuned Embeddings**: Domain-specific embedding models
- **Real Pimcore Integration**: Connect to actual Pimcore CMS

## License

POCL
