<?php
declare(strict_types=1);

namespace App\Neuron\Agents;

use App\Neuron\ProviderFactory;
use App\Neuron\ToolRegistry;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;

/**
 * Copilot Agent
 *
 * The main orchestrator agent with access to all core tools.
 * Can delegate to specialized tools for content generation, search,
 * data management, and more.
 */
class CopilotAgent extends Agent
{
    private ToolRegistry $registry;
    private ?string $providerName;
    private ?string $modelName;
    private array $options;

    // Core tools this agent has access to
    private const CORE_TOOLS = [
        'advanced_search',
        'ai_text_generator',
        'ai_translator',
        'data_object_manager',
        'asset_manager',
    ];

    public function __construct(
        ToolRegistry $registry,
        ?string $provider = null,
        ?string $model = null,
        array $options = []
    ) {
        $this->registry = $registry;
        $this->providerName = $provider;
        $this->modelName = $model;
        $this->options = $options;
    }

    /**
     * Configure the LLM provider.
     */
    protected function provider(): AIProviderInterface
    {
        return ProviderFactory::create($this->providerName, $this->modelName);
    }

    /**
     * Define the system instructions for the agent.
     */
    protected function instructions(): string
    {
        return <<<'PROMPT'
You are a Pimcore Copilot assistant - an intelligent AI helper for content management,
data operations, e-commerce, and AI-powered content generation.

## Your Capabilities

You have access to specialized tools for various tasks:

### Content Generation & AI
- **ai_text_generator**: Generate product descriptions, marketing copy, blog posts, and other content
- **ai_translator**: Translate content between languages (supports 10+ languages)

### Search & Retrieval
- **advanced_search**: Search for products, assets, documents by keywords and filters

### Data Management
- **data_object_manager**: Create, read, update, delete data objects (products, categories, etc.)
- **asset_manager**: Manage digital assets (images, videos, documents)

## Guidelines

1. **Always use tools** when they can help answer the user's question. Don't make up data.
2. **Be specific** about what data you need when using search tools.
3. **Explain your actions** briefly before executing tools.
4. **Handle errors gracefully** - if a tool fails, explain what happened and suggest alternatives.
5. **Stay focused** on Pimcore-related tasks and data management.

## Response Style

- Be concise but helpful
- Use markdown formatting for readability
- When displaying data, format it clearly (tables, lists)
- If asked about capabilities, explain what tools are available
PROMPT;
    }

    /**
     * Define the tools available to this agent.
     */
    protected function tools(): array
    {
        return $this->registry->getTools(self::CORE_TOOLS);
    }
}
