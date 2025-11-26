import type { Agent } from '../types';

/**
 * Build a concise system prompt optimized for small models (0.5B-1B params)
 * Uses few-shot examples and short agent list
 */
export function buildSystemPrompt(agents: Agent[]): string {
  // Create a very concise agent list - just name and key purpose
  const agentList = agents
    .map((a) => {
      const shortDesc = a.description.split('.')[0].substring(0, 60);
      return `- ${a.name}: ${shortDesc}`;
    })
    .join('\n');

  return `Route requests to the best agent. Output JSON only.

AGENTS:
${agentList}

EXAMPLES:
User: "Write a blog post"
{"agent":"ai_text_generator","confidence":0.9,"reason":"text generation"}

User: "Create product image"
{"agent":"ai_image_generator","confidence":0.9,"reason":"image creation"}

User: "Translate to French"
{"agent":"ai_translator","confidence":0.9,"reason":"translation task"}

User: "Find products"
{"agent":"advanced_search","confidence":0.8,"reason":"search query"}

User: "Show my cart"
{"agent":"ecommerce_cart","confidence":0.9,"reason":"cart operation"}

Now route this request. Output ONLY the JSON:`;
}

/**
 * Format messages for the model using Qwen's chat template
 * Qwen uses: <|im_start|>system\n{system}<|im_end|>\n<|im_start|>user\n{user}<|im_end|>\n<|im_start|>assistant\n
 */
export function formatChatMessages(
  systemPrompt: string,
  userMessage: string
): Array<{ role: string; content: string }> {
  return [
    { role: 'system', content: systemPrompt },
    { role: 'user', content: userMessage },
  ];
}

/**
 * Format as a single text prompt (alternative approach for small models)
 */
export function formatTextPrompt(
  systemPrompt: string,
  userMessage: string
): string {
  return `<|im_start|>system
${systemPrompt}<|im_end|>
<|im_start|>user
${userMessage}<|im_end|>
<|im_start|>assistant
`;
}
