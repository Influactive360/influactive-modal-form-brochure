import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        frontEnd: 'front-end/frontForm.js',
        backEndStyle: 'back-end/post-type/style.scss',
        backEndForm: 'back-end/post-type/form/backForm.js',
        backEndLayout: 'back-end/post-type/layout/layout.js',
        backEndTab: 'back-end/post-type/tab/tab.js',
      },
      output: {
        entryFileNames: '[name].bundled.js',
        assetFileNames: '[name].bundled.[ext]',
      },
    },
  },
})
