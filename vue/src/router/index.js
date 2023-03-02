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
    path: '/races',
    name: 'Races',
    component: Races
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
