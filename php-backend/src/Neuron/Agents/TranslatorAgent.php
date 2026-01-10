<?php
declare(strict_types=1);

namespace App\Neuron\Agents;

use App\Neuron\ProviderFactory;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;

/**
 * Translator Agent
 *
 * Specialized agent for multi-language translation.
 * Maintains context, tone, and technical accuracy.
 */
class TranslatorAgent extends Agent
{
    private ?string $providerName;
    private ?string $modelName;
    private array $options;

    // Supported languages
    public const LANGUAGES = [
        'en' => 'English',
        'de' => 'German',
        'fr' => 'French',
        'es' => 'Spanish',
        'it' => 'Italian',
        'pt' => 'Portuguese',
        'nl' => 'Dutch',
        'pl' => 'Polish',
        'ru' => 'Russian',
        'zh' => 'Chinese',
        'ja' => 'Japanese',
        'ko' => 'Korean',
    ];

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
        $sourceLanguage = $this->options['source_language'] ?? 'auto';
        $targetLanguage = $this->options['target_language'] ?? 'en';

        return <<<PROMPT
You are an expert translator specializing in accurate, natural-sounding translations.

## Translation Guidelines

### Source Language
{$sourceLanguage} (auto-detect if not specified)

### Target Language
{$targetLanguage}

### Quality Standards

1. **Accuracy**: Preserve the exact meaning of the original text
2. **Naturalness**: Use native expressions and idioms in the target language
3. **Context**: Consider the context and intended use of the text
4. **Consistency**: Maintain consistent terminology throughout

### Special Handling

- **Technical terms**: Keep technical/product terminology accurate
- **Brand names**: Keep brand names unchanged
- **Numbers & units**: Adapt formatting to target locale (dates, currencies)
- **Cultural references**: Adapt when necessary for local understanding

### Output Format

Provide only the translated text without explanations unless:
- The user explicitly asks for notes
- There are ambiguities that require clarification
- Important nuances might be lost in translation

### Supported Languages
English, German, French, Spanish, Italian, Portuguese, Dutch, Polish, Russian, Chinese, Japanese, Korean
PROMPT;
    }

    protected function tools(): array
    {
        return [];
    }

    /**
     * Get supported languages.
     */
    public static function getSupportedLanguages(): array
    {
        return self::LANGUAGES;
    }
}
