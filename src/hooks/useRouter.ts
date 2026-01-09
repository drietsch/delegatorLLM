import { useState, useEffect, useRef, useCallback } from 'react';

interface LoadingState {
  status: 'idle' | 'loading' | 'ready' | 'error';
  message: string;
  progress: number;
}

interface TopMatch {
  name: string;
  score: number;
}

interface RoutingResult {
  query: string;
  agent: string;
  description: string;
  confidence: number;
  topMatches: TopMatch[];
}

export function useRouter() {
  const workerRef = useRef<Worker | null>(null);
  const [loadingState, setLoadingState] = useState<LoadingState>({
    status: 'idle',
    message: '',
    progress: 0,
  });
  const [isRouting, setIsRouting] = useState(false);
  const [lastResult, setLastResult] = useState<RoutingResult | null>(null);

  useEffect(() => {
    workerRef.current = new Worker(
      new URL('../worker/router.worker.ts', import.meta.url),
      { type: 'module' }
    );

    workerRef.current.onmessage = (e) => {
      const { type, status, message, progress, data, error } = e.data;

      switch (type) {
        case 'status':
          setLoadingState(prev => ({
            ...prev,
            status,
            message: message || '',
          }));
          break;

        case 'progress':
          setLoadingState(prev => ({
            ...prev,
            progress,
            message: message || prev.message,
          }));
          break;

        case 'processing':
          setIsRouting(true);
          break;

        case 'result':
          setIsRouting(false);
          setLastResult(data as RoutingResult);
          break;

        case 'error':
          setLoadingState(prev => ({
            ...prev,
            status: 'error',
            message: error,
          }));
          setIsRouting(false);
          break;
      }
    };

    workerRef.current.postMessage({ cmd: 'init' });

    return () => {
      workerRef.current?.terminate();
    };
  }, []);

  const route = useCallback((query: string): Promise<RoutingResult> => {
    return new Promise((resolve, reject) => {
      if (!workerRef.current || loadingState.status !== 'ready') {
        reject(new Error('Router not ready'));
        return;
      }

      const handler = (e: MessageEvent) => {
        if (e.data.type === 'result') {
          workerRef.current?.removeEventListener('message', handler);
          resolve(e.data.data as RoutingResult);
        } else if (e.data.type === 'error') {
          workerRef.current?.removeEventListener('message', handler);
          reject(new Error(e.data.error));
        }
      };

      workerRef.current.addEventListener('message', handler);
      workerRef.current.postMessage({ cmd: 'query', text: query });
    });
  }, [loadingState.status]);

  return {
    loadingState,
    isRouting,
    lastResult,
    route,
    isReady: loadingState.status === 'ready',
  };
}
