<?php
declare(strict_types=1);

namespace App\Neuron;

use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use NeuronAI\Tools\PropertyType;

/**
 * Tool Registry
 *
 * Manages tool definitions loaded from agents.json.
 * Converts JSON Schema tool definitions to Neuron Tool instances.
 */
class ToolRegistry
{
    private array $tools = [];
    private array $handlers = [];
    private array $agentDefinitions = [];

    /**
     * Create a new ToolRegistry from agents.json.
     */
    public function __construct(?string $agentsJsonPath = null)
    {
        $path = $agentsJsonPath ?? BASE_PATH . '/agents.json';

        if (!file_exists($path)) {
            throw new \RuntimeException("agents.json not found: $path");
        }

        $data = json_decode(file_get_contents($path), true);

        if (!isset($data['agents']) || !is_array($data['agents'])) {
            throw new \RuntimeException("Invalid agents.json format");
        }

        foreach ($data['agents'] as $agent) {
            $this->agentDefinitions[$agent['name']] = $agent;
            $tool = $this->createToolFromSchema($agent);
            $this->tools[$agent['name']] = $tool;
        }
    }

    /**
     * Register a handler function for a tool.
     *
     * @param string   $name    Tool name
     * @param callable $handler Function to execute when tool is called
     */
    public function registerHandler(string $name, callable $handler): void
    {
        $this->handlers[$name] = $handler;

        if (isset($this->tools[$name])) {
            $this->tools[$name]->setCallable($handler);
        }
    }

    /**
     * Register multiple handlers at once.
     */
    public function registerHandlers(array $handlers): void
    {
        foreach ($handlers as $name => $handler) {
            $this->registerHandler($name, $handler);
        }
    }

    /**
     * Get a single tool by name.
     */
    public function get(string $name): ?Tool
    {
        return $this->tools[$name] ?? null;
    }

    /**
     * Get multiple tools by name.
     *
     * @param array|null $names Tool names (null = all tools)
     * @return Tool[]
     */
    public function getTools(?array $names = null): array
    {
        if ($names === null) {
            return array_values($this->tools);
        }

        $result = [];
        foreach ($names as $name) {
            if (isset($this->tools[$name])) {
                $result[] = $this->tools[$name];
            }
        }

        return $result;
    }

    /**
     * Get all tool names.
     */
    public function getNames(): array
    {
        return array_keys($this->tools);
    }

    /**
     * Get agent definition by name.
     */
    public function getAgentDefinition(string $name): ?array
    {
        return $this->agentDefinitions[$name] ?? null;
    }

    /**
     * Check if a tool exists.
     */
    public function has(string $name): bool
    {
        return isset($this->tools[$name]);
    }

    /**
     * Check if a tool has a registered handler.
     */
    public function hasHandler(string $name): bool
    {
        return isset($this->handlers[$name]);
    }

    /**
     * Get tools by skill (fuzzy matching against skills array).
     */
    public function getBySkill(string $skill): array
    {
        $matches = [];
        $skill = strtolower($skill);

        foreach ($this->agentDefinitions as $name => $agent) {
            $skills = array_map('strtolower', $agent['skills'] ?? []);

            foreach ($skills as $s) {
                if (str_contains($s, $skill) || str_contains($skill, $s)) {
                    $matches[] = $this->tools[$name];
                    break;
                }
            }
        }

        return $matches;
    }

    /**
     * Create a Neuron Tool from an agent schema definition.
     */
    private function createToolFromSchema(array $agent): Tool
    {
        $function = $agent['function'] ?? [];
        $parameters = $function['parameters'] ?? [];
        $properties = $parameters['properties'] ?? [];
        $required = $parameters['required'] ?? [];

        $toolProperties = [];

        foreach ($properties as $propName => $propSchema) {
            $type = $this->mapJsonSchemaType($propSchema['type'] ?? 'string');

            $toolProperties[] = ToolProperty::make(
                name: $propName,
                type: $type,
                description: $propSchema['description'] ?? '',
                required: in_array($propName, $required),
            );
        }

        $tool = Tool::make(
            name: $function['name'] ?? $agent['name'],
            description: $function['description'] ?? $agent['description'] ?? '',
            properties: $toolProperties,
        );

        // If a handler is already registered, apply it
        if (isset($this->handlers[$agent['name']])) {
            $tool->setCallable($this->handlers[$agent['name']]);
        }

        return $tool;
    }

    /**
     * Map JSON Schema type to Neuron PropertyType.
     */
    private function mapJsonSchemaType(string $type): PropertyType
    {
        return match (strtolower($type)) {
            'string' => PropertyType::STRING,
            'integer', 'int' => PropertyType::INTEGER,
            'number', 'float', 'double' => PropertyType::NUMBER,
            'boolean', 'bool' => PropertyType::BOOLEAN,
            'array' => PropertyType::ARRAY,
            'object' => PropertyType::OBJECT,
            default => PropertyType::STRING,
        };
    }

    /**
     * Export tool definitions as JSON (for debugging).
     */
    public function toJson(): string
    {
        $export = [];

        foreach ($this->tools as $name => $tool) {
            $export[$name] = [
                'name' => $tool->getName(),
                'description' => $tool->getDescription(),
                'hasHandler' => isset($this->handlers[$name]),
            ];
        }

        return json_encode($export, JSON_PRETTY_PRINT);
    }
}
