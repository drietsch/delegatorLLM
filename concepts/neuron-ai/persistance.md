Got it — we’ll do **file-based persistence only** (JSON), no SQLite/DB at all. Below is an updated blueprint that keeps **all state, messages, attachments metadata, tool logs, workflow runs, and RAG indexes** in JSON files, while still delivering **fully working** tools, streaming, async processing, RAG, workflows (HITL/branches/loops), and provider switching.

---

# File-Based Neuron AI Backend Blueprint (JSON-only)

## 1) Core idea

Everything that would normally go into a DB becomes **append-only JSONL** (JSON Lines) or **JSON documents** on disk.

Why JSONL?

* append is safe and fast
* easy to stream and tail
* resilient to partial writes

We’ll use both:

* **JSON** for “current state snapshot”
* **JSONL** for “event log / history”

---

## 2) Folder layout (persistence + fixtures)

```txt
storage/
  sessions/
    <sessionId>.json                # session snapshot (current state)
  messages/
    <sessionId>.jsonl               # chat history (append-only)
  workflows/
    runs/
      <runId>.json                  # workflow snapshot
    events/
      <runId>.jsonl                 # workflow event log (append-only)
  tools/
    <sessionId>.jsonl               # tool calls log (append-only)
  rag/
    collections/
      products.json                 # raw docs (fixtures or built)
      assets.json
      documents.json
      customers.json
    index/
      products.index.json           # inverted index OR embeddings vectors
      assets.index.json
  attachments/
    meta/
      <fileId>.json                 # metadata, extracted text
    bin/
      <fileId>_<filename>           # uploaded binaries
config/
  agents.json
  providers.json
  mcp.json
fixtures/
  products.json
  assets.json
  documents.json
  customers.json
```

---

## 3) State model (JSON schemas)

### 3.1 Session snapshot `storage/sessions/<sessionId>.json`

```json
{
  "id": "uuid",
  "agentId": "copilot_orchestrator",
  "provider": { "name": "openai", "model": "gpt-4.1-mini" },
  "createdAt": "ISO",
  "updatedAt": "ISO",
  "context": {
    "activeObject": null,
    "language": "en",
    "flags": { "rag": true, "streaming": true }
  }
}
```

### 3.2 Messages log `storage/messages/<sessionId>.jsonl`

Each line:

```json
{ "id":"uuid","ts":"ISO","role":"user|assistant|tool","content":"...", "structured":null, "attachments":[] }
```

### 3.3 Tool log `storage/tools/<sessionId>.jsonl`

Each line:

```json
{ "id":"uuid","ts":"ISO","tool":"advanced_search","input":{...},"output":{...},"status":"ok|error","durationMs":123 }
```

### 3.4 Workflow snapshot `storage/workflows/runs/<runId>.json`

```json
{
  "id": "uuid",
  "sessionId": "uuid",
  "name": "product_enrichment_hitl",
  "status": "queued|running|paused|completed|failed",
  "createdAt": "ISO",
  "updatedAt": "ISO",
  "cursor": { "node": "retrieve_products", "step": 2 },
  "state": {
    "input": {},
    "vars": {},
    "pendingHitl": null
  }
}
```

### 3.5 Workflow event log `storage/workflows/events/<runId>.jsonl`

Each line:

```json
{ "seq": 12, "ts":"ISO", "type":"workflow_step|tool_call|token|hitl_request|final|error", "payload":{...} }
```

---

## 4) Streaming strategy (SSE over event logs)

### Chat streaming

`POST /api/chat/stream` returns SSE.
The server:

* starts Neuron run
* **writes events into JSONL** as they happen (tool calls, tokens, final)
* simultaneously streams them to the client

### Workflow streaming

`GET /api/workflows/:runId/stream`

* tail `storage/workflows/events/<runId>.jsonl`
* emit each line as SSE event

This makes async + resume easy without a DB.

---

## 5) Async processing without DB

We use a **file-based queue**:

```txt
storage/queue/
  jobs/
    <jobId>.json
  leases/
    <jobId>.lock
```

* Controller creates a job file (queued)
* Worker claims by creating lock file (atomic)
* Worker writes progress to workflow events JSONL
* On completion, updates workflow snapshot

Worker command:

* `php bin/worker.php` (polls queue folder)

---

## 6) Neuron integration design

### 6.1 NeuronRuntime responsibilities

* load `agents.json`
* pick provider for request
* build agent (system prompt + rules)
* register tools (local + MCP proxy)
* optionally inject RAG context
* run either:

  * direct tool endpoint execution
  * orchestrated chat workflow
* emit events (tokens, tools, steps)

### 6.2 ProviderFactory

Read provider config from:

* request override
* agent default
* `config/providers.json`
* environment variables

---

## 7) Tools: mapping from `agents.json`

We treat each `agent.function` as a **Tool Definition** with:

* name
* description
* JSON schema (parameters)

Then map tool name → handler:

* local handler (PHP)
* MCP proxy handler
* Neuron “sub-agent” handler (for AI tools)

### “Real function, mock data” rule

* Data sources can be fixtures JSON
* But handlers must:

  * validate schema
  * execute logic
  * write logs
  * return structured results

---

## 8) RAG without a DB

Two POC options:

### Option A (fast): Inverted index (keyword search)

* Build `.index.json` per collection:

  * token → list of doc IDs
* Retrieval = tokenize query, score docs, return topK
* Works very well for POC, no embeddings.

### Option B (more realistic): Store embeddings in JSON

* `products.vectors.json` holds embedding arrays per doc
* Similarity computed in PHP for small data sets
* Slower, but still fine for POC volumes.

I recommend **Option A** first; add embeddings later.

---

## 9) Workflows with pause/resume (HITL) file-based

When HITL needed:

* write `hitl_request` event to `events/<runId>.jsonl`
* set workflow snapshot status to `paused`
* include expected answer schema + prompt

Resume endpoint:
`POST /api/workflows/:runId/hitl`

* validate input vs schema
* write `hitl_response` event
* update snapshot state
* enqueue resume job

---

## 10) Endpoint plan (unchanged, but file-backed)

### Chat

* `POST /api/chat`
* `POST /api/chat/stream`

### Existing per-agent endpoints

* Keep them, validate input using `function.parameters`
* Execute via runtime/tool registry

### Attachments

* `POST /api/attachments`
* store binary + meta json
* optional extractedText (mock ok)

### Workflows

* `POST /api/workflows/run` → returns runId (queued)
* `GET /api/workflows/:runId` → reads snapshot JSON
* `GET /api/workflows/:runId/stream` → tails JSONL
* `POST /api/workflows/:runId/hitl` → resume

---

## 11) File I/O correctness (important)

Use atomic write pattern for snapshots:

1. write `file.tmp`
2. rename to final name (atomic on same filesystem)

Append JSONL with `flock()` to avoid interleaving.

---

## 12) Concrete first milestone (using your agents.json)

### Implement these first tools fully:

* `advanced_search` (reads fixtures/products.json, filters)
* `data_object_manager` (CRUD stored in `storage/pimcore_shadow/<type>.json`)
* `asset_manager` (file upload/list/meta)
* `ai_text_generator` (Neuron call)
* `ai_translator` (Neuron call)

Then build:

* `copilot_chat_orchestrator` workflow that can decide between these tools.

---

## 13) What changes in your previous briefing

* Replace DB with `storage/*` JSON/JSONL
* Replace “worker reads DB” with “worker reads queue files”
* Replace “workflow events in DB” with “workflow events JSONL”

Everything else stays the same: tools, providers, streaming, workflows, RAG, attachments.

---

## 14) Next step (I can produce immediately)

If you tell me your backend framework (plain PHP / Symfony / Laravel / Slim), I’ll output:

* exact directory skeleton
* file-by-file responsibilities
* JSON schemas for snapshots/events
* SSE implementation
* worker queue logic
* a “shadow Pimcore” CRUD layer using JSON files
* how each agent in your `agents.json` maps to a tool or workflow

No DB, no sqlite — just JSON on disk, fully functional.
