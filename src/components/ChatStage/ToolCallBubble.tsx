import React, { useState } from 'react';
import { Card, Tag, Spin, Typography } from 'antd';
import {
  ToolOutlined,
  CheckCircleOutlined,
  LoadingOutlined,
  ExclamationCircleOutlined,
  DownOutlined,
  RightOutlined,
} from '@ant-design/icons';
import type { ToolCall, ToolResult } from '../../types/session';
import './ToolCallBubble.css';

const { Text } = Typography;

interface ToolCallBubbleProps {
  toolCall: ToolCall;
  result?: ToolResult;
}

export const ToolCallBubble: React.FC<ToolCallBubbleProps> = ({ toolCall, result }) => {
  const [expanded, setExpanded] = useState(false);

  const getStatusIcon = () => {
    switch (toolCall.status) {
      case 'executing':
        return <Spin indicator={<LoadingOutlined style={{ fontSize: 14 }} spin />} />;
      case 'completed':
        return <CheckCircleOutlined style={{ color: '#10b981', fontSize: 14 }} />;
      case 'error':
        return <ExclamationCircleOutlined style={{ color: '#ef4444', fontSize: 14 }} />;
      default:
        return <ToolOutlined style={{ color: '#6366f1', fontSize: 14 }} />;
    }
  };

  const getStatusColor = () => {
    switch (toolCall.status) {
      case 'executing':
        return 'processing';
      case 'completed':
        return 'success';
      case 'error':
        return 'error';
      default:
        return 'default';
    }
  };

  const formatResult = (res: unknown): string => {
    if (typeof res === 'string') {
      try {
        const parsed = JSON.parse(res);
        return JSON.stringify(parsed, null, 2);
      } catch {
        return res;
      }
    }
    return JSON.stringify(res, null, 2);
  };

  return (
    <Card
      size="small"
      className="tool-call-bubble"
      bodyStyle={{ padding: '8px 12px' }}
    >
      <div
        className="tool-call-header"
        onClick={() => setExpanded(!expanded)}
        style={{ cursor: 'pointer' }}
      >
        <div className="tool-call-header-left">
          {getStatusIcon()}
          <Tag color="blue" style={{ margin: 0, borderRadius: 6 }}>
            {toolCall.name}
          </Tag>
          <Tag color={getStatusColor()} style={{ margin: 0, borderRadius: 6, fontSize: 10 }}>
            {toolCall.status}
          </Tag>
        </div>
        <div className="tool-call-header-right">
          {expanded ? (
            <DownOutlined style={{ fontSize: 10, color: '#94a3b8' }} />
          ) : (
            <RightOutlined style={{ fontSize: 10, color: '#94a3b8' }} />
          )}
        </div>
      </div>

      {expanded && (
        <div className="tool-call-details">
          <div className="tool-call-section">
            <Text type="secondary" style={{ fontSize: 11, fontWeight: 600 }}>
              ARGUMENTS
            </Text>
            <pre className="tool-call-code">
              {JSON.stringify(toolCall.args, null, 2)}
            </pre>
          </div>

          {result && (
            <div className="tool-call-section">
              <Text type="secondary" style={{ fontSize: 11, fontWeight: 600 }}>
                {result.error ? 'ERROR' : 'RESULT'}
              </Text>
              <pre className={`tool-call-code ${result.error ? 'error' : ''}`}>
                {result.error || formatResult(result.result)}
              </pre>
            </div>
          )}
        </div>
      )}
    </Card>
  );
};

export default ToolCallBubble;
