import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  head: {
    script: [
      {
        src: "https://vk.com/js/api/openapi.js?169",
        type: "text/javascript"
      }
    ]
  }
})
