# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Browser-native agent routing system using FunctionGemma-270M for function calling. Routes user requests to specialized agents with parameter extraction. Runs entirely in the browser using LiteRT.js. Privacy-first: user queries never leave the device.

## Commands

```bash
npm run dev      # Start dev server at http://localhost:5173
npm run build    # TypeScript compile + Vite build → dist/
npm run lint     # ESLint check
npm run preview  # Preview production build
npm run deploy   # Build + deploy to GitHub Pages
```

## Model Setup

Download the FunctionGemma model (requires HuggingFace account and Gemma license acceptance):
```bash
# Accept license at https://huggingface.co/google/functiongemma-270m-it
curl -L -H "Authorization: Bearer YOUR_HF_TOKEN" \
  -o public/models/tiny_garden.litertlm \
  "https://huggingface.co/google/functiongemma-270m-it/resolve/main/tiny_garden.litertlm"
```

## Architecture

### Core Flow
1. User query → FunctionGemma model via LiteRT.js
2. Model generates function call: `call:agent_name{param:value}`
3. Output parsed to extract agent and parameters
4. Mock agent gateway simulates response

### Key Services

**delegator.ts** - Singleton managing FunctionGemma inference
- `initialize()` - Load LiteRT runtime, tokenizer, and model
- `delegate(userInput)` - Returns DelegationResult with agent, extracted arguments, confidence
- Uses Transformers.js AutoTokenizer for tokenization

**prompt-builder.ts** - Builds function calling prompts with Gemma chat template
- `buildFunctionCallingPrompt()` - Creates prompt with function schemas
- `parseFunctionCallOutput()` - Extracts function name and arguments from model output

**backend-detector.ts** - Detects WebGPU support, falls back to WASM/CPU

**agent-gateway.ts** - Mock implementations for all 25 agents (simulates 300-700ms delay)

### React Hooks

**useModelLoader** - Model loading state management with progress tracking
**useDelegator** - Chat message history and delegation flow

### Component Hierarchy
```
App
├── ModelStatus (loading/ready/error display)
├── ChatHistory
│   └── MessageBubble
│       └── RoutingInfo (agent routing visualization)
└── ChatInput (query input + example buttons)
```

## Configuration

**agents.json** - Agent manifest with 25 agents. Each has:
- name, description, skills[], endpoint
- function schema with parameters for FunctionGemma

**Vite config** - Base path `/delegatorLLM/` for GitHub Pages. CORS headers required for SharedArrayBuffer (multi-threaded WASM). Copies LiteRT WASM files to `/litert-wasm/`.

## Key Types (src/types/index.ts)

- `Agent` - name, description, skills, endpoint, function?
- `FunctionSchema` - name, description, parameters
- `DelegationResult` - agent, arguments, confidence, reason
- `ChatMessage` - id, role, content, timestamp, delegation?, agentResponse?
- `InferenceStatus` - idle | loading | ready | processing | error
- `RuntimeBackend` - webgpu | cpu

## FunctionGemma Output Format

Model outputs function calls in format:
```
call:function_name{param1:value1,param2:value2}
```

Example: `call:ai_translator{text:Hello,target_language:de}`

## Browser Requirements

- Chrome/Edge 113+ (WebGPU)
- Firefox 120+ (WASM fallback)
- Safari 17+ (WASM fallback)

Model size: ~288MB (browser-cached). Tokenizer: ~33MB.
