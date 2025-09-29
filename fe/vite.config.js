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
      usePolling: false, // Disabilita polling per evitare loop
      ignored: [
        '**/node_modules/**',
        '**/dist/**',
        '**/.git/**',
        '**/.vite/**',
        '**/coverage/**',
        '**/.nyc_output/**',
        '**/.cache/**',
        '**/.temp/**',
        '**/.tmp/**',
        '**/.vscode/**',
        '**/.idea/**',
        '**/*.log',
        '**/.DS_Store',
        // Ignora file di configurazione che potrebbero causare loop
        '**/vite.config.*',
        '**/package.json',
        '**/.prettierrc*',
        '**/.eslintrc*',
        '**/.prettierignore',
        '**/.eslintignore',
        '**/.eslintcache',
        '**/package-lock.json',
        '**/yarn.lock',
        '**/pnpm-lock.yaml',
      ],
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
