import type { InferenceStatus, LoadingProgress, RuntimeBackend } from '../types';
import { getBackendDisplayName } from '../services/backend-detector';

interface ModelStatusProps {
  status: InferenceStatus;
  progress: LoadingProgress | null;
  backend: RuntimeBackend | null;
  error: Error | null;
  onLoad: () => void;
}

export function ModelStatus({
  status,
  progress,
  backend,
  error,
  onLoad,
}: ModelStatusProps) {
  if (status === 'idle') {
    return (
      <div className="model-status idle">
        <p>Multilingual embedding model not loaded</p>
        <button onClick={onLoad} className="load-button">
          Load E5 Multilingual Model
        </button>
        <p className="hint">
          First load downloads ~118MB from HuggingFace (cached for future visits)
        </p>
      </div>
    );
  }

  if (status === 'loading') {
    const progressPercent = progress?.progress ?? 0;
    return (
      <div className="model-status loading">
        <div className="loading-spinner" />
        <p>{progress?.status || 'Initializing...'}</p>
        {progress?.file && (
          <div className="progress-container">
            <div
              className="progress-bar"
              style={{ width: `${progressPercent}%` }}
            />
            <span className="progress-text">
              {progressPercent.toFixed(0)}%
            </span>
          </div>
        )}
      </div>
    );
  }

  if (status === 'error') {
    return (
      <div className="model-status error">
        <p>Failed to load model</p>
        <p className="error-message">{error?.message}</p>
        <button onClick={onLoad} className="retry-button">
          Retry
        </button>
      </div>
    );
  }

  // Ready state
  return (
    <div className="model-status ready">
      <span className="status-badge ready">Ready</span>
      <span className="backend-info">
        {backend && getBackendDisplayName(backend)}
      </span>
    </div>
  );
}
