<?php
declare(strict_types=1);

/**
 * LLM Provider Configuration
 *
 * Defines available providers and their default settings.
 * Environment variables take precedence over these defaults.
 */

return [
    'default' => getenv('DEFAULT_PROVIDER') ?: 'anthropic',

    'providers' => [
        'anthropic' => [
            'key' => getenv('ANTHROPIC_API_KEY') ?: '',
            'model' => getenv('ANTHROPIC_MODEL') ?: 'claude-sonnet-4-20250514',
            'max_tokens' => (int) (getenv('ANTHROPIC_MAX_TOKENS') ?: 8192),
        ],

        'openai' => [
            'key' => getenv('OPENAI_API_KEY') ?: '',
            'model' => getenv('OPENAI_MODEL') ?: 'gpt-4.1',
            'max_tokens' => (int) (getenv('OPENAI_MAX_TOKENS') ?: 4096),
        ],

        'ollama' => [
            'base_uri' => getenv('OLLAMA_URL') ?: 'http://localhost:11434',
            'model' => getenv('OLLAMA_MODEL') ?: 'llama3',
        ],
    ],

    // Tool execution settings
    'tools' => [
        'max_tries' => 5,
        'timeout' => 30,
    ],

    // Streaming settings
    'streaming' => [
        'chunk_size' => 1024,
        'flush_interval' => 10, // milliseconds
    ],
];
