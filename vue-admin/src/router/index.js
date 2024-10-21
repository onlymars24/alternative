import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import store from '../store/index.js'
import ElementPlusUse from '../views/admin/ElementPlusUse.vue'
const routes = [
  {
    meta: {
      requiresGuest: true
    },
    path: '/login',
    component: () => import('../views/admin/ALogin.vue'),
    name: 'ALogin'
  },
  {
    path: '/kladr/station/page',
    component: () => import('../views/admin/KladrStationPage.vue'),
    name: 'KladrStationPage'
  },   
  {
    path: '/element',
    component: () => ElementPlusUse,
    name: 'ElementPlusUse'
  },   
  {
    name: 'App',
    // component: () => import('../App.vue'),
    meta: {
      requiresAuth: true
    },
   
    children: [
      {
        path: '/roles',
        component: () => import('../views/admin/Roles.vue'),
        name: 'Roles'
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
    ]
  },


  // {
  //   path: '/:catchAll(.*)',
  //   name: 'Error',
  //   component: () => import('../views/Error.vue')
  // },
]

let member = null

// const axiosRouter = axios.create({
//   baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
// })

// // axiosAdmin.interceptors.request.use(config => {
// //     config.headers.Authorization = 'Bearer '+ localStorage.getItem('authAdminToken')
// //     return config;
// // })

// axiosRouter.defaults.withCredentials = true;
// axiosRouter.defaults.withXSRFToken = true;


// await axiosRouter
// .get('/member')
// .then(response => {
//   console.log(response)
//   member = response.data.member
// })
// .catch(error => {
//   console.log(error)
// })

await store.dispatch('getMember')


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  console.log('store.state.member')
  console.log(store.state.member)
  if (to.meta.requiresGuest && store.state.member) {
    next({name: 'KladrStationPage'})
  }
  else if(to.meta.requiresAuth && !store.state.member){
    next({name: 'ALogin'});
  }
  else {
    next();
  }

})


export default router