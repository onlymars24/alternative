import { createRouter, createWebHistory } from 'vue-router'

import ElementPlusUse from '../views/admin/ElementPlusUse.vue'
const routes = [
  {
    path: '/login',
    component: () => import('../views/admin/ALogin.vue'),
    name: 'ALogin'
  },
  {
    path: '/tickets',
    component: () => import('../views/admin/Tickets.vue'),
    name: 'Tickets'
  },
  {
    path: '/order/:order_id',
    component: () => import('../views/admin/Order.vue'),
    name: 'Order'
  },
  {
    path: '/transactions',
    component: () => import('../views/admin/Transactions.vue'),
    name: 'Transactions'
  },
  {
    path: '/sms',
    component: () => import('../views/admin/sms.vue'),
    name: 'Sms'
  },
  {
    path: '/whatsapp',
    component: () => import('../views/admin/WhatsAppSms.vue'),
    name: 'WhatsAppSms'
  },
  {
    path: '/reports',
    component: () => import('../views/admin/Reports.vue'),
    name: 'Reports'
  },
  {
    path: '/feedback',
    component: () => import('../views/admin/Feedback.vue'),
    name: 'Feedback'
  },
  {
    path: '/debugging',
    component: () => import('../views/admin/Debugging.vue'),
    name: 'Debugging'
  },
  {
    path: '/edit/main',
    component: () => import('../views/admin/EditMain.vue'),
    name: 'EditMain'
  },
  {
    path: '/edit/upcoming/trips',
    component: () => import('../views/admin/EditUpcomingTrips.vue'),
    name: 'EditUpcomingTrips'
  },
  {
    path: '/edit/points',
    component: () => import('../views/admin/EditPoints.vue'),
    name: 'EditPoints'
  },
  {
    path: '/kladr/station/page',
    component: () => import('../views/admin/KladrStationPage.vue'),
    name: 'KladrStationPage'
  },
  {
    path: '/stations',
    component: () => import('../views/admin/Stations.vue'),
    name: 'Stations'
  },

  {
    path: '/bus/routes',
    component: () => import('../views/admin/BusRoutes.vue'),
    name: 'BusRoutes'
  },
  {
    path: '/bus/routes/edit/:dispatchPointName/:arrivalPointName',
    component: () => import('../views/admin/BusRoutesEdit.vue'),
    name: 'BusRoutesEdit'
  },

  {
    path: '/event/create',
    name: 'EventCreate',
    component: () => import('../views/admin/EventCreate.vue')
  }, 
  {
    path: '/event/edit',
    name: 'EventEdit',
    component: () => import('../views/admin/EventEdit.vue')
  }, 
  {
    path: '/bonuses/transactions',
    name: 'BonusesTransactions',
    component: () => import('../views/admin/BonusesTransactions.vue')
  }, 
  {
    path: '/bonuses/users',
    name: 'BonusesUsers',
    component: () => import('../views/admin/BonusesUsers.vue')
  }, 
  {
    path: '/bonuses/user/:user_id',
    name: 'BonusesUser',
    component: () => import('../views/admin/BonusesUser.vue')
  }, 
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('../views/admin/Settings.vue')
  }, 
  {
    path: '/edit/robots/txt',
    name: 'EditRobotsTxt',
    component: () => import('../views/admin/EditRobotsTxt.vue')
  }, 
  {
    path: '/kladr',
    name: 'Kladr',
    component: () => import('../views/admin/Kladr.vue')
  }, 
  // {
  //   path: '/:catchAll(.*)',
  //   name: 'Error',
  //   component: () => import('../views/Error.vue')
  // },
]



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
