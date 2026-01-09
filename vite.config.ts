import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// Use /delegatorLLM/ for GitHub Pages, / for Fly.io
const base = process.env.GITHUB_PAGES ? '/delegatorLLM/' : '/'

export default defineConfig({
  plugins: [react()],
  base,
  server: {
    port: 5174,
  },
  worker: {
    format: 'es'
  }
})
