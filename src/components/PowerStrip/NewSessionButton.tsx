import { Tooltip, Button } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import './PowerStrip.css';

interface NewSessionButtonProps {
  onClick: () => void;
  disabled?: boolean;
}

export function NewSessionButton({ onClick, disabled }: NewSessionButtonProps) {
  return (
    <Tooltip title="New Chat" placement="right">
      <Button
        type="text"
        icon={<PlusOutlined />}
        onClick={onClick}
        disabled={disabled}
        className="new-session-button"
        style={{
          width: 36,
          height: 36,
          borderRadius: '50%',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          backgroundColor: 'rgba(255, 255, 255, 0.1)',
          color: 'rgba(255, 255, 255, 0.8)',
          border: '2px dashed rgba(255, 255, 255, 0.3)',
          transition: 'all 0.2s ease',
        }}
      />
    </Tooltip>
  );
}
