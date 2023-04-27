import {createRouter, createWebHistory} from 'vue-router'
import Main from '../views/Main.vue'
import Races from '../views/Races.vue'
import SeatPage from '../views/SeatPage.vue'
import form from '../views/form.vue'
import AuthorizationPage from '../views/AuthorizationPage.vue'
import PersonalAccount from '../views/PersonalAccount.vue'
import Error from '../views/Error.vue'
import Payment from '../views/Payment.vue'
import Faq from '../views/Faq.vue'
import RacesWithoutDate from '../views/RacesWithoutDate.vue'
import Avtobus from '../views/Avtobus.vue'


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
    path: '/races/:dispatch_id/:dispatch_name/:arrival_id/:arrival_name/',
    name: 'RacesWithoutDate',
    component: RacesWithoutDate
  },
  {
    path: '/avtobus/:dispatch_name/:arrival_name/',
    name: 'Avtobus',
    component: Avtobus
  },

  {
    path: '/seats/:race_id',
    name: 'SeatPage',
    component: SeatPage
  },
  {
    path: '/form/:race_id',
    name: 'Form',
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
  {
    path: '/payment/:order_id',
    name: 'Payment',
    component: Payment
  },
  {
    path: '/faq',
    name: 'Faq',
    component: Faq
  },




  {
    path: '/:catchAll(.*)',
    name: 'Error',
    component: Error
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authToken = localStorage.getItem('authToken')

  if(authToken){
    if(to.name === 'Login'){
      return next({name: 'Main'})
    }
  }
  else{
    if(to.name === 'Account'){
      return next({name: 'Login'})
    }
  }

  next()
})

export default router
