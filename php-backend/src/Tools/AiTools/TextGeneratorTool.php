<?php
declare(strict_types=1);

namespace App\Tools\AiTools;

use App\Neuron\Agents\TextGeneratorAgent;
use NeuronAI\Chat\Messages\UserMessage;

/**
 * Text Generator Tool
 *
 * Uses the TextGeneratorAgent to create AI-powered content.
 * Supports various tones, lengths, and content types.
 */
class TextGeneratorTool
{
    /**
     * Execute text generation.
     *
     * @param string      $prompt  What to generate
     * @param string|null $context Additional context
     * @param string|null $tone    Tone: professional, casual, creative, technical
     * @param string|null $length  Length: short, medium, long
     *
     * @return string JSON encoded result
     */
    public static function execute(
        string $prompt,
        ?string $context = null,
        ?string $tone = 'professional',
        ?string $length = 'medium'
    ): string {
        try {
            // Build full prompt
            $fullPrompt = $prompt;

            if ($context) {
                $fullPrompt .= "\n\n## Context\n$context";
            }

            if ($tone) {
                $fullPrompt .= "\n\n## Tone\nWrite in a $tone tone.";
            }

            if ($length) {
                $lengthGuidance = match ($length) {
                    'short' => 'Keep it concise, around 50-100 words.',
                    'medium' => 'Aim for 150-300 words with balanced detail.',
                    'long' => 'Write comprehensively, 400-800 words.',
                    default => '',
                };
                if ($lengthGuidance) {
                    $fullPrompt .= "\n\n## Length\n$lengthGuidance";
                }
            }

            // Create agent and generate
            $agent = new TextGeneratorAgent(null, null, [
                'tone' => $tone,
                'length' => $length,
            ]);

            $response = $agent->chat(new UserMessage($fullPrompt));
            $content = $response->getContent();

            return json_encode([
                'success' => true,
                'content' => $content,
                'metadata' => [
                    'tone' => $tone,
                    'length' => $length,
                    'prompt_length' => strlen($prompt),
                    'output_length' => strlen($content),
                ],
            ]);
        } catch (\Throwable $e) {
            return json_encode([
                'error' => $e->getMessage(),
                'prompt' => $prompt,
            ]);
        }
    }

    /**
     * Generate a product description.
     */
    public static function generateProductDescription(
        string $productName,
        array $attributes = [],
        ?string $tone = 'professional'
    ): string {
        $prompt = "Write a compelling product description for: $productName";

        if (!empty($attributes)) {
            $prompt .= "\n\nProduct attributes:";
            foreach ($attributes as $key => $value) {
                $prompt .= "\n- $key: $value";
            }
        }

        return self::execute($prompt, null, $tone, 'medium');
    }

    /**
     * Generate marketing copy.
     */
    public static function generateMarketingCopy(
        string $product,
        string $targetAudience,
        ?string $callToAction = null
    ): string {
        $prompt = "Write marketing copy for $product targeting $targetAudience.";

        if ($callToAction) {
            $prompt .= "\n\nInclude a call-to-action: $callToAction";
        }

        return self::execute($prompt, null, 'creative', 'short');
    }

    /**
     * Generate SEO-optimized content.
     */
    public static function generateSeoContent(
        string $topic,
        array $keywords = [],
        ?int $wordCount = 500
    ): string {
        $prompt = "Write SEO-optimized content about: $topic";

        if (!empty($keywords)) {
            $keywordList = implode(', ', $keywords);
            $prompt .= "\n\nNaturally incorporate these keywords: $keywordList";
        }

        $length = $wordCount <= 200 ? 'short' : ($wordCount <= 400 ? 'medium' : 'long');

        return self::execute($prompt, null, 'professional', $length);
    }
}
