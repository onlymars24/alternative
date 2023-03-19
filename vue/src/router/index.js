import {createRouter, createWebHistory} from 'vue-router'
import Main from '../views/Main.vue'
import Races from '../views/Races.vue'
import SeatPage from '../views/SeatPage.vue'
import form from '../views/form.vue'
import AuthorizationPage from '../views/AuthorizationPage.vue'
import PersonalAccount from '../views/PersonalAccount.vue'


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
  {
    path: '/seats/:race_id',
    name: 'SeatPage',
    component: SeatPage
  },
  {
    path: '/form/:race_id',
    name: 'form',
    component: form
  },
  {
    path: '/login',
    name: 'Login',
    component: AuthorizationPage
  },
    {
    path: '/account',
    name: 'Account',
    component: PersonalAccount
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
