# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Agent Delegator is a proof-of-concept demonstrating browser-based semantic routing to specialized AI agents using multilingual embeddings running entirely in WebAssembly. The routing decision runs 100% client-side using a compact multilingual embedding model (Xenova/paraphrase-multilingual-MiniLM-L12-v2) - no server-side LLM calls required for routing.

## Tech Stack

- **Frontend**: React 18 + TypeScript + Vite + Ant Design (including @ant-design/x for chat components)
- **ML Inference**: @huggingface/transformers running ONNX models via WebAssembly in a Web Worker
- **Backend**: PHP 8.2-FPM + Nginx (serves API endpoints and static files)
- **Deployment**: Docker multi-stage build, hosted on Fly.io

## Development Commands

```bash
# Frontend (Terminal 1)
npm install
npm run dev              # Starts Vite dev server on http://localhost:5174

# Backend (Terminal 2)
cd php-backend
php -S localhost:3001 index.php   # PHP dev server for API

# Production build
npm run build            # Outputs to dist/

# Docker (alternative to separate terminals)
docker build -t agent-delegator .
docker run -p 8080:8080 agent-delegator

# Deployment
fly deploy               # Deploy to Fly.io
GITHUB_PAGES=true npm run build  # Build for GitHub Pages (static only)
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
│                     PHP BACKEND                              │
│  Nginx → PHP-FPM (port 8080)                                │
│  ├── GET  /api/agents          → agents.json                │
│  ├── GET  /embeddings/{id}     → cached embedding bundle    │
│  ├── PUT  /embeddings/{id}     → store embedding bundle     │
│  └── POST /api/chat            → mock streaming response    │
└─────────────────────────────────────────────────────────────┘
```

## Key Files

| File | Purpose |
|------|---------|
| `src/worker/router.worker.ts` | ML inference engine - loads model, generates embeddings, computes similarity |
| `src/hooks/useRouter.ts` | Web Worker communication hook |
| `src/context/MultiSessionContext.tsx` | Global state for multi-session chat management |
| `src/components/ChatStage/ChatStage.tsx` | Main chat interface with agent routing |
| `agents.json` | Agent definitions with names, descriptions, skills, and endpoints |
| `php-backend/index.php` | All backend API routes |

## Agent Configuration

Agents are defined in `/agents.json`. Each agent has:
- `name`, `description`, `skills[]` - used for semantic matching
- `endpoint` - API endpoint for execution
- `function` - function calling schema

Routing matches user queries against agent profiles: `"{name}: {description} Skills: {skills.join(', ')}"`

## Embedding Cache Strategy

1. Compute `buildId` = SHA-256(model_id + chunking_version + SHA-256(agents.json))
2. Check `GET /embeddings/{buildId}` for cached vectors
3. On cache miss: load model, generate embeddings, store via `PUT /embeddings/{buildId}`
4. Cache stored in `php-backend/embeddings/{buildId}.json`

## Session Status Flow

Sessions progress through: `routing` → `working` → `streaming` → `completed` (or `error`)

Status is visualized in the PowerStrip component with colored dots (orange/blue/green/red).
