<?php
declare(strict_types=1);

namespace App\Neuron\Agents;

use App\Neuron\ProviderFactory;
use App\Neuron\ToolRegistry;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;

/**
 * Generic Agent
 *
 * Fallback agent for any agent ID not explicitly handled.
 * Uses the agent definition from agents.json to configure behavior.
 */
class GenericAgent extends Agent
{
    private string $agentId;
    private ToolRegistry $registry;
    private ?string $providerName;
    private ?string $modelName;
    private array $options;
    private ?array $agentDefinition;

    public function __construct(
        string $agentId,
        ToolRegistry $registry,
        ?string $provider = null,
        ?string $model = null,
        array $options = []
    ) {
        $this->agentId = $agentId;
        $this->registry = $registry;
        $this->providerName = $provider;
        $this->modelName = $model;
        $this->options = $options;
        $this->agentDefinition = $registry->getAgentDefinition($agentId);
    }

    protected function provider(): AIProviderInterface
    {
        return ProviderFactory::create($this->providerName, $this->modelName);
    }

    public function instructions(): string
    {
        if (!$this->agentDefinition) {
            return "You are a helpful AI assistant.";
        }

        $name = $this->agentDefinition['name'] ?? $this->agentId;
        $description = $this->agentDefinition['description'] ?? '';
        $skills = implode(', ', $this->agentDefinition['skills'] ?? []);

        return <<<PROMPT
You are the {$name} agent.

## Description
{$description}

## Skills
{$skills}

## Guidelines

1. Focus on tasks related to your described capabilities
2. Use available tools when they can help
3. Be clear about what you can and cannot do
4. Ask for clarification if the request is ambiguous
5. Provide structured, actionable responses

If a request is outside your capabilities, explain what you can help with instead.
PROMPT;
    }

    protected function tools(): array
    {
        // Generic agent gets its own tool if available
        if ($this->registry->has($this->agentId)) {
            return $this->registry->getTools([$this->agentId]);
        }

        return [];
    }

    /**
     * Get the agent ID.
     */
    public function getAgentId(): string
    {
        return $this->agentId;
    }

    /**
     * Get the agent definition from agents.json.
     */
    public function getDefinition(): ?array
    {
        return $this->agentDefinition;
    }
}
