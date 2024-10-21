import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5';
import { createRequire } from 'node:module';
import svgLoader from "vite-svg-loader";

import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/


const require = createRequire( import.meta.url );

export default defineConfig({
  plugins: [
    vue(),
    ckeditor5( { theme: require.resolve( '@ckeditor/ckeditor5-theme-lark' ) } ),
    svgLoader(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  build: {
    target: 'esnext'
  }
})
