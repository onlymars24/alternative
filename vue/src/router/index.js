import {createRouter, createWebHistory, createMemoryHistory} from 'vue-router'
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
import ALogin from '../views/admin/ALogin.vue'
import Tickets from '../views/admin/Tickets.vue'
import Order from '../views/admin/Order.vue'
import Sms from '../views/admin/Sms.vue'
import Admin from '../components/admin/Admin.vue'
import Transactions from '../views/admin/Transactions.vue'
import Reports from '../views/admin/Reports.vue'
import Feedback from '../views/admin/Feedback.vue'
import ReturnConditions from '../views/ReturnConditions.vue'
import Debugging from '../views/admin/Debugging.vue'
import EditMain from '../views/admin/EditMain.vue'
import EditPoints from '../views/admin/EditPoints.vue'
import BusStations from '../views/admin/BusStations.vue'
import BusStation from '../views/BusStation.vue'
import ReturnRace from '../views/ReturnRace.vue'
import New from '../views/New.vue'
import News from '../views/News.vue'
import EventCreate from '../views/admin/EventCreate.vue'
import EventEdit from '../views/admin/EventEdit.vue'
import Bonuses from '../views/admin/Bonuses.vue'


const routes = [
  // {
  //   path: '/:catchAll(.*)',
  //   name: 'Error',
  //   component: Error
  // },
  {
    path: '/',
    name: 'Main',
    component: Main
  },
  {
    path: '/'+encodeURI('автовокзал')+'/:title',
    name: 'BusStation',
    component: BusStation
  },
  {
    path: '/'+encodeURI('автобус')+'/:dispatch_name/:arrival_name',
    name: 'Races',
    component: Races
  },
  {
    path: '/'+encodeURI('обратный')+'/'+encodeURI('билет')+'/:dispatch_point_id/:dispatch_station_name/:arrival_point_id/:arrival_station_name/:order_id',
    name: 'ReturnRace',
    component: ReturnRace
  },
  {
    path: '/seats/:dispatch_point_id/:arrival_point_id/:date/:race_id',
    name: 'SeatPage',
    component: SeatPage
  },
  {
    path: '/form/:dispatch_point_id/:arrival_point_id/:date/:race_id',
    name: 'Form',
    component: form
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
    path: '/return/conditions',
    name: 'ReturnConditions',
    component: ReturnConditions
  },
  {
    path: '/faq',
    name: 'Faq',
    component: Faq
  },
  {
    path: '/news',
    name: 'News',
    component: News
  },  
  {
    path: '/new/:id',
    name: 'New',
    component: New
  },

  {
    path: '/fj239f3j984jsdiaisja',
    component: Admin,
    children: [
      {
        path: 'login',
        component: ALogin,
        name: 'ALogin'
      },
      {
        path: 'tickets',
        component: Tickets,
        name: 'Tickets'
      },
      {
        path: 'order/:order_id',
        component: Order,
        name: 'Order'
      },
      {
        path: 'transactions',
        component: Transactions,
        name: 'Transactions'
      },
      {
        path: 'sms',
        component: Sms,
        name: 'Sms'
      },
      {
        path: 'reports',
        component: Reports,
        name: 'Reports'
      },
      {
        path: 'feedback',
        component: Feedback,
        name: 'Feedback'
      },
      {
        path: 'debugging',
        component: Debugging,
        name: 'Debugging'
      },
      {
        path: 'edit/main',
        component: EditMain,
        name: 'EditMain'
      },
      {
        path: 'edit/points',
        component: EditPoints,
        name: 'EditPoints'
      },
      {
        path: 'bus/stations',
        component: BusStations,
        name: 'BusStations'
      },
      {
        path: 'event/create',
        name: 'EventCreate',
        component: EventCreate
      }, 
      {
        path: 'event/edit',
        name: 'EventEdit',
        component: EventEdit
      }, 
      {
        path: 'bonuses',
        name: 'Bonuses',
        component: Bonuses
      }, 
    ]
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
  const authAdminToken = localStorage.getItem('authAdminToken')

  if(!authToken){
    if(to.name === 'Account'){
      return next({name: 'Main'})
    }
  }

  if(authAdminToken){
    if(to.name === 'ALogin'){
      return next({name: 'Tickets'})
    }
  }
  else{
    if(to.name === 'Tickets' || to.name === 'Order' || to.name === 'Reports' || to.name === 'Feedback' || to.name === 'Debugging' || to.name === 'BusStations' || to.name === 'EditPoints'
     || to.name === 'EditMain'){
      return next({name: 'ALogin'})
    }
  }

  next()
})

export default router