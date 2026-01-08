import { AutoModelForCausalLM, AutoTokenizer } from '@huggingface/transformers';
import type { DelegationResult, LoadingProgress, RuntimeBackend, Agent } from '../types';
import agentManifest from '../config/agents.json';

// Model configuration - Game model (fine-tuned for function calling)
const MODEL_ID = 'Xenova/functiongemma-270m-game';

/**
 * FunctionGemma Delegator Service
 * Uses Transformers.js for browser-based function calling inference
 */
class DelegatorService {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private model: any = null;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private tokenizer: any = null;
  private backend: RuntimeBackend = 'webgpu';
  private isInitializing = false;

  /**
   * Check if the model is ready for inference
   */
  get isReady(): boolean {
    return this.model !== null && this.tokenizer !== null;
  }

  /**
   * Get the current runtime backend
   */
  get currentBackend(): RuntimeBackend {
    return this.backend;
  }

  /**
   * Get available agents
   */
  get agents(): Agent[] {
    return agentManifest.agents as unknown as Agent[];
  }

  /**
   * Build simplified tool schema (game model format)
   */
  private buildToolSchema() {
    return this.agents
      .filter((a) => a.function)
      .slice(0, 8) // Limit for model context
      .map((agent) => ({
        type: 'function',
        function: {
          name: agent.function!.name,
          description: agent.function!.description.split('.')[0], // First sentence only
          parameters: {
            type: 'object',
            properties: Object.fromEntries(
              Object.entries(agent.function!.parameters.properties).map(([k, v]) => [
                k,
                { type: (v as {type: string}).type, description: (v as {description: string}).description.split('.')[0] }
              ])
            ),
            required: agent.function!.parameters.required || [],
          },
        },
      }));
  }

  /**
   * Initialize the delegator with FunctionGemma model
   */
  async initialize(
    onProgress?: (progress: LoadingProgress) => void
  ): Promise<void> {
    if (this.model || this.isInitializing) {
      return;
    }

    this.isInitializing = true;

    try {
      // 1. Load tokenizer
      onProgress?.({ status: 'Loading tokenizer...' });
      console.log('[Delegator] Loading tokenizer:', MODEL_ID);
      this.tokenizer = await AutoTokenizer.from_pretrained(MODEL_ID);
      console.log('[Delegator] Tokenizer loaded');

      // 2. Load model with WebGPU
      onProgress?.({ status: 'Loading FunctionGemma model (WebGPU)...' });
      console.log('[Delegator] Loading model:', MODEL_ID);
      this.model = await AutoModelForCausalLM.from_pretrained(MODEL_ID, {
        device: 'webgpu',
        dtype: 'q4',
      });
      console.log('[Delegator] Model loaded successfully');

      onProgress?.({ status: 'Model loaded successfully!' });
    } catch (error) {
      const errorMsg = error instanceof Error ? error.message : String(error);
      console.error('[Delegator] Initialization failed:', errorMsg);
      console.error('[Delegator] Full error:', error);
      onProgress?.({ status: `Error: ${errorMsg}` });
      throw error;
    } finally {
      this.isInitializing = false;
    }
  }

  /**
   * Delegate a user request using FunctionGemma function calling
   */
  async delegate(userInput: string): Promise<DelegationResult> {
    if (!this.model || !this.tokenizer) {
      throw new Error('Delegator not initialized. Call initialize() first.');
    }

    console.log('[Delegator] User input:', userInput);

    try {
      // 1. Build tool schema JSON
      const tools = this.buildToolSchema();

      // 2. Build prompt manually (game model format)
      const prompt = `<start_of_turn>developer
You are a model that can do function calling with the following functions:
${JSON.stringify(tools, null, 2)}
<end_of_turn>
<start_of_turn>user
${userInput}
<end_of_turn>
<start_of_turn>model
`;

      // 3. Tokenize
      const inputs = await this.tokenizer(prompt, {
        return_tensors: 'pt',
        padding: false,
      });

      console.log('[Delegator] Input tokens:', inputs.input_ids.dims);

      // 3. Generate response
      const output = await this.model.generate({
        ...inputs,
        max_new_tokens: 128,
        do_sample: false,
      });

      // 4. Decode output (only new tokens)
      const decoded = this.tokenizer.decode(
        output.slice(null, [inputs.input_ids.dims[1], null]),
        { skip_special_tokens: false }
      );
      console.log('[Delegator] Output:', decoded);

      // 5. Parse function call from output
      const parsed = this.parseFunctionCall(decoded);

      if (parsed.functionName) {
        const agent = this.agents.find(
          (a) => a.function?.name === parsed.functionName || a.name === parsed.functionName
        );

        return {
          agent: parsed.functionName,
          arguments: parsed.arguments,
          confidence: 0.95,
          reason: agent?.description.split('.')[0] || `Function call: ${parsed.functionName}`,
          rawOutput: decoded,
        };
      }

      return {
        agent: 'unknown',
        arguments: { query: userInput },
        confidence: 0.3,
        reason: 'Could not parse function call from model output',
        rawOutput: decoded,
      };
    } catch (error) {
      console.error('[Delegator] Inference failed:', error);
      throw error;
    }
  }

  /**
   * Parse function call from model output
   */
  private parseFunctionCall(output: string): {
    functionName: string | null;
    arguments: Record<string, unknown>;
  } {
    const startTag = '<start_function_call>';
    const endTag = '<end_function_call>';
    const startIndex = output.indexOf(startTag);
    const endIndex = output.indexOf(endTag);

    if (startIndex === -1 || endIndex === -1) {
      return { functionName: null, arguments: {} };
    }

    const callStr = output.substring(startIndex + startTag.length, endIndex).trim();

    // Parse: call:function_name{args}
    const callMatch = callStr.match(/^call:(\w+)\s*(\{[\s\S]*\})?$/);
    if (!callMatch) {
      return { functionName: null, arguments: {} };
    }

    const functionName = callMatch[1];
    let args: Record<string, unknown> = {};

    if (callMatch[2]) {
      try {
        // Sanitize to valid JSON
        let argsStr = callMatch[2]
          .replace(/<escape>(.*?)<escape>/g, '"$1"')
          .replace(/(\w+):/g, '"$1":')
          .replace(/'/g, '"');

        args = JSON.parse(argsStr);
      } catch (e) {
        console.warn('[Delegator] Failed to parse arguments:', e);
      }
    }

    return { functionName, arguments: args };
  }

  /**
   * Dispose of the model and free resources
   */
  dispose(): void {
    this.model = null;
    this.tokenizer = null;
  }
}

// Export singleton instance
export const delegatorService = new DelegatorService();
