import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    port: 3000,
    host: '0.0.0.0',
    allowedHosts: ['iliadlocal', 'iliadLocal', 'localhost'],
    watch: {
      usePolling: true,
      interval: 1000,
    },
    hmr: {
      port: 80,
      host: 'iliadLocal',
    },
    proxy: {
      '/api': {
        target: 'http://iliadApi:8000',
        changeOrigin: true,
      },
    },
  },
});
