import type { RuntimeBackend } from '../types';

/**
 * Detect the best available runtime backend for ONNX inference
 * Prefers WebGPU if available, falls back to CPU (WASM)
 */
export async function detectBackend(): Promise<RuntimeBackend> {
  // Check for WebGPU support
  if ('gpu' in navigator) {
    try {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const gpu = (navigator as any).gpu;
      const adapter = await gpu.requestAdapter();
      if (adapter) {
        console.log('[Backend] WebGPU available');
        return 'webgpu';
      }
    } catch (error) {
      console.warn('[Backend] WebGPU check failed:', error);
    }
  }

  console.log('[Backend] Falling back to CPU (WASM)');
  return 'cpu';
}

/**
 * Get human-readable backend name
 */
export function getBackendDisplayName(backend: RuntimeBackend): string {
  switch (backend) {
    case 'webgpu':
      return 'WebGPU (GPU accelerated)';
    case 'cpu':
      return 'WebAssembly (CPU)';
    default:
      return 'Unknown';
  }
}
