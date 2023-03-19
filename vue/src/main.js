import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.css';
import './style.css'
import App from './App.vue'
import store from './store'
import router from './router'


createApp(App)
.use(store)
.use(router)
.mount('#app')