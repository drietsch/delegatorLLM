import type { Agent, FunctionSchema } from '../types';

/**
 * Build a function calling prompt for FunctionGemma
 * Uses the Gemma chat template format with developer role for function calling
 */
export function buildFunctionCallingPrompt(
  userQuery: string,
  agents: Agent[]
): string {
  // Extract function schemas from agents that have them
  const functionSchemas = agents
    .filter((a) => a.function)
    .map((a) => a.function as FunctionSchema);

  // Format functions for the prompt
  const functionsJson = JSON.stringify(functionSchemas, null, 2);

  // Gemma chat template with developer role (activates function calling mode)
  return `<start_of_turn>developer
You are a model that can do function calling with the following functions:
${functionsJson}
<end_of_turn>
<start_of_turn>user
${userQuery}
<end_of_turn>
<start_of_turn>model
`;
}

/**
 * Parse FunctionGemma output to extract function call
 * Expected format: call:function_name{param1:value1,param2:value2}
 */
export function parseFunctionCallOutput(output: string): {
  functionName: string | null;
  arguments: Record<string, unknown>;
  rawOutput: string;
} {
  // Match the function call pattern
  // Format: call:function_name{param1:value1,param2:value2,...}
  const callMatch = output.match(/call:(\w+)\{([^}]*)\}/);

  if (!callMatch) {
    return {
      functionName: null,
      arguments: {},
      rawOutput: output,
    };
  }

  const [, functionName, paramsStr] = callMatch;
  const args: Record<string, unknown> = {};

  if (paramsStr) {
    // Parse parameters: key:value pairs separated by commas
    // Handle escaped values with <escape> markers
    const paramPairs = paramsStr.split(',');

    for (const pair of paramPairs) {
      const colonIndex = pair.indexOf(':');
      if (colonIndex === -1) continue;

      const key = pair.slice(0, colonIndex).trim();
      let value = pair.slice(colonIndex + 1).trim();

      // Remove <escape> markers if present
      value = value.replace(/<escape>/g, '');

      // Try to parse as JSON for complex types
      try {
        args[key] = JSON.parse(value);
      } catch {
        // Keep as string if not valid JSON
        args[key] = value;
      }
    }
  }

  return {
    functionName,
    arguments: args,
    rawOutput: output,
  };
}
