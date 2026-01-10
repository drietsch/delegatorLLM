# Agent Delegator

A proof-of-concept demonstrating **browser-based semantic routing** to specialized AI agents with a **Neuron AI-powered PHP backend** featuring **MCP (Model Context Protocol)** integration for standardized tool interoperability.

## Overview

Agent Delegator showcases how natural language queries can be automatically routed to the most appropriate AI agent. The routing intelligence runs **100% client-side** using a compact multilingual embedding model compiled to WebAssembly, while agent execution happens server-side via the Neuron AI PHP framework with MCP-compatible tools.

```
User Query: "Search for laptops"
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
Best Match: "copilot" (confidence: 0.87)
    │
    ▼
┌─────────────────────────────────────────┐
│  Neuron AI Backend                      │
│  CopilotAgent → advanced_search tool    │
│  → Returns 3 laptop products            │
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
│  - Drag & drop      │   - Status dots    │  - Markdown rendering    │
│  - Multi-select     │   - Background     │  - Tool call display     │
│                     │     notifications  │  - Agent confidence      │
│                     │                    │                          │
├─────────────────────┴────────────────────┴──────────────────────────┤
│  Navigation Header: Chat | API Docs | Examples                       │
├─────────────────────────────────────────────────────────────────────┤
│                        Web Worker (router.worker.ts)                 │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │  @huggingface/transformers                                      │ │
│  │  ├── Model: Xenova/paraphrase-multilingual-MiniLM-L12-v2       │ │
│  │  ├── Task: Feature Extraction (384-dim embeddings)              │ │
│  │  └── Runtime: ONNX via WebAssembly                              │ │
│  └────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────┬───────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                     PHP BACKEND (Neuron AI + MCP)                    │
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
│  ├── MCP/                    # Model Context Protocol                │
│  │   ├── Server/McpServer.php    (base MCP server)                  │
│  │   ├── Client/McpClient.php    (external MCP client)              │
│  │   ├── McpRegistry.php         (server/client registry)           │
│  │   └── Tools/                  (SearchMcp, DataObjectMcp, AssetMcp)│
│  ├── Tools/                  # Tool Implementations                  │
│  │   ├── SearchTool.php                                             │
│  │   ├── DataObjectTool.php                                         │
│  │   ├── AssetTool.php                                              │
│  │   └── AiTools/ (TextGenerator, Translator)                       │
│  ├── Persistence/            # File-based State                      │
│  ├── Queue/                  # Async Processing                      │
│  └── Rag/                    # Retrieval System                      │
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
| GET | `/api/mcp/servers` | List MCP servers |
| GET | `/api/mcp/tools` | List all MCP tools |
| POST | `/api/mcp/tools/call` | Call any MCP tool |
| POST | `/api/mcp/{server}` | JSON-RPC endpoint for MCP server |
| POST | `/api/attachments` | Upload file |
| GET | `/api/sessions/{id}` | Get session data |

### Key Technologies

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Embeddings** | `@huggingface/transformers` | Browser-native ML inference via ONNX/WASM |
| **Model** | `Xenova/paraphrase-multilingual-MiniLM-L12-v2` | 50+ language support, 384 dimensions |
| **UI Framework** | Ant Design + Ant Design X | Chat bubbles, sender, markdown rendering |
| **Markdown** | `@ant-design/x-markdown` | Renders AI responses with syntax highlighting |
| **State Management** | React Context + useReducer | Multi-session chat state |
| **Build Tool** | Vite | Fast HMR, worker bundling |
| **Backend Framework** | Neuron AI (PHP) | Agentic AI with tools, RAG, workflows |
| **Tool Protocol** | MCP (Model Context Protocol) | Standardized AI tool interoperability |
| **LLM Providers** | Anthropic Claude, OpenAI, Ollama | Multi-provider support |
| **Persistence** | JSON/JSONL files | No database required |
| **Deployment** | Docker + Fly.io | Single container with frontend + backend |

## Features

### MCP Integration

The backend implements the [Model Context Protocol](https://modelcontextprotocol.io/), enabling standardized AI tool integration:

- **MCP Servers**: Expose tools as JSON-RPC 2.0 endpoints
  - `search` - Product and content search
  - `dataobjects` - Data object CRUD operations
  - `assets` - Digital asset management
- **MCP Client**: Connect to external MCP servers
- **MCP Registry**: Central registry for all servers and clients

### Neuron AI Backend

The backend is powered by [Neuron AI](https://github.com/inspector-apm/neuron-ai), a PHP framework for building agentic AI applications:

- **Multi-Provider Support**: Anthropic Claude (default), OpenAI, Ollama
- **Streaming Responses**: Real-time SSE streaming with markdown support
- **Tool Calling**: Dynamic tool registry with visual tool call display
- **RAG Integration**: Keyword-based retrieval for products, assets, documents
- **Workflows**: HITL (Human-in-the-Loop) support for approval workflows

### Available Tools

| Tool | Description |
|------|-------------|
| `advanced_search` | Search products, assets, documents by keywords |
| `ai_text_generator` | Generate marketing copy, descriptions, articles |
| `ai_translator` | Multi-language translation (50+ languages) |
| `data_object_create/read/update/delete` | CRUD for data objects |
| `asset_upload/download/list/move` | Digital asset management |

### Frontend Features

- **Navigation**: Chat, API Docs, and Examples pages
- **Markdown Rendering**: AI responses rendered with syntax highlighting
- **Tool Call Visualization**: Expandable tool call cards showing args and results
- **Multi-Session Chat**: Background sessions with notification pills
- **DAM Integration**: Drag & drop assets from tree to chat

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

# Start frontend (Terminal 1) - port 5174/5175
npm run dev

# Start backend (Terminal 2) - port 3001
cd php-backend
php -S localhost:3001 index.php
```

### Environment Variables

Create `php-backend/.env`:

```env
# Required: At least one LLM provider
ANTHROPIC_API_KEY=sk-ant-...
# OPENAI_API_KEY=sk-...
# OLLAMA_URL=http://localhost:11434
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

```bash
# Set secrets
fly secrets set ANTHROPIC_API_KEY=sk-ant-...

# Deploy
fly deploy
```

## Project Structure

```
├── src/                          # Frontend (React)
│   ├── components/
│   │   ├── AssetTree/            # DAM tree with drag support
│   │   ├── ChatStage/            # Chat UI, tool calls, markdown
│   │   ├── Layout/               # Navigation header, layout
│   │   └── PowerStrip/           # Session tokens
│   ├── pages/
│   │   ├── ApiDocs.tsx           # API documentation
│   │   └── Examples.tsx          # Example queries
│   ├── context/
│   │   └── MultiSessionContext.tsx
│   ├── hooks/
│   │   └── useRouter.ts          # Worker communication
│   └── worker/
│       └── router.worker.ts      # ML inference
│
├── php-backend/                  # Backend (Neuron AI + MCP)
│   ├── index.php                 # Router with all endpoints
│   ├── src/
│   │   ├── Api/                  # Controllers
│   │   ├── Neuron/               # AI runtime + agents
│   │   ├── MCP/                  # Model Context Protocol
│   │   │   ├── Server/           # MCP server base
│   │   │   ├── Client/           # MCP client
│   │   │   └── Tools/            # MCP tool servers
│   │   ├── Tools/                # Tool implementations
│   │   ├── Persistence/          # File-based stores
│   │   └── Rag/                  # RAG system
│   ├── fixtures/                 # Sample data
│   └── storage/                  # Runtime data
│
├── agents.json                   # Agent definitions
├── Dockerfile                    # Multi-stage build
└── fly.toml                      # Fly.io configuration
```

## Testing the API

```bash
# Health check
curl http://localhost:3001/api/health

# Chat (streaming)
curl -X POST http://localhost:3001/api/chat/stream \
  -H "Content-Type: application/json" \
  -d '{"message": "Search for laptops"}'

# MCP tools list
curl http://localhost:3001/api/mcp/tools

# Direct MCP tool call
curl -X POST http://localhost:3001/api/mcp/tools/call \
  -H "Content-Type: application/json" \
  -d '{"name": "advanced_search", "arguments": {"query": "laptops"}}'
```

## Why This Approach?

### Benefits

1. **Privacy**: Query content never leaves the browser for routing
2. **Latency**: No network round-trip for routing decisions
3. **Cost**: No LLM API calls for classification
4. **Multilingual**: 50+ languages supported out of the box
5. **Real AI**: Neuron backend provides actual LLM execution
6. **Standardized Tools**: MCP protocol for tool interoperability
7. **No Database**: File-based persistence simplifies deployment

### Trade-offs

1. **Initial Load**: ~46MB model download on first visit
2. **Memory**: Model consumes browser memory
3. **API Keys**: Requires LLM provider credentials for execution

## License

POCL
