import { Tooltip, Avatar } from 'antd';
import { RobotOutlined, ExclamationOutlined } from '@ant-design/icons';
import type { Session, SessionStatus } from '../../types/session';
import './PowerStrip.css';

interface SessionTokenProps {
  session: Session;
  isActive: boolean;
  onClick: () => void;
}

const getStatusClass = (status: SessionStatus): string => {
  switch (status) {
    case 'routing':
    case 'working':
    case 'streaming':
      return 'token-working';
    default:
      return '';
  }
};

const getAvatarStyle = (status: SessionStatus, isActive: boolean): React.CSSProperties => {
  const baseStyle: React.CSSProperties = {
    cursor: 'pointer',
    transition: 'all 0.2s cubic-bezier(0.4, 0, 0.2, 1)',
    borderRadius: 10,
  };

  if (isActive) {
    return {
      ...baseStyle,
      boxShadow: '0 0 0 2px rgba(255,255,255,0.9), 0 4px 12px rgba(0,0,0,0.2)',
      opacity: 1,
    };
  }

  return {
    ...baseStyle,
    opacity: status === 'completed' ? 0.85 : 0.55,
  };
};

const getAvatarColor = (status: SessionStatus): string => {
  switch (status) {
    case 'routing':
      return '#3b82f6'; // Blue
    case 'working':
    case 'streaming':
      return '#8b5cf6'; // Purple
    case 'completed':
      return '#10b981'; // Green
    case 'error':
      return '#ef4444'; // Red
    default:
      return '#64748b'; // Gray
  }
};

export function SessionToken({ session, isActive, onClick }: SessionTokenProps) {
  const showUnreadDot = session.hasUnread && session.status === 'completed';
  const showErrorBadge = session.status === 'error';
  const statusClass = getStatusClass(session.status);

  const tooltipContent = (
    <div style={{ maxWidth: 200 }}>
      <div style={{ fontWeight: 600, marginBottom: 4 }}>
        {session.agent || 'New Chat'}
      </div>
      <div style={{ fontSize: 12, opacity: 0.8 }}>
        {session.title}
      </div>
      {session.status === 'working' && session.estimatedTime && (
        <div style={{ fontSize: 11, opacity: 0.6, marginTop: 4 }}>
          Est: {session.estimatedTime}
        </div>
      )}
    </div>
  );

  return (
    <Tooltip title={tooltipContent} placement="right" mouseEnterDelay={0.5}>
      <div className={`session-token ${statusClass}`} onClick={onClick}>
        <Avatar
          size={28}
          icon={<RobotOutlined style={{ fontSize: 14 }} />}
          style={{
            ...getAvatarStyle(session.status, isActive),
            backgroundColor: getAvatarColor(session.status),
          }}
        />
        {showUnreadDot && <span className="unread-dot" />}
        {showErrorBadge && (
          <span className="error-badge">
            <ExclamationOutlined style={{ fontSize: 10 }} />
          </span>
        )}
      </div>
    </Tooltip>
  );
}
