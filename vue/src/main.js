import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.css';
import './style.css'
import App from './App.vue'
import store from './store'
import router from './router'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import ticketStatuses from './data/TicketStatuses';
import VueTheMask from 'vue-the-mask'
import CKEditor from '@ckeditor/ckeditor5-vue';
// import { Bootstrap5Pagination } from 'laravel-vue-pagination';

import '@fontsource/roboto/100.css';
import '@fontsource/roboto/300.css';
import '@fontsource/roboto/400.css';
import '@fontsource/roboto/500.css';
import '@fontsource/roboto/700.css';
import '@fontsource/roboto/900.css';

import '@fontsource-variable/roboto-condensed';

// import VueMasonry from 'vue-masonry-css'


createApp(App)
// .use(VueMasonry)
.use(CKEditor)
.use(VueTheMask)
.use(store)
.use(router)
.use(ElementPlus)
.use(ticketStatuses)
// .use(Bootstrap5Pagination)
.mount('#app')