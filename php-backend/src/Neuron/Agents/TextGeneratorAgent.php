<?php
declare(strict_types=1);

namespace App\Neuron\Agents;

use App\Neuron\ProviderFactory;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;

/**
 * Text Generator Agent
 *
 * Specialized agent for AI-powered text content generation.
 * Supports various content types, tones, and lengths.
 */
class TextGeneratorAgent extends Agent
{
    private ?string $providerName;
    private ?string $modelName;
    private array $options;

    public function __construct(
        ?string $provider = null,
        ?string $model = null,
        array $options = []
    ) {
        $this->providerName = $provider;
        $this->modelName = $model;
        $this->options = $options;
    }

    protected function provider(): AIProviderInterface
    {
        return ProviderFactory::create($this->providerName, $this->modelName);
    }

    protected function instructions(): string
    {
        $tone = $this->options['tone'] ?? 'professional';
        $length = $this->options['length'] ?? 'medium';

        return <<<PROMPT
You are an expert content writer specializing in creating high-quality text content.

## Your Role
Generate engaging, well-structured content based on user requirements.

## Guidelines

### Tone: {$tone}
- Professional: Clear, authoritative, business-appropriate
- Casual: Friendly, conversational, approachable
- Creative: Imaginative, unique, attention-grabbing
- Technical: Precise, detailed, informative

### Length: {$length}
- Short: 50-100 words, punchy and concise
- Medium: 150-300 words, balanced detail
- Long: 400-800 words, comprehensive coverage

### Best Practices
1. Start with a hook or compelling opening
2. Structure content logically with clear paragraphs
3. Use active voice and strong verbs
4. Include relevant keywords naturally
5. End with a clear call-to-action when appropriate

### Content Types
- Product descriptions
- Marketing copy
- Blog posts and articles
- Email content
- Social media posts
- Technical documentation

When generating content, focus on:
- Clarity and readability
- SEO-friendly structure
- Brand voice consistency
- Value for the reader
PROMPT;
    }

    protected function tools(): array
    {
        // Text generator doesn't need tools - it generates directly
        return [];
    }
}
