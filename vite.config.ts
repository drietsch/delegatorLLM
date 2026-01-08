import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import path from 'path';

export default defineConfig({
  // Base path for GitHub Pages deployment
  base: '/delegatorLLM/',

  plugins: [
    react(),
    viteStaticCopy({
      targets: [
        {
          src: 'node_modules/onnxruntime-web/dist/*.wasm',
          dest: 'wasm',
        },
        {
          src: 'node_modules/@litertjs/core/wasm/*',
          dest: 'litert-wasm',
        },
      ],
    }),
  ],

  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },

  // Required headers for SharedArrayBuffer (multi-threaded WASM)
  server: {
    headers: {
      'Cross-Origin-Opener-Policy': 'same-origin',
      'Cross-Origin-Embedder-Policy': 'require-corp',
    },
  },

  // Also for preview mode
  preview: {
    headers: {
      'Cross-Origin-Opener-Policy': 'same-origin',
      'Cross-Origin-Embedder-Policy': 'require-corp',
    },
  },

  // Optimize dependencies
  optimizeDeps: {
    exclude: ['onnxruntime-web'],
  },

  // Build configuration
  build: {
    target: 'esnext',
  },
});
