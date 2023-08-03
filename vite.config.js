import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        frontEnd: 'assets/js/modal-form-script.js',
        backEnd: 'assets/js/admin.js',
      },
      output: {
        entryFileNames: '[name].bundled.js',
        assetFileNames: '[name].bundled.[ext]',
      },
    },
  },
})
