import { useEffect } from 'react';
import { ModelStatus } from './components/ModelStatus';
import { ChatHistory } from './components/ChatHistory';
import { ChatInput } from './components/ChatInput';
import { useModelLoader } from './hooks/useModelLoader';
import { useDelegator } from './hooks/useDelegator';

function App() {
  const { status, progress, error, backend, loadModel } = useModelLoader();
  const { messages, isProcessing, sendMessage, clearMessages } = useDelegator();

  // Auto-load model on mount (optional - comment out for manual loading)
  useEffect(() => {
    if (status === 'idle') {
      loadModel();
    }
  }, [status, loadModel]);

  const isModelReady = status === 'ready';

  return (
    <div className="app">
      <header className="app-header">
        <h1>Browser Delegator LLM</h1>
        <p className="subtitle">
          Local AI routing powered by multilingual E5 embeddings (50+ languages)
        </p>
        <ModelStatus
          status={status}
          progress={progress}
          backend={backend}
          error={error}
          onLoad={loadModel}
        />
      </header>

      <main className="app-main">
        <ChatHistory messages={messages} isProcessing={isProcessing} />
      </main>

      <footer className="app-footer">
        {messages.length > 0 && (
          <button className="clear-button" onClick={clearMessages}>
            Clear Chat
          </button>
        )}
        <ChatInput
          onSubmit={sendMessage}
          disabled={!isModelReady || isProcessing}
          showExamples={isModelReady && messages.length === 0}
          placeholder={
            isModelReady
              ? 'Type your request or click an example above...'
              : 'Loading model...'
          }
        />
      </footer>
    </div>
  );
}

export default App;
