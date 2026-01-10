<?php
declare(strict_types=1);

namespace App\Neuron;

use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Anthropic\Anthropic;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Providers\Ollama\Ollama;
use InvalidArgumentException;

/**
 * Factory for creating LLM provider instances.
 *
 * Supports Anthropic (Claude), OpenAI (GPT), and Ollama (local) providers.
 * Provider selection can be overridden per-request or defaults to config.
 */
class ProviderFactory
{
    private static ?array $config = null;

    /**
     * Create an AI provider instance.
     *
     * @param string|null $provider Provider name (anthropic, openai, ollama)
     * @param string|null $model    Model identifier (overrides config default)
     * @param array       $options  Additional provider options
     *
     * @return AIProviderInterface
     * @throws InvalidArgumentException If provider is unknown or not configured
     */
    public static function create(
        ?string $provider = null,
        ?string $model = null,
        array $options = []
    ): AIProviderInterface {
        $config = self::getConfig();
        $provider = $provider ?? $config['default'];

        if (!isset($config['providers'][$provider])) {
            throw new InvalidArgumentException("Unknown provider: $provider");
        }

        $providerConfig = $config['providers'][$provider];

        return match ($provider) {
            'anthropic' => self::createAnthropic($providerConfig, $model, $options),
            'openai' => self::createOpenAI($providerConfig, $model, $options),
            'ollama' => self::createOllama($providerConfig, $model, $options),
            default => throw new InvalidArgumentException("Unsupported provider: $provider"),
        };
    }

    /**
     * Create Anthropic (Claude) provider.
     */
    private static function createAnthropic(array $config, ?string $model, array $options): Anthropic
    {
        $key = $config['key'] ?? getenv('ANTHROPIC_API_KEY');
        if (empty($key)) {
            throw new InvalidArgumentException('ANTHROPIC_API_KEY not configured');
        }

        return new Anthropic(
            key: $key,
            model: $model ?? $config['model'] ?? 'claude-sonnet-4-20250514',
            max_tokens: $options['max_tokens'] ?? $config['max_tokens'] ?? 8192,
        );
    }

    /**
     * Create OpenAI (GPT) provider.
     */
    private static function createOpenAI(array $config, ?string $model, array $options): OpenAI
    {
        $key = $config['key'] ?? getenv('OPENAI_API_KEY');
        if (empty($key)) {
            throw new InvalidArgumentException('OPENAI_API_KEY not configured');
        }

        return new OpenAI(
            key: $key,
            model: $model ?? $config['model'] ?? 'gpt-4.1',
        );
    }

    /**
     * Create Ollama (local) provider.
     */
    private static function createOllama(array $config, ?string $model, array $options): Ollama
    {
        $baseUri = $config['base_uri'] ?? getenv('OLLAMA_URL') ?: 'http://localhost:11434';

        return new Ollama(
            baseUri: $baseUri,
            model: $model ?? $config['model'] ?? 'llama3',
        );
    }

    /**
     * Get provider configuration.
     */
    private static function getConfig(): array
    {
        if (self::$config === null) {
            $configPath = BASE_PATH . '/config/providers.php';
            if (file_exists($configPath)) {
                self::$config = require $configPath;
            } else {
                // Fallback defaults
                self::$config = [
                    'default' => 'anthropic',
                    'providers' => [
                        'anthropic' => [
                            'model' => 'claude-sonnet-4-20250514',
                            'max_tokens' => 8192,
                        ],
                        'openai' => [
                            'model' => 'gpt-4.1',
                        ],
                        'ollama' => [
                            'base_uri' => 'http://localhost:11434',
                            'model' => 'llama3',
                        ],
                    ],
                ];
            }
        }

        return self::$config;
    }

    /**
     * Get list of available providers.
     */
    public static function getAvailableProviders(): array
    {
        return array_keys(self::getConfig()['providers']);
    }

    /**
     * Check if a provider is configured and ready to use.
     */
    public static function isProviderAvailable(string $provider): bool
    {
        $config = self::getConfig();

        if (!isset($config['providers'][$provider])) {
            return false;
        }

        return match ($provider) {
            'anthropic' => !empty($config['providers']['anthropic']['key'] ?? getenv('ANTHROPIC_API_KEY')),
            'openai' => !empty($config['providers']['openai']['key'] ?? getenv('OPENAI_API_KEY')),
            'ollama' => true, // Ollama doesn't require API key
            default => false,
        };
    }
}
