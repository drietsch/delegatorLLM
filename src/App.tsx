import { ConfigProvider } from 'antd';
import { XProvider } from '@ant-design/x';
import { MultiSessionProvider } from './context/MultiSessionContext';
import { PowerStripLayout } from './components/Layout';

// Custom theme
const customTheme = {
  token: {
    colorPrimary: '#6366f1',
    colorBgContainer: '#ffffff',
    borderRadius: 12,
    colorBgLayout: '#f8fafc',
  },
};

function App() {
  return (
    <ConfigProvider theme={customTheme}>
      <XProvider>
        <MultiSessionProvider>
          <PowerStripLayout />
        </MultiSessionProvider>
      </XProvider>
    </ConfigProvider>
  );
}

export default App;
