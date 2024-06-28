import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.css';
import './style.css'
import App from './App.vue'
import store from './store'
import router from './router'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import ticketStatuses from './data/TicketStatuses';
import VueTheMask from 'vue-the-mask'
import CKEditor from '@ckeditor/ckeditor5-vue';

import '@fontsource/roboto/100.css';
import '@fontsource/roboto/300.css';
import '@fontsource/roboto/400.css';
import '@fontsource/roboto/500.css';
import '@fontsource/roboto/700.css';
import '@fontsource/roboto/900.css';

import '@fontsource-variable/roboto-condensed';

// import VueMasonry from 'vue-masonry-css'

const vuetify = createVuetify({
    components,
    directives,
  })

createApp(App)
// .use(VueMasonry)
.use(CKEditor)
.use(VueTheMask)
.use(store)
.use(router)
.use(ElementPlus)
.use(ticketStatuses)
.mount('#app')