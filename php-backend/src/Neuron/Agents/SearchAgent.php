<?php
declare(strict_types=1);

namespace App\Neuron\Agents;

use App\Neuron\ProviderFactory;
use App\Neuron\ToolRegistry;
use NeuronAI\Agent;
use NeuronAI\Providers\AIProviderInterface;

/**
 * Search Agent
 *
 * Specialized agent for searching and retrieving data.
 * Can search products, assets, documents, and other data objects.
 */
class SearchAgent extends Agent
{
    private ToolRegistry $registry;
    private ?string $providerName;
    private ?string $modelName;
    private array $options;

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

    protected function provider(): AIProviderInterface
    {
        return ProviderFactory::create($this->providerName, $this->modelName);
    }

    protected function instructions(): string
    {
        return <<<'PROMPT'
You are a search assistant specialized in finding and presenting data from the Pimcore system.

## Your Capabilities

You can search for:
- **Products**: Search by name, SKU, category, attributes
- **Assets**: Search images, videos, documents by filename, tags, metadata
- **Documents**: Search CMS pages, snippets, emails
- **Customers**: Search customer profiles and data

## How to Search

Use the `advanced_search` tool with appropriate parameters:
- `query`: The search terms
- `object_type`: Type to search (product, asset, document, customer)
- `filters`: JSON object with attribute filters
- `limit`: Maximum results to return

## Response Guidelines

1. **Understand the query**: Identify what the user is looking for
2. **Construct effective searches**: Use specific terms and filters
3. **Present results clearly**: Format results in tables or lists
4. **Provide context**: Explain what was found and relevance
5. **Suggest refinements**: If results aren't ideal, suggest filter changes

## Example Searches

- "Find red shoes under $100" → search products with color=red, price<100
- "Show laptop images" → search assets with type=image, tags contain laptop
- "List VIP customers" → search customers with segment=VIP
PROMPT;
    }

    protected function tools(): array
    {
        return $this->registry->getTools(['advanced_search']);
    }
}
