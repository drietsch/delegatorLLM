<?php
declare(strict_types=1);

namespace App\Neuron;

use App\Neuron\Agents\CopilotAgent;
use App\Neuron\Agents\TextGeneratorAgent;
use App\Neuron\Agents\TranslatorAgent;
use App\Neuron\Agents\SearchAgent;
use App\Neuron\Agents\GenericAgent;
use NeuronAI\Agent;

/**
 * Agent Factory
 *
 * Creates Neuron agent instances based on agent ID.
 * Routes to specialized agent classes or falls back to a generic agent.
 */
class AgentFactory
{
    private static ?ToolRegistry $registry = null;

    /**
     * Create an agent instance.
     *
     * @param string      $agentId  Agent identifier (from agents.json)
     * @param string|null $provider LLM provider override
     * @param string|null $model    Model override
     * @param array       $options  Additional options
     *
     * @return Agent
     */
    public static function create(
        string $agentId,
        ?string $provider = null,
        ?string $model = null,
        array $options = []
    ): Agent {
        $registry = self::getRegistry();

        return match ($agentId) {
            // Main orchestrator agent with access to all core tools
            'copilot', 'copilot_orchestrator' => new CopilotAgent($registry, $provider, $model, $options),

            // Specialized AI content agents
            'ai_text_generator' => new TextGeneratorAgent($provider, $model, $options),
            'ai_translator' => new TranslatorAgent($provider, $model, $options),

            // Search agent
            'advanced_search' => new SearchAgent($registry, $provider, $model, $options),

            // Fall back to generic agent for any other tool/agent
            default => new GenericAgent($agentId, $registry, $provider, $model, $options),
        };
    }

    /**
     * Get the shared ToolRegistry instance.
     */
    public static function getRegistry(): ToolRegistry
    {
        if (self::$registry === null) {
            self::$registry = new ToolRegistry();
            self::registerCoreToolHandlers(self::$registry);
        }

        return self::$registry;
    }

    /**
     * Register handlers for core tools.
     */
    private static function registerCoreToolHandlers(ToolRegistry $registry): void
    {
        // Import tool handlers
        $registry->registerHandlers([
            'advanced_search' => [\App\Tools\SearchTool::class, 'execute'],
            'data_object_manager' => [\App\Tools\DataObjectTool::class, 'execute'],
            'asset_manager' => [\App\Tools\AssetTool::class, 'execute'],
            'ai_text_generator' => [\App\Tools\AiTools\TextGeneratorTool::class, 'execute'],
            'ai_translator' => [\App\Tools\AiTools\TranslatorTool::class, 'execute'],
        ]);
    }

    /**
     * Check if an agent exists in the registry.
     */
    public static function exists(string $agentId): bool
    {
        return self::getRegistry()->has($agentId);
    }

    /**
     * Get all available agent IDs.
     */
    public static function getAvailableAgents(): array
    {
        return self::getRegistry()->getNames();
    }

    /**
     * Get agent definition from agents.json.
     */
    public static function getAgentDefinition(string $agentId): ?array
    {
        return self::getRegistry()->getAgentDefinition($agentId);
    }

    /**
     * Find agents matching a skill/capability.
     */
    public static function findBySkill(string $skill): array
    {
        $tools = self::getRegistry()->getBySkill($skill);
        return array_map(fn($t) => $t->getName(), $tools);
    }

    /**
     * Reset the registry (useful for testing).
     */
    public static function reset(): void
    {
        self::$registry = null;
    }
}
