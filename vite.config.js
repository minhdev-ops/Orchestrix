/// <reference types="vitest/config" />
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import { fileURLToPath } from 'node:url';
import { storybookTest } from '@storybook/addon-vitest/vitest-plugin';
import { playwright } from '@vitest/browser-playwright';
const dirname = typeof __dirname !== 'undefined' ? __dirname : path.dirname(fileURLToPath(import.meta.url));

// More info at: https://storybook.js.org/docs/next/writing-tests/integrations/vitest-addon
export default defineConfig({
  plugins: [laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true
  }), tailwindcss(), vue({
    template: {
      transformAssetUrls: {
        base: null,
        includeAbsolute: false
      }
    }
  })],
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**']
    }
  },
  resolve: {
    alias: {
      '@': '/resources/js',
      '@agriverse': '/app/Modules/AgriVerse/Resources/js'
    }
  },
  optimizeDeps: {
    include: ['vue', '@inertiajs/vue3', 'primevue/config', 'primevue/toastservice', 'ziggy-js', 'three']
  },
  test: {
    projects: [{
      extends: true,
      plugins: [
      // The plugin will run tests for the stories defined in your Storybook config
      // See options at: https://storybook.js.org/docs/next/writing-tests/integrations/vitest-addon#storybooktest
      storybookTest({
        configDir: path.join(dirname, '.storybook')
      })],
      test: {
        name: 'storybook',
        browser: {
          enabled: true,
          headless: true,
          provider: playwright({}),
          instances: [{
            browser: 'chromium'
          }]
        }
      }
    }]
  }
});