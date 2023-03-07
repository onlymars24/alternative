import {createRouter, createWebHistory} from 'vue-router'
import Main from '../views/Main.vue'
import Races from '../views/Races.vue'

const routes = [
  {
    path: '/',
    name: 'Main',
    component: Main
  },
  {
    path: '/races/:dispatch_id/:dispatch_name/:arrival_id/:arrival_name/:date',
    name: 'Races',
    component: Races
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
