import type { ChatMessage } from '../types';
import { RoutingInfo } from './RoutingInfo';

interface MessageBubbleProps {
  message: ChatMessage;
}

export function MessageBubble({ message }: MessageBubbleProps) {
  const { role, content, delegation } = message;

  return (
    <div className={`message-bubble ${role}`}>
      <div className="message-header">
        <span className="role-label">
          {role === 'user' ? 'You' : role === 'assistant' ? 'Delegator' : 'System'}
        </span>
        <span className="timestamp">
          {message.timestamp.toLocaleTimeString()}
        </span>
      </div>
      <div className="message-content">
        {content.split('\n').map((line, i) => (
          <p key={i}>{line || '\u00A0'}</p>
        ))}
      </div>
      {delegation && <RoutingInfo delegation={delegation} />}
    </div>
  );
}
