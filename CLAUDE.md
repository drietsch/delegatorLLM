# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Agent Delegator is a proof-of-concept demonstrating browser-based semantic routing to specialized AI agents with a **Neuron AI-powered PHP backend**. The routing decision runs 100% client-side using a compact multilingual embedding model (Xenova/paraphrase-multilingual-MiniLM-L12-v2), while agent execution happens server-side via real LLM calls through Anthropic Claude, OpenAI, or Ollama.

## Tech Stack

- **Frontend**: React 18 + TypeScript + Vite + Ant Design (including @ant-design/x for chat components)
- **ML Inference**: @huggingface/transformers running ONNX models via WebAssembly in a Web Worker
- **Backend**: PHP 8.2-FPM + Nginx + Neuron AI framework
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
│  └── GET  /api/queue/stats      → job queue stats           │
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
```
