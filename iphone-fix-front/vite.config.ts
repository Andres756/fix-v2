import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath } from 'url'
import * as path from 'path'
import viteCompression from 'vite-plugin-compression'

// ✅ Corrección para ESM
const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default defineConfig({
  plugins: [
    vue(),

    // ✅ Gzip compression para reducir el peso de los archivos servidos
    viteCompression({
      verbose: false,
      disable: false,
      threshold: 10240, // solo comprime archivos > 10 KB
      algorithm: 'gzip',
      ext: '.gz',
    }),
  ],

  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      '~': path.resolve(__dirname, './'),
    },
  },

  build: {
    minify: 'terser',
    rollupOptions: {

    // @ts-expect-error: "terserOptions" no está tipado en Vite pero es válido en Rollup
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
      },
      format: {
        comments: false,
      },
    },
  },
  },

  server: {
    port: 5173,
    open: true,
    strictPort: true,
    hmr: true,
  },

  // ✅ Configura correctamente rutas relativas si lo subes en subcarpeta de Hostinger
  base: './',
})
