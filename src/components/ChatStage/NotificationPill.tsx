import { useState, useEffect, useCallback } from 'react';
import { Button } from 'antd';
import { CheckOutlined, CloseOutlined, ExclamationOutlined } from '@ant-design/icons';
import type { Notification } from '../../types/session';
import './ChatStage.css';

interface NotificationPillProps {
  notification: Notification;
  onView: () => void;
  onDismiss: () => void;
}

export function NotificationPill({ notification, onView, onDismiss }: NotificationPillProps) {
  const [dismissing, setDismissing] = useState(false);

  const handleDismiss = useCallback(() => {
    setDismissing(true);
    setTimeout(onDismiss, 300); // Wait for animation
  }, [onDismiss]);

  const handleView = useCallback(() => {
    onView();
    handleDismiss();
  }, [onView, handleDismiss]);

  // Auto-dismiss after 4 seconds
  useEffect(() => {
    const timer = setTimeout(() => {
      handleDismiss();
    }, 4000);

    return () => clearTimeout(timer);
  }, [handleDismiss]);

  const isSuccess = notification.type === 'success';

  return (
    <div className={`notification-pill ${dismissing ? 'dismissing' : ''}`}>
      <span className={`pill-icon ${isSuccess ? 'success' : 'error'}`}>
        {isSuccess ? <CheckOutlined /> : <ExclamationOutlined />}
      </span>
      <span className="pill-message">{notification.message}</span>
      <Button
        type="link"
        size="small"
        onClick={handleView}
        style={{ padding: '0 8px', height: 'auto', color: '#6366f1' }}
      >
        View
      </Button>
      <Button
        type="text"
        size="small"
        icon={<CloseOutlined />}
        onClick={handleDismiss}
        style={{ padding: '0 4px', height: 'auto', color: '#94a3b8' }}
      />
    </div>
  );
}

interface NotificationStackProps {
  notifications: Notification[];
  onView: (sessionId: string) => void;
  onDismiss: (id: string) => void;
}

export function NotificationStack({ notifications, onView, onDismiss }: NotificationStackProps) {
  if (notifications.length === 0) return null;

  // Only show the most recent notification
  const latestNotification = notifications[notifications.length - 1];

  return (
    <div className="notification-stack">
      <NotificationPill
        key={latestNotification.id}
        notification={latestNotification}
        onView={() => onView(latestNotification.sessionId)}
        onDismiss={() => onDismiss(latestNotification.id)}
      />
    </div>
  );
}
