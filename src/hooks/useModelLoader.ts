import { useState, useCallback, useRef } from 'react';
import type { LoadingProgress, InferenceStatus, RuntimeBackend } from '../types';
import { delegatorService } from '../services/delegator';

interface UseModelLoaderResult {
  status: InferenceStatus;
  progress: LoadingProgress | null;
  error: Error | null;
  backend: RuntimeBackend | null;
  loadModel: () => Promise<void>;
}

/**
 * Hook for managing model loading state
 */
export function useModelLoader(): UseModelLoaderResult {
  const [status, setStatus] = useState<InferenceStatus>('idle');
  const [progress, setProgress] = useState<LoadingProgress | null>(null);
  const [error, setError] = useState<Error | null>(null);
  const [backend, setBackend] = useState<RuntimeBackend | null>(null);
  const loadingRef = useRef(false);

  const loadModel = useCallback(async () => {
    // Prevent double loading
    if (loadingRef.current || delegatorService.isReady) {
      return;
    }

    loadingRef.current = true;
    setStatus('loading');
    setError(null);

    try {
      await delegatorService.initialize((prog) => {
        setProgress(prog);
      });

      setBackend(delegatorService.currentBackend);
      setStatus('ready');
    } catch (err) {
      setError(err instanceof Error ? err : new Error('Unknown error'));
      setStatus('error');
    } finally {
      loadingRef.current = false;
    }
  }, []);

  return {
    status,
    progress,
    error,
    backend,
    loadModel,
  };
}
