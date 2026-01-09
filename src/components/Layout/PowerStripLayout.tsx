import { PowerStrip } from '../PowerStrip';
import { ChatStage } from '../ChatStage';
import { AssetTree } from '../AssetTree';
import './PowerStripLayout.css';

export function PowerStripLayout() {
  return (
    <div className="app-layout">
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
