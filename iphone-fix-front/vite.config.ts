import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import * as path from 'path'
import { fileURLToPath } from 'url'

// ✅ Corrección para ESM en TypeScript
const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  // ✅ Para TypeScript con ESM, no incluimos postcss aquí
  // La configuración de Tailwind se manejará automáticamente por postcss.config.js
})