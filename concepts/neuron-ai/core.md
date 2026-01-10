Got it. Based on your `agents.json`, here’s a **concrete integration blueprint** that turns those agents into a **Neuron-driven backend** with **tools, routing, streaming, structured output, attachments, async, RAG, workflows, HITL, branches/loops**, and state/persistence—while keeping your existing “endpoint per agent” style.

---

# Implementation Blueprint: Neuron AI Backend for `agents.json`

## 1) What we’ll build (POC scope, fully functional plumbing)

### You will end up with:

* A **single Neuron runtime** that can execute any agent from `agents.json`
* Each agent endpoint (e.g. `/api/copilot/text-generation`) becomes a **thin controller** that delegates to the runtime
* A unified **/api/chat/stream** endpoint for the React chat UI (recommended), plus backward-compatible per-agent endpoints
* A **Tool Registry** built from the `function` entries (JSON schema) + real implementations (mock data OK)
* **MCP tool bridge**: tools can be executed locally or proxied to MCP servers
* **RAG** for Pimcore-ish collections: `products`, `assets`, `documents`, `customers` (using mock fixtures)
* **Workflows** that support single-step, multi-step, loops, branching, HITL pause/resume
* **State & persistence** (SQLite) for sessions, messages, tool calls, workflow runs, attachments, and (optionally) embeddings

---

## 2) Key design decision: “Agent endpoints” vs “Chat runtime”

Your `agents.json` already defines endpoints per agent. We keep them, but implement them via a shared runtime:

### Recommendation

* **Frontend chat** uses:

  * `POST /api/chat/stream` (SSE) for everything (best UX)
* Your existing endpoints remain:

  * `/api/copilot/text-generation`, `/api/assets`, etc.
  * They call the same underlying runtime, forcing “tool-style” execution or “agent conversation” depending on request type.

---

## 3) Map `agents.json` to Neuron: two execution modes

Your JSON defines **agents** that behave like “capability modules”. Neuron can support them in two ways:

### Mode A: “Copilot Chat Agent” (preferred for the chat UI)

* A single Neuron **Copilot Orchestrator Agent** handles user messages.
* It selects tools based on your agent definitions.
* It can run workflows, RAG, tool calls, and generate final responses.

### Mode B: “Direct tool endpoint execution” (compatible with your endpoints)

* Each endpoint validates parameters using the JSON schema.
* Then it runs the underlying Neuron agent or workflow dedicated to that feature.
* Useful for deterministic APIs and structured output.

Both modes share the same tool registry + persistence.

---

## 4) Backend Structure (minimal but scalable)

```txt
src/
  Api/
    ChatController.php
    AgentEndpointController.php
    AttachmentsController.php
    WorkflowController.php
  Neuron/
    NeuronRuntime.php
    ProviderFactory.php
    AgentFactory.php
    ToolRegistry.php
    McpBridge.php
    RagService.php
    WorkflowFactory.php
    Streaming/
      SseEmitter.php
      EventEnvelope.php
  Persistence/
    Db.php
    Repositories/
      SessionsRepo.php
      MessagesRepo.php
      ToolLogsRepo.php
      WorkflowRunsRepo.php
      AttachmentsRepo.php
  Fixtures/
    products.json
    assets.json
    documents.json
    customers.json
```

---

## 5) Persistence schema (SQLite, POC but real)

Tables:

* `sessions(id, createdAt, updatedAt, agentId, provider, metadataJson)`
* `messages(id, sessionId, role, content, structuredJson, createdAt)`
* `tool_logs(id, sessionId, toolName, inputJson, outputJson, status, startedAt, endedAt)`
* `workflow_runs(id, sessionId, name, status, stateJson, createdAt, updatedAt)`
* `workflow_events(id, runId, seq, type, payloadJson, createdAt)` *(optional for replay)*
* `attachments(id, filename, mime, size, path, extractedText, metaJson, createdAt)`
* `rag_documents(id, collection, sourceId, title, text, metaJson)`
* `rag_vectors(id, docId, embeddingJson)` *(optional for real vector search)*

---

## 6) Tool Registry derived from `agents.json`

### Build-time / startup:

Parse `agents.json` and create a `ToolDefinition` for each `function` block:

Example:

* Tool name: `ai_text_generator`
* Schema: `parameters`
* Description: `function.description`

Then register tool handlers (real functions) for:

* deterministic “managers” (`asset_manager`, `data_object_manager`, etc.)
* AI-powered ones (`ai_text_generator`, `ai_translator`) implemented as Neuron agent calls

✅ Important: Even “AI agents” become tools from the orchestrator’s perspective.

---

## 7) Tool Implementation Plan (what is real vs mock)

### 7.1 Mock data allowed

* Products, assets, customers, documents → from fixtures or SQLite “shadow Pimcore”
* GraphQL responses → simulated

### 7.2 Must be fully implemented

* Schema validation of tool inputs
* Tool execution routing
* Logging + persistence
* Streaming tool events
* Workflow pause/resume mechanics

### Suggested initial tool categories

#### A) PIM / Object tools

* `data_object_manager`:

  * `create/read/update/delete/list`
  * operates on SQLite tables per `object_type` or a generic JSON store

#### B) DAM tools

* `asset_manager`:

  * `upload/download/list/delete/move`
  * store metadata, path, mock thumbnails; real file IO for uploads

#### C) Search

* `advanced_search`:

  * query fixtures/SQLite; return filterable results

#### D) AI tools

* `ai_text_generator`, `ai_translator`, `ai_classifier`:

  * use Neuron provider + prompt templates
  * structured output optional

#### E) Ecommerce (POC)

* cart/checkout/pricing:

  * implement state in SQLite keyed by session/customer_id

#### F) Workflow + automation

* `workflow_manager`, `automation_runner`:

  * these should map to Neuron **workflows** (below)

#### G) MCP tools

* Any tool may be configured as `execution: "mcp"` in agent metadata (extend JSON optionally)
* `McpBridge` forwards tool call to MCP server, returns result, logs it

---

## 8) Streaming contract for React (SSE events)

All streaming endpoints emit envelopes:

```json
{
  "type": "token|tool_call|tool_result|workflow_step|state|error|final",
  "ts": "ISO-8601",
  "sessionId": "...",
  "runId": "...",
  "payload": { ... }
}
```

Frontend expects:

* `token` events to append assistant text
* `tool_call` + `tool_result` to render “thinking” timeline
* `workflow_step` to show multi-step execution
* `final` includes final message + optional structured payload

---

## 9) Workflows: templates driven by your agents

Your `agents.json` includes many “manager-like” agents and “AI-like” agents. Workflows combine them.

### Workflow 1: `copilot_chat_orchestrator` (multi-step)

Steps:

1. Interpret intent (LLM)
2. Decide tool(s) (LLM tool calling)
3. Execute tool(s)
4. If user asked for “analysis/report” → structured output schema enforcement
5. Compose final answer
6. Persist messages + tool logs

Supports:

* branching (if tool required vs direct answer)
* loops (tool calls repeated until done, bounded by max iterations)

### Workflow 2: `data_import_pipeline` (async, multi-step)

1. Validate source + format
2. Parse file (mock parsing ok)
3. Map fields
4. Create/update objects via `data_object_manager` tool
5. Emit progress events
6. Produce report (structured)

### Workflow 3: `product_enrichment_hitl` (HITL)

1. Retrieve product(s) via search
2. Identify missing fields (via `data_quality`)
3. Propose enrichment text/images (ai tools)
4. Pause for human confirmation (HITL)
5. On resume, apply updates (data_object_manager / asset_manager)

### Workflow 4: `export_feed` (branching)

* if `format=json/csv/xml` choose exporter path
* optional: send webhook after completion

---

## 10) Human-in-the-loop (pause/resume)

Implement:

* Workflow node emits `hitl_request` with question + expected schema
* Workflow writes `workflow_runs.stateJson = { status: "paused", ... }`
* Frontend displays the question
* User POSTs answer to:

  * `POST /api/workflows/:runId/hitl`
* Resume continues workflow from stored node/state

---

## 11) RAG for Pimcore context (products + assets)

### Collections

* `products`: product name, sku, description, attributes
* `assets`: filename, alt text, tags, usage
* `documents`: pages/snippets content
* `customers`: profile + history (mock)

### Retrieval flow

* Retrieve topK docs based on query (and/or “active object context”)
* Inject into prompt as “Context”
* Cite which docs were used (IDs only)

For POC:

* Option A: naive keyword retrieval (fast to implement)
* Option B: real embeddings + cosine similarity in PHP for small sets

Expose via request flag:

```json
"rag": { "enabled": true, "collections": ["products","assets"], "topK": 5 }
```

---

## 12) Structured output: leverage agent `function.parameters`

Your `agents.json` already contains JSON schemas. Use them for:

* validating incoming requests to agent endpoints
* optionally forcing the model to output structured JSON

Example:

* `ai_text_generator` can return:

```json
{
  "title": "...",
  "content": "...",
  "bullets": ["..."]
}
```

Add `options.structuredOutput.schema` to force schema.

---

## 13) Concrete endpoint plan

### Unified chat (recommended)

* `POST /api/chat/stream` (SSE)
* `POST /api/chat` (non-stream)

### Existing agent endpoints (implemented via ToolRegistry + Runtime)

* `/api/copilot/text-generation` → tool `ai_text_generator`
* `/api/copilot/translation` → tool `ai_translator`
* `/api/assets` → tool `asset_manager`
* etc.

### Workflows

* `POST /api/workflows/run`
* `GET /api/workflows/:runId/stream`
* `POST /api/workflows/:runId/hitl`

### Attachments

* `POST /api/attachments`
* `POST /api/attachments/:id/index`

---

## 14) How `agents.json` is used (explicit rules)

### At runtime, for every request:

1. Match agent by `agentId` OR by endpoint route map
2. Validate input parameters using `function.parameters`
3. Run in one of two modes:

   * **tool invocation** (endpoint style)
   * **orchestrated chat** (frontend chat)

### `skills[]` array usage

Use `skills[]` as:

* tool routing hints (for retrieval of tool candidates)
* simple keyword-based “agent suggestion” (POC)
* eventually replace with embeddings-based agent router

---

## 15) Minimum milestone plan

### Milestone 1: Neuron integration baseline

* Neuron installed
* ProviderFactory supports 2 providers
* Chat endpoint works (non-stream)

### Milestone 2: Streaming + tool registry

* SSE streaming
* Parse agents.json → register tools
* Implement 5 core tools (product search/get, asset search/get, ai_text_generator)

### Milestone 3: Workflows + persistence

* workflow_runs table
* workflow run stream
* HITL pause/resume

### Milestone 4: Attachments + RAG

* upload + extraction mock
* retrieval pipeline implemented (keyword or embeddings)
* agent can answer using RAG

### Milestone 5: Async worker

* queued workflow runs
* progress events
* resumable execution

---

## 16) What I need next (no blocker—just improves precision)

If you paste:

* your current PHP routing setup (framework? slim/symfony/laravel/custom)
* your current streaming approach (if any)
* how frontend expects streaming events (or I can propose)

…I can produce a **file-by-file implementation plan** including:

* exact class stubs
* example ToolRegistry builder from this `agents.json`
* an example Workflow definition matching your POC needs
* SSE emitter implementation that matches React consumption

If you want, I can also generate a **ready-to-commit skeleton** (folder structure + PHP files + sqlite migrations) as a zip, similar to the skill zip you asked for earlier.
