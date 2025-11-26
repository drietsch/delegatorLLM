import { useState, useCallback, type FormEvent, type KeyboardEvent } from 'react';

interface ChatInputProps {
  onSubmit: (message: string) => void;
  disabled?: boolean;
  placeholder?: string;
  showExamples?: boolean;
}

// Example queries mapped to agents from agents.json
const exampleQueries = [
  { text: 'Write a product description for headphones', agent: 'ai_text_generator' },
  { text: 'Create an image of a sunset over mountains', agent: 'ai_image_generator' },
  { text: 'Übersetze ins Französische', agent: 'ai_translator' },
  { text: 'Classify this product into categories', agent: 'ai_classifier' },
  { text: 'Show all products in electronics', agent: 'data_object_manager' },
  { text: 'Upload and manage product images', agent: 'asset_manager' },
  { text: 'Search for winter jackets', agent: 'advanced_search' },
  { text: 'Import products from CSV file', agent: 'data_importer' },
  { text: 'Show my shopping cart', agent: 'ecommerce_cart' },
  { text: 'Check data quality score', agent: 'data_quality' },
  { text: 'Send newsletter to subscribers', agent: 'newsletter_manager' },
  { text: 'Generate product variants for sizes', agent: 'variant_generator' },
];

export function ChatInput({
  onSubmit,
  disabled = false,
  placeholder = 'Type your request...',
  showExamples = false,
}: ChatInputProps) {
  const [input, setInput] = useState('');

  const handleSubmit = useCallback(
    (e: FormEvent) => {
      e.preventDefault();
      if (input.trim() && !disabled) {
        onSubmit(input.trim());
        setInput('');
      }
    },
    [input, disabled, onSubmit]
  );

  const handleKeyDown = useCallback(
    (e: KeyboardEvent<HTMLTextAreaElement>) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (input.trim() && !disabled) {
          onSubmit(input.trim());
          setInput('');
        }
      }
    },
    [input, disabled, onSubmit]
  );

  const handleExampleClick = useCallback(
    (query: string) => {
      if (!disabled) {
        onSubmit(query);
      }
    },
    [disabled, onSubmit]
  );

  return (
    <div className="chat-input-container">
      {showExamples && (
        <div className="example-queries">
          <span className="example-label">Try:</span>
          {exampleQueries.slice(0, 6).map((example, index) => (
            <button
              key={index}
              className="example-button"
              onClick={() => handleExampleClick(example.text)}
              disabled={disabled}
              title={`Routes to: ${example.agent}`}
            >
              {example.text}
            </button>
          ))}
        </div>
      )}
      <form className="chat-input" onSubmit={handleSubmit}>
        <textarea
          value={input}
          onChange={(e) => setInput(e.target.value)}
          onKeyDown={handleKeyDown}
          placeholder={placeholder}
          disabled={disabled}
          rows={2}
        />
        <button type="submit" disabled={disabled || !input.trim()}>
          {disabled ? 'Processing...' : 'Send'}
        </button>
      </form>
    </div>
  );
}
