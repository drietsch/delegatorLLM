import { useState, useCallback } from 'react';
import { PowerStrip } from '../PowerStrip';
import { ChatStage } from '../ChatStage';
import { AssetTree } from '../AssetTree';
import { NavHeader, type PageKey } from './index';
import { ApiDocs, Examples } from '../../pages';
import { useMultiSession } from '../../context/MultiSessionContext';
import './PowerStripLayout.css';

export function PowerStripLayout() {
  const [activePage, setActivePage] = useState<PageKey>('chat');
  const { sendMessage } = useMultiSession();

  const handlePageChange = useCallback((page: PageKey) => {
    setActivePage(page);
  }, []);

  const handleTryQuery = useCallback((query: string) => {
    // Switch to chat page and send the query
    setActivePage('chat');
    // Small delay to ensure page switch renders first
    setTimeout(() => {
      sendMessage(query);
    }, 100);
  }, [sendMessage]);

  const renderPage = () => {
    switch (activePage) {
      case 'api-docs':
        return <ApiDocs />;
      case 'examples':
        return <Examples onTryQuery={handleTryQuery} />;
      case 'chat':
      default:
        return (
          <div className="chat-layout">
            <div className="asset-panel">
              <AssetTree />
            </div>
            <div className="chat-widget">
              <PowerStrip />
              <ChatStage />
            </div>
          </div>
        );
    }
  };

  return (
    <div className="app-layout">
      <NavHeader activePage={activePage} onPageChange={handlePageChange} />
      <div className="app-content">
        {renderPage()}
      </div>
    </div>
  );
}
