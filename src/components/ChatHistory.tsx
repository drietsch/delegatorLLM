import { useEffect, useRef } from 'react';
import type { ChatMessage } from '../types';
import { MessageBubble } from './MessageBubble';

interface ChatHistoryProps {
  messages: ChatMessage[];
  isProcessing?: boolean;
}

export function ChatHistory({ messages, isProcessing }: ChatHistoryProps) {
  const bottomRef = useRef<HTMLDivElement>(null);

  // Auto-scroll to bottom on new messages
  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  if (messages.length === 0) {
    return (
      <div className="chat-history empty">
        <p>No messages yet</p>
        <p className="hint">
          Try asking something like:
          <br />
          "Write a blog post about AI"
          <br />
          "Debug this JavaScript code"
          <br />
          "Optimize my website for SEO"
        </p>
      </div>
    );
  }

  return (
    <div className="chat-history">
      {messages.map((message) => (
        <MessageBubble key={message.id} message={message} />
      ))}
      {isProcessing && (
        <div className="message-bubble assistant processing">
          <div className="typing-indicator">
            <span />
            <span />
            <span />
          </div>
        </div>
      )}
      <div ref={bottomRef} />
    </div>
  );
}
