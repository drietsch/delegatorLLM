# Browser Delegator LLM

A browser-native agent routing system that uses semantic similarity to delegate user requests to specialized agents. Runs entirely in the browser with no server required.

## Concept

Traditional agent orchestration systems rely on server-side LLMs to route requests, introducing latency, cost, and privacy concerns. This project demonstrates an alternative approach:

**Local-first AI routing** - The entire delegation logic runs in the user's browser using a lightweight embedding model. User queries never leave the device.

### How It Works

```
┌─────────────────────────────────────────────────────────────────┐
│                         Browser                                  │
│  ┌─────────────┐    ┌──────────────┐    ┌───────────────────┐  │
│  │ User Query  │───▶│  Embedding   │───▶│ Semantic Matching │  │
│  │ (any lang)  │    │  Model (E5)  │    │  (cosine sim)     │  │
│  └─────────────┘    └──────────────┘    └─────────┬─────────┘  │
│                                                    │            │
│                     ┌──────────────────────────────▼──────┐     │
│                     │         Agent Selection             │     │
│                     │  ┌─────────┐ ┌─────────┐ ┌───────┐ │     │
│                     │  │ Agent 1 │ │ Agent 2 │ │  ...  │ │     │
│                     │  └─────────┘ └─────────┘ └───────┘ │     │
│                     └─────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
```

1. **User submits a query** in any of 50+ supported languages
2. **Embedding model** (multilingual-e5-small) converts the query to a vector
3. **Semantic matching** compares the query vector against pre-computed agent embeddings
4. **Best matching agent** is selected based on cosine similarity
5. **Agent executes** the delegated task

### Why Embeddings Instead of Generative LLM?

Small generative models (0.5B-1B parameters) struggle with:
- Following complex JSON output instructions
- Maintaining context with long agent lists
- Consistent structured output

Embedding-based routing is:
- **Faster**: ~50ms vs 10+ seconds for text generation
- **More reliable**: Mathematical similarity, no parsing needed
- **Smaller**: ~118MB vs 300MB+ for generative models
- **Multilingual**: Native support for 50+ languages

## Features

- **Multilingual support**: Query in German, French, Spanish, Chinese, and 50+ other languages
- **WebGPU acceleration**: Uses GPU when available, falls back to WASM/CPU
- **Privacy-first**: All processing happens locally in the browser
- **Offline capable**: Works without internet after initial model download
- **Extensible**: Easy to add new agents via JSON configuration

## Tech Stack

- **Frontend**: React + TypeScript + Vite
- **ML Runtime**: Transformers.js (ONNX Runtime Web)
- **Embedding Model**: [multilingual-e5-small](https://huggingface.co/Xenova/multilingual-e5-small)
- **Acceleration**: WebGPU / WASM

## Getting Started

```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build
```

Open http://localhost:5173 in your browser. The embedding model (~118MB) will be downloaded and cached on first load.

## Agent Configuration

Agents are defined in `src/config/agents.json`:

```json
{
  "agents": [
    {
      "name": "ai_translator",
      "description": "Multi-language translation agent...",
      "skills": ["translate", "übersetzen", "traduire", ...],
      "endpoint": "/api/translation"
    }
  ]
}
```

Each agent's description and skills are embedded at startup. Add multilingual keywords to improve matching for non-English queries.

## Performance

| Metric | Value |
|--------|-------|
| Model size | ~118MB (cached) |
| Initial load | 2-5 seconds |
| Query embedding | ~20ms (WebGPU) / ~80ms (CPU) |
| Agent matching | <1ms |

## Browser Support

- Chrome/Edge 113+ (WebGPU)
- Firefox 120+ (WASM)
- Safari 17+ (WASM)

## License

MIT
