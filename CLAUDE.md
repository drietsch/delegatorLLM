# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Agent Delegator is a proof-of-concept demonstrating browser-based semantic routing to specialized AI agents with a **Neuron AI-powered PHP backend** featuring **MCP (Model Context Protocol)** integration for standardized tool interoperability. The routing decision runs 100% client-side using a compact multilingual embedding model (Xenova/paraphrase-multilingual-MiniLM-L12-v2), while agent execution happens server-side via real LLM calls through Anthropic Claude, OpenAI, or Ollama.

## Tech Stack

- **Frontend**: React 18 + TypeScript + Vite + Ant Design (including @ant-design/x for chat components)
- **ML Inference**: @huggingface/transformers running ONNX models via WebAssembly in a Web Worker
- **Backend**: PHP 8.2-FPM + Nginx + Neuron AI framework
- **Tool Protocol**: MCP (Model Context Protocol) for standardized AI tools
- **LLM Providers**: Anthropic Claude (default), OpenAI, Ollama
- **Persistence**: File-based (JSON/JSONL) - no database required
- **Deployment**: Docker multi-stage build, hosted on Fly.io

## Development Commands

```bash
# Frontend (Terminal 1)
npm install
npm run dev              # Starts Vite dev server on http://localhost:5174

# Backend (Terminal 2)
cd php-backend
composer install         # Install Neuron AI and dependencies
php bin/build-indexes.php  # Build RAG indexes from fixtures
php -S localhost:3001 index.php   # PHP dev server for API

# Background Worker (Terminal 3 - optional)
cd php-backend
php bin/worker.php       # Process async jobs

# Production build
npm run build            # Outputs to dist/

# Docker (alternative to separate terminals)
docker build -t agent-delegator .
docker run -p 8080:8080 -e ANTHROPIC_API_KEY=sk-ant-... agent-delegator

# Deployment
fly secrets set ANTHROPIC_API_KEY=sk-ant-...
fly deploy               # Deploy to Fly.io
```

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        BROWSER                               │
│  React App (5174 dev / 8080 prod)                           │
│  ├── PowerStripLayout (3-panel: sessions | chat | assets)   │
│  ├── MultiSessionContext (global state via useReducer)      │
│  └── Web Worker (router.worker.ts)                          │
│       ├── Loads ONNX embedding model (~46MB)                │
│       ├── Computes SHA-256 buildId for cache key            │
│       ├── Routes queries via cosine similarity              │
│       └── Caches embeddings server-side                     │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                 PHP BACKEND (Neuron AI)                      │
│  Nginx → PHP-FPM (port 8080)                                │
│                                                              │
│  API Endpoints:                                              │
│  ├── GET  /api/agents           → agents.json               │
│  ├── GET  /api/health           → system health + stats     │
│  ├── POST /api/chat             → non-streaming chat        │
│  ├── POST /api/chat/stream      → SSE streaming chat        │
│  ├── POST /api/workflows/run    → start workflow            │
│  ├── GET  /api/workflows/{id}/stream → workflow events      │
│  ├── POST /api/workflows/{id}/hitl → HITL feedback          │
│  ├── POST /api/attachments      → file upload               │
│  ├── GET  /api/sessions/{id}    → session data              │
│  ├── GET  /api/rag/stats        → RAG index stats           │
│  ├── POST /api/rag/search       → RAG retrieval             │
│  ├── POST /api/copilot/{tool}   → direct tool execution     │
│  ├── GET  /api/queue/stats      → job queue stats           │
│  │                                                          │
│  │  MCP Endpoints:                                          │
│  ├── GET  /api/mcp/servers      → list MCP servers          │
│  ├── GET  /api/mcp/tools        → list all MCP tools        │
│  ├── POST /api/mcp/tools/call   → call any MCP tool         │
│  └── POST /api/mcp/{server}     → JSON-RPC 2.0 endpoint     │
└─────────────────────────────────────────────────────────────┘
```

## Key Files

### Frontend

| File | Purpose |
|------|---------|
| `src/worker/router.worker.ts` | ML inference engine - loads model, generates embeddings, computes similarity |
| `src/hooks/useRouter.ts` | Web Worker communication hook |
| `src/context/MultiSessionContext.tsx` | Global state for multi-session chat management |
| `src/components/ChatStage/ChatStage.tsx` | Main chat interface with agent routing |
| `src/components/Layout/NavHeader.tsx` | Navigation header with page routing |
| `src/components/Layout/PowerStripLayout.tsx` | Main 3-panel layout with page support |
| `src/pages/ApiDocs.tsx` | API documentation page |
| `src/pages/Examples.tsx` | Example queries page with "Try" functionality |
| `agents.json` | Agent definitions with names, descriptions, skills, and endpoints |

### Backend (php-backend/)

| File | Purpose |
|------|---------|
| `index.php` | Router with all API endpoints |
| `src/bootstrap.php` | Autoloader, env loading, storage setup |
| `src/Api/ChatController.php` | Streaming and non-streaming chat |
| `src/Api/WorkflowController.php` | Workflow execution with HITL |
| `src/Api/AttachmentsController.php` | File upload handling |
| `src/Neuron/ProviderFactory.php` | Creates Anthropic/OpenAI/Ollama instances |
| `src/Neuron/AgentFactory.php` | Instantiates agents by ID |
| `src/Neuron/ToolRegistry.php` | Parses agents.json, creates Neuron Tools |
| `src/Neuron/Streaming/SseEmitter.php` | SSE streaming in frontend format |
| `src/Neuron/Agents/*.php` | Agent implementations (Copilot, Translator, etc.) |
| `src/MCP/Server/McpServer.php` | Base MCP server implementation |
| `src/MCP/Client/McpClient.php` | External MCP client |
| `src/MCP/McpRegistry.php` | Registry for all MCP servers and clients |
| `src/MCP/Tools/*.php` | MCP tool servers (SearchMcp, DataObjectMcp, AssetMcp) |
| `src/Tools/*.php` | Tool implementations (Search, DataObject, Asset) |
| `src/Persistence/*.php` | Session, Message, ToolLog stores |
| `src/Queue/JobQueue.php` | File-based async job queue |
| `src/Rag/RagService.php` | RAG retrieval service |
| `src/Rag/InvertedIndex.php` | TF-IDF keyword search index |
| `bin/worker.php` | Background job processor |
| `bin/build-indexes.php` | RAG index builder |
| `fixtures/*.json` | Sample data for RAG |

## Backend Architecture

### Neuron AI Integration

The backend uses the [Neuron AI](https://github.com/inspector-apm/neuron-ai) PHP framework:

```php
// Creating an agent
$agent = AgentFactory::create('copilot');
$response = $agent->chat(new UserMessage($query));

// Streaming response
foreach ($agent->stream($message) as $chunk) {
    $emitter->text($chunk);
}
```

### SSE Streaming Format

Frontend expects line-prefixed format:
- `0:` - Text token
- `9:` - Tool call start
- `a:` - Tool result
- `d:` - Done/finish
- `e:` - Error
- `w:` - Workflow event

### MCP (Model Context Protocol) Integration

The backend implements the [Model Context Protocol](https://modelcontextprotocol.io/) for standardized AI tool interoperability:

```php
// List available MCP servers
$registry = new McpRegistry();
$servers = $registry->listServers();

// Call an MCP tool
$result = $registry->callTool('advanced_search', ['query' => 'laptops']);

// JSON-RPC 2.0 endpoint
POST /api/mcp/search
{"jsonrpc": "2.0", "method": "tools/call", "params": {...}, "id": 1}
```

Available MCP Servers:
- `search` - Product and content search
- `dataobjects` - Data object CRUD operations
- `assets` - Digital asset management

### File-based Persistence

```
storage/
├── sessions/{id}.json      # Session snapshots
├── messages/{id}.jsonl     # Chat history (append-only)
├── workflows/
│   ├── runs/{id}.json      # Workflow state
│   └── events/{id}.jsonl   # Workflow events
├── tools/{id}.jsonl        # Tool call logs
├── rag/index/*.json        # RAG indexes
├── queue/
│   ├── jobs/*.json         # Job definitions
│   └── leases/*.lock       # Job locks
└── attachments/
    ├── meta/{id}.json      # File metadata
    └── bin/{id}_filename   # Actual files
```

### RAG System

Collections with TF-IDF keyword search:
- `products` - Electronics, office equipment (15 items)
- `assets` - Images, documents, videos (15 items)
- `documents` - Pages, articles, landing pages (12 items)
- `customers` - B2B/B2C profiles (15 items)

Build indexes: `php bin/build-indexes.php`

## Agent Configuration

Agents are defined in `/agents.json`. Each agent has:
- `name`, `description`, `skills[]` - used for semantic matching
- `endpoint` - API endpoint for execution
- `function` - function calling schema (parsed by ToolRegistry)

Routing matches user queries against agent profiles: `"{name}: {description} Skills: {skills.join(', ')}"`

## Environment Variables

Create `php-backend/.env`:

```env
# Required: At least one LLM provider
ANTHROPIC_API_KEY=sk-ant-...
OPENAI_API_KEY=sk-...
OLLAMA_URL=http://localhost:11434

# Optional
DEFAULT_PROVIDER=anthropic
DEFAULT_MODEL=claude-sonnet-4-20250514
```

## Frontend Pages

The frontend includes multiple pages accessible via the navigation header:

| Page | Path | Description |
|------|------|-------------|
| Chat | `/` | Main chat interface with multi-session support |
| API Docs | `/api-docs` | Interactive API documentation with endpoints, SSE protocol, MCP docs |
| Examples | `/examples` | Sample queries with "Try" button to test in chat |

Navigation is handled via state in `PowerStripLayout.tsx` with `NavHeader` providing the menu.

## Session Status Flow

Sessions progress through: `routing` → `working` → `streaming` → `completed` (or `error`)

Status is visualized in the PowerStrip component with colored dots (orange/blue/green/red).

## Working with the Backend

### Adding a New Agent

1. Add agent definition to `agents.json`
2. Create agent class in `src/Neuron/Agents/YourAgent.php`
3. Register in `AgentFactory::create()` match statement
4. Agent automatically gets tools from ToolRegistry

### Adding a New Tool

1. Add tool definition to `agents.json` (function schema)
2. Create tool class in `src/Tools/YourTool.php`
3. Register handler in `ToolRegistry::registerHandlers()`

### Adding RAG Data

1. Add/modify fixture file in `fixtures/`
2. Update collection config in `RagService::COLLECTIONS`
3. Run `php bin/build-indexes.php`

### Testing Endpoints

```bash
# Health check
curl http://localhost:3001/api/health

# RAG search
curl -X POST http://localhost:3001/api/rag/search \
  -H "Content-Type: application/json" \
  -d '{"query": "laptop professional", "collections": ["products"]}'

# Direct tool execution
curl -X POST http://localhost:3001/api/copilot/advanced_search \
  -H "Content-Type: application/json" \
  -d '{"query": "laptop", "object_type": "product"}'

# Streaming chat (requires ANTHROPIC_API_KEY)
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
