import { useState, useCallback, type FormEvent, type KeyboardEvent } from 'react';

interface ChatInputProps {
  onSubmit: (message: string) => void;
  disabled?: boolean;
  placeholder?: string;
}

export function ChatInput({
  onSubmit,
  disabled = false,
  placeholder = 'Type your request...',
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

  return (
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
  );
}
