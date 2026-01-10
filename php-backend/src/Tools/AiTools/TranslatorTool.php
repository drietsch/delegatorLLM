<?php
declare(strict_types=1);

namespace App\Tools\AiTools;

use App\Neuron\Agents\TranslatorAgent;
use NeuronAI\Chat\Messages\UserMessage;

/**
 * Translator Tool
 *
 * Uses the TranslatorAgent for multi-language translation.
 * Maintains context, tone, and technical accuracy.
 */
class TranslatorTool
{
    /**
     * Execute translation.
     *
     * @param string      $text            Text to translate
     * @param string|null $source_language Source language code (e.g., 'en', 'de') or 'auto'
     * @param string      $target_language Target language code
     *
     * @return string JSON encoded result
     */
    public static function execute(
        string $text,
        ?string $source_language = 'auto',
        string $target_language = 'en'
    ): string {
        try {
            // Build prompt
            $sourceLabel = self::getLanguageName($source_language ?? 'auto');
            $targetLabel = self::getLanguageName($target_language);

            $prompt = "Translate the following text from $sourceLabel to $targetLabel:\n\n$text";

            // Create agent with language options
            $agent = new TranslatorAgent(null, null, [
                'source_language' => $source_language,
                'target_language' => $target_language,
            ]);

            $response = $agent->chat(new UserMessage($prompt));
            $translation = $response->getContent();

            return json_encode([
                'success' => true,
                'original' => $text,
                'translation' => $translation,
                'source_language' => $source_language ?? 'auto',
                'target_language' => $target_language,
                'metadata' => [
                    'original_length' => strlen($text),
                    'translation_length' => strlen($translation),
                ],
            ]);
        } catch (\Throwable $e) {
            return json_encode([
                'error' => $e->getMessage(),
                'text' => $text,
                'target_language' => $target_language,
            ]);
        }
    }

    /**
     * Batch translate multiple texts.
     */
    public static function batchTranslate(
        array $texts,
        string $targetLanguage,
        ?string $sourceLanguage = 'auto'
    ): string {
        $results = [];

        foreach ($texts as $index => $text) {
            $result = json_decode(
                self::execute($text, $sourceLanguage, $targetLanguage),
                true
            );

            $results[] = [
                'index' => $index,
                'original' => $text,
                'translation' => $result['translation'] ?? null,
                'error' => $result['error'] ?? null,
            ];
        }

        $successful = count(array_filter($results, fn($r) => $r['translation'] !== null));

        return json_encode([
            'success' => true,
            'total' => count($texts),
            'successful' => $successful,
            'failed' => count($texts) - $successful,
            'target_language' => $targetLanguage,
            'results' => $results,
        ]);
    }

    /**
     * Get full language name from code.
     */
    private static function getLanguageName(string $code): string
    {
        $languages = TranslatorAgent::LANGUAGES;

        if ($code === 'auto') {
            return 'the detected language';
        }

        return $languages[$code] ?? $code;
    }

    /**
     * Get list of supported languages.
     */
    public static function getSupportedLanguages(): array
    {
        return TranslatorAgent::LANGUAGES;
    }

    /**
     * Detect language of text (simplified - returns 'auto' for now).
     */
    public static function detectLanguage(string $text): string
    {
        // In a real implementation, this would use language detection
        // For now, we return 'auto' and let the LLM detect
        return json_encode([
            'detected' => 'auto',
            'confidence' => 0.0,
            'note' => 'Language detection delegated to LLM',
        ]);
    }
}
