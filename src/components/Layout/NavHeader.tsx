import { Menu } from 'antd';
import {
  MessageOutlined,
  ApiOutlined,
  BulbOutlined,
  RobotOutlined,
} from '@ant-design/icons';
import type { MenuProps } from 'antd';
import './NavHeader.css';

export type PageKey = 'chat' | 'api-docs' | 'examples';

interface NavHeaderProps {
  activePage: PageKey;
  onPageChange: (page: PageKey) => void;
}

const menuItems: MenuProps['items'] = [
  {
    key: 'chat',
    icon: <MessageOutlined />,
    label: 'Chat',
  },
  {
    key: 'api-docs',
    icon: <ApiOutlined />,
    label: 'API Docs',
  },
  {
    key: 'examples',
    icon: <BulbOutlined />,
    label: 'Examples',
  },
];

export function NavHeader({ activePage, onPageChange }: NavHeaderProps) {
  const handleMenuClick: MenuProps['onClick'] = (info) => {
    onPageChange(info.key as PageKey);
  };

  return (
    <header className="nav-header">
      <div className="nav-brand">
        <div className="nav-logo">
          <RobotOutlined />
        </div>
        <span className="nav-title">Agent Delegator</span>
      </div>
      <Menu
        mode="horizontal"
        selectedKeys={[activePage]}
        onClick={handleMenuClick}
        items={menuItems}
        className="nav-menu"
      />
      <div className="nav-spacer" />
    </header>
  );
}
