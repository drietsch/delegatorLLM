import { useState, useRef, useMemo, useEffect, useCallback } from 'react';
import { Card, Typography, Progress, Alert, Tag, Spin, Button, Dropdown, Flex, Tooltip, GetRef } from 'antd';
import { Bubble, Sender, Prompts } from '@ant-design/x';
import {
  RobotOutlined,
  UserOutlined,
  SyncOutlined,
  SearchOutlined,
  EditOutlined,
  CodeOutlined,
  GlobalOutlined,
} from '@ant-design/icons';
import { useMultiSession } from '../../context/MultiSessionContext';
import { NotificationStack } from './NotificationPill';
import { AttachmentChips } from './AttachmentChips';
import { debounce } from '../../utils/debounce';
import { useRouter } from '../../hooks/useRouter';
import type { MenuProps } from 'antd';
import type { Agent } from '../../types/session';
import type { Asset, Attachment } from '../../types/asset';
import { assetToAttachment } from '../../types/asset';
import { API_BASE } from '../../config/api';
import './ChatStage.css';

const { Text, Title } = Typography;

// Example prompts
const examplePrompts = [
  { key: 'search', icon: <SearchOutlined />, label: 'Search for blue cars' },
  { key: 'translate', icon: <GlobalOutlined />, label: 'Translate hello to German' },
  { key: 'generate', icon: <EditOutlined />, label: 'Write a product description' },
  { key: 'code', icon: <CodeOutlined />, label: 'Help me write a function' },
];

export function ChatStage() {
  const {
    activeSession,
    notifications,
    isRouterReady,
    routerLoadingState,
    sendMessage,
    switchSession,
    dismissNotification,
    createSession,
    minimizeToRail,
  } = useMultiSession();

  const { route } = useRouter();
  const senderRef = useRef<GetRef<typeof Sender>>(null);
  const [inputValue, setInputValue] = useState('');
  const [autoMode, setAutoMode] = useState(true);
  const [manualAgent, setManualAgent] = useState<string | null>(null);
  const [allAgents, setAllAgents] = useState<Agent[]>([]);
  const [routingResult, setRoutingResult] = useState<{ agent: string; confidence: number } | null>(null);
  const [isRouting, setIsRouting] = useState(false);
  const [attachments, setAttachments] = useState<Attachment[]>([]);
  const [isDragOver, setIsDragOver] = useState(false);

  // Drag and drop handlers
  const handleDragOver = useCallback((e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.dataTransfer.types.includes('application/json')) {
      setIsDragOver(true);
      e.dataTransfer.dropEffect = 'copy';
    }
  }, []);

  const handleDragLeave = useCallback((e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragOver(false);
  }, []);

  const handleDrop = useCallback((e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsDragOver(false);

    try {
      const data = e.dataTransfer.getData('application/json');
      if (data) {
        const assets: Asset[] = JSON.parse(data);
        const newAttachments = assets.map(assetToAttachment);
        setAttachments(prev => {
          // Avoid duplicates by assetId
          const existingIds = new Set(prev.map(a => a.assetId));
          const unique = newAttachments.filter(a => !existingIds.has(a.assetId));
          return [...prev, ...unique];
        });
      }
    } catch (err) {
      console.error('Failed to parse dropped data:', err);
    }
  }, []);

  const removeAttachment = useCallback((id: string) => {
    setAttachments(prev => prev.filter(a => a.id !== id));
  }, []);

  // Fetch all agents on mount
  useEffect(() => {
    fetch(`${API_BASE}/api/agents`)
      .then((res) => res.json())
      .then((data) => {
        const agents = data.agents || data;
        setAllAgents(agents);
      })
      .catch((err) => console.error('Failed to fetch agents:', err));
  }, []);

  // Debounced routing
  const debouncedRoute = useMemo(
    () =>
      debounce(async (query: string) => {
        if (!query.trim() || !isRouterReady) return;
        setIsRouting(true);
        try {
          const result = await route(query);
          if (result) {
            setRoutingResult({ agent: result.agent, confidence: result.confidence });
          }
        } finally {
          setIsRouting(false);
        }
      }, 300),
    [route, isRouterReady]
  );

  const handleInputChange = (value: string) => {
    setInputValue(value);
    if (autoMode && value.trim()) {
      debouncedRoute(value);
    }
  };

  const handleSubmit = async (value: string) => {
    if (!value.trim() && attachments.length === 0) return;

    // Create session if none exists
    if (!activeSession) {
      createSession();
    }

    // Include attachment info in message
    const attachmentInfo = attachments.length > 0
      ? `\n\n[Attachments: ${attachments.map(a => a.name).join(', ')}]`
      : '';

    await sendMessage(value.trim() + attachmentInfo);
    setInputValue('');
    setAttachments([]);
    senderRef.current?.clear?.();
    setRoutingResult(null);
  };

  const handlePromptClick = (info: { data: { label?: React.ReactNode } }) => {
    if (info.data.label && typeof info.data.label === 'string') {
      setInputValue(info.data.label);
      if (autoMode) {
        debouncedRoute(info.data.label);
      }
    }
  };

  const handleAgentMenuClick: MenuProps['onClick'] = (item) => {
    if (item.key === '__auto__') {
      setAutoMode(true);
      setManualAgent(null);
      if (inputValue.trim()) {
        debouncedRoute(inputValue);
      }
    } else {
      setAutoMode(false);
      setManualAgent(item.key);
    }
  };

  const effectiveAgent = autoMode ? routingResult?.agent : manualAgent;
  const confidence = routingResult?.confidence ?? 0;

  const getConfidenceColor = (conf: number): string => {
    if (conf > 0.8) return '#10b981';
    if (conf > 0.5) return '#f59e0b';
    return '#ef4444';
  };

  const agentMenuItems: MenuProps['items'] = [
    {
      key: '__auto__',
      icon: <SyncOutlined spin={isRouting} style={{ color: '#6366f1' }} />,
      label: <span style={{ fontWeight: 500 }}>Auto-detect</span>,
    },
    { type: 'divider' },
    ...allAgents.map((agent) => ({
      key: agent.name,
      icon: <RobotOutlined style={{ color: '#64748b' }} />,
      label: (
        <Tooltip title={agent.description} placement="right" mouseEnterDelay={0.3}>
          <span>{agent.name}</span>
        </Tooltip>
      ),
    })),
  ];

  const getAgentDisplayName = () => {
    if (autoMode) {
      return effectiveAgent || 'Auto-detect';
    }
    return manualAgent || 'Select Agent';
  };

  const isSessionWorking =
    activeSession?.status === 'routing' ||
    activeSession?.status === 'working' ||
    activeSession?.status === 'streaming';

  const showWelcome =
    routerLoadingState.status === 'ready' &&
    (!activeSession || activeSession.messages.length === 0) &&
    !isSessionWorking;

  // Sender footer - with Auto/Manual switch
  const senderFooter = () => (
    <Flex justify="space-between" align="center" style={{ padding: '4px 0', gap: 4 }}>
      <Flex align="center" gap={6}>
        <Button
          size="small"
          type={autoMode ? 'primary' : 'default'}
          onClick={() => {
            const newAutoMode = !autoMode;
            setAutoMode(newAutoMode);
            if (newAutoMode && inputValue.trim()) {
              debouncedRoute(inputValue);
            }
            if (!newAutoMode) {
              setRoutingResult(null);
            }
          }}
          style={{
            fontSize: 11,
            padding: '2px 8px',
            height: 'auto',
          }}
        >
          Auto
        </Button>
        <Dropdown
          menu={{
            selectedKeys: [autoMode ? '__auto__' : manualAgent || ''],
            onClick: handleAgentMenuClick,
            items: agentMenuItems,
          }}
          trigger={['click']}
          disabled={autoMode}
        >
          <Button
            size="small"
            type="text"
            style={{
              display: 'flex',
              alignItems: 'center',
              gap: 4,
              padding: '2px 8px',
              borderRadius: 8,
              background: effectiveAgent ? 'rgba(99, 102, 241, 0.08)' : 'transparent',
            }}
          >
            {isRouting ? (
              <SyncOutlined spin style={{ fontSize: 12, color: '#6366f1' }} />
            ) : (
              <RobotOutlined style={{ fontSize: 12, color: effectiveAgent ? '#6366f1' : '#94a3b8' }} />
            )}
            <span style={{
              fontSize: 11,
              fontWeight: 500,
              color: effectiveAgent ? '#6366f1' : '#64748b',
              maxWidth: 80,
              overflow: 'hidden',
              textOverflow: 'ellipsis',
              whiteSpace: 'nowrap',
            }}>
              {getAgentDisplayName()}
            </span>
            {autoMode && effectiveAgent && confidence > 0 && (
              <span
                style={{
                  background: getConfidenceColor(confidence),
                  color: 'white',
                  padding: '1px 5px',
                  borderRadius: 6,
                  fontSize: 9,
                  fontWeight: 600,
                }}
              >
                {(confidence * 100).toFixed(0)}%
              </span>
            )}
          </Button>
        </Dropdown>
      </Flex>
      {isSessionWorking && (
        <Button
          type="text"
          size="small"
          onClick={minimizeToRail}
          style={{
            color: '#6366f1',
            fontSize: 10,
            padding: '2px 6px',
            height: 'auto',
          }}
        >
          BG
        </Button>
      )}
    </Flex>
  );

  // Convert messages to Bubble.List format
  const bubbleItems = activeSession?.messages.map((msg) => ({
    key: msg.id,
    role: msg.role as 'user' | 'assistant',
    content: msg.content,
  })) || [];

  return (
    <div className="chat-stage">
      {/* Loading state */}
      {routerLoadingState.status !== 'ready' && routerLoadingState.status !== 'error' && (
        <Card className="loading-card">
          <Flex align="center" gap={16} style={{ marginBottom: 20 }}>
            <div className="loading-icon">
              <Spin indicator={<SyncOutlined spin style={{ fontSize: 24, color: 'white' }} />} />
            </div>
            <div>
              <Text strong style={{ fontSize: 16, display: 'block' }}>
                Initializing AI Router
              </Text>
              <Text type="secondary">{routerLoadingState.message || 'Loading...'}</Text>
            </div>
          </Flex>
          <Flex justify="space-between" style={{ marginBottom: 8 }}>
            <Text type="secondary" style={{ fontSize: 13 }}>
              Multilingual MiniLM Embeddings
            </Text>
            <Text strong style={{ color: '#6366f1' }}>
              {Math.round(routerLoadingState.progress)}%
            </Text>
          </Flex>
          <Progress
            percent={Math.round(routerLoadingState.progress)}
            showInfo={false}
            strokeColor={{ '0%': '#6366f1', '100%': '#a855f7' }}
            trailColor="#e2e8f0"
          />
        </Card>
      )}

      {routerLoadingState.status === 'error' && (
        <Alert
          message="Router Error"
          description={routerLoadingState.message}
          type="error"
          showIcon
          style={{ borderRadius: 12, marginBottom: 16 }}
        />
      )}

      {activeSession?.status === 'error' && (
        <Alert
          message="Error"
          description={activeSession.error}
          type="error"
          showIcon
          style={{ borderRadius: 12, marginBottom: 16 }}
        />
      )}

      {/* Welcome screen */}
      {showWelcome && (
        <div className="welcome-section">
          <div className="welcome-header">
            <div className="welcome-icon">
              <RobotOutlined style={{ fontSize: 40, color: 'white' }} />
            </div>
            <Title level={4} style={{ margin: '16px 0 8px', color: '#1e293b' }}>
              Agent Delegator
            </Title>
            <Text type="secondary">Ask anything - I'll route to the best agent</Text>
          </div>
          <div className="welcome-prompts">
            <Prompts
              items={examplePrompts}
              onItemClick={handlePromptClick}
              wrap
              styles={{
                item: {
                  background: '#f8fafc',
                  borderRadius: 10,
                  border: '1px solid #e2e8f0',
                },
              }}
            />
          </div>
        </div>
      )}

      {/* Chat messages */}
      {activeSession && activeSession.messages.length > 0 && (
        <div className="messages-container">
          <div className="messages-header">
            <Flex align="center" gap={8}>
              <RobotOutlined style={{ color: '#6366f1' }} />
              <span style={{ fontWeight: 600 }}>Chat</span>
              {activeSession.agent && (
                <Tag color="purple" style={{ borderRadius: 10 }}>
                  {activeSession.agent}
                </Tag>
              )}
            </Flex>
          </div>
          <Bubble.List
            className="bubble-list"
            role={{
              user: {
                placement: 'end',
                avatar: (
                  <div className="avatar-user">
                    <UserOutlined style={{ color: '#fff', fontSize: 14 }} />
                  </div>
                ),
                variant: 'filled',
                styles: {
                  content: {
                    background: 'linear-gradient(135deg, #6366f1, #8b5cf6)',
                    color: 'white',
                    borderRadius: '16px 16px 4px 16px',
                    padding: '10px 14px',
                  },
                },
              },
              assistant: {
                placement: 'start',
                avatar: (
                  <div className="avatar-assistant">
                    <RobotOutlined style={{ color: '#fff', fontSize: 14 }} />
                  </div>
                ),
                variant: 'outlined',
                styles: {
                  content: {
                    background: 'white',
                    borderRadius: '16px 16px 16px 4px',
                    padding: '10px 14px',
                    border: '1px solid #e2e8f0',
                  },
                },
              },
            }}
            items={bubbleItems.map((item, index) => {
              const isLastAssistant = item.role === 'assistant' &&
                index === bubbleItems.length - 1;
              const shouldAnimate = isLastAssistant &&
                activeSession?.status === 'streaming';

              return {
                key: item.key,
                role: item.role,
                content: item.content,
                loading: isSessionWorking && item.role === 'assistant' && !item.content,
                typing: shouldAnimate ? { effect: 'typing', step: 5, interval: 20 } : undefined,
              };
            })}
          />
        </div>
      )}

      {/* Input area with drop zone */}
      <div
        className={`input-container ${isDragOver ? 'drag-over' : ''}`}
        onDragOver={handleDragOver}
        onDragLeave={handleDragLeave}
        onDrop={handleDrop}
      >
        <AttachmentChips attachments={attachments} onRemove={removeAttachment} />
        <Sender
          ref={senderRef}
          placeholder={isRouterReady ? 'Type your query or drop assets...' : 'Loading model...'}
          value={inputValue}
          onChange={handleInputChange}
          onSubmit={handleSubmit}
          loading={isSessionWorking}
          disabled={!isRouterReady}
          footer={senderFooter}
          autoSize={{ minRows: 1, maxRows: 4 }}
          styles={{
            input: {
              borderRadius: attachments.length > 0 ? '0 0 10px 10px' : 10,
              fontSize: 14,
            },
          }}
        />
        {isDragOver && (
          <div className="drop-overlay">
            <span>Drop assets here</span>
          </div>
        )}
      </div>

      {/* Notification pills */}
      <NotificationStack
        notifications={notifications}
        onView={switchSession}
        onDismiss={dismissNotification}
      />
    </div>
  );
}
