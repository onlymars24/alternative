import {createRouter, createWebHistory, createMemoryHistory} from 'vue-router'
// import Main from '../views/Main.vue'
// import Races from '../views/Races.vue'
// import SeatPage from '../views/SeatPage.vue'
// import form from '../views/form.vue'
// import AuthorizationPage from '../views/AuthorizationPage.vue'
// import PersonalAccount from '../views/PersonalAccount.vue'
// import Error from '../views/Error.vue'
// import Faq from '../views/Faq.vue'
// import Contacts from '../views/Contacts.vue'
// import RacesWithoutDate from '../views/RacesWithoutDate.vue'
// import Avtobus from '../views/Avtobus.vue'
// import ALogin from '../views/admin/ALogin.vue'
// import Tickets from '../views/admin/Tickets.vue'
// import Order from '../views/admin/Order.vue'
// import Sms from '../views/admin/Sms.vue'
// import Admin from '../components/admin/Admin.vue'
// import Transactions from '../views/admin/Transactions.vue'
// import Reports from '../views/admin/Reports.vue'
// import Feedback from '../views/admin/Feedback.vue'
// import ReturnConditions from '../views/ReturnConditions.vue'
// import Debugging from '../views/admin/Debugging.vue'
// import ElementPlusUse from '../views/admin/ElementPlusUse.vue'
// import EditUpcomingTrips from '../views/admin/EditUpcomingTrips.vue'
// import EditPoints from '../views/admin/EditPoints.vue'
// import KladrStationPage from '../views/admin/KladrStationPage.vue'
// import StationPage from '../views/StationPage.vue'

// import KladrPage from '../views/KladrPage.vue'

// import ReturnRace from '../views/ReturnRace.vue'
// import New from '../views/New.vue'
// import News from '../views/News.vue'
// import EventCreate from '../views/admin/EventCreate.vue'
// import EventEdit from '../views/admin/EventEdit.vue'
// import BonusesTransactions from '../views/admin/BonusesTransactions.vue'
// import BonusesUsers from '../views/admin/BonusesUsers.vue'
// import BonusesUser from '../views/admin/BonusesUser.vue'

// import BusRoutes from '../views/admin/BusRoutes.vue'
// import BusRoutesEdit from '../views/admin/BusRoutesEdit.vue'
// import WhatsAppSms from '../views/admin/WhatsAppSms.vue'
// import Settings from '../views/admin/Settings.vue'
// import EditRobotsTxt from '../views/admin/EditRobotsTxt.vue'
// import Kladr from '../views/admin/Kladr.vue'
// import Stations from '../views/admin/Stations.vue'



const routes = [
  // {
  //   path: '/:catchAll(.*)',
  //   name: 'Error',
  //   component: Error
  // },
  {
    path: '/',
    name: 'Main',
    component: () => import('../views/Main.vue')
  },
  {
    path: '/'+encodeURI('расписание')+'/:region_code/:settlement_name',
    name: 'KladrPage',
    component: () => import('../views/KladrPage.vue')
  },
  {
    path: '/'+encodeURI('автовокзал')+'/:region_code/:settlement_name',
    name: 'StationPage',
    component: () => import('../views/StationPage.vue')
  },
  {
    path: '/'+encodeURI('автобус')+'/:dispatch_name/:arrival_name',
    name: 'Races',
    component: () => import('../views/Races.vue')
  },
  {
    path: '/'+encodeURI('обратный')+'/'+encodeURI('билет')+'/:dispatch_point_id/:dispatch_station_name/:arrival_point_id/:arrival_station_name/:order_id',
    name: 'ReturnRace',
    component: () => import('../views/ReturnRace.vue')
  },
  {
    path: '/seats/:dispatch_point_id/:arrival_point_id/:date/:race_id',
    name: 'SeatPage',
    component: () => import('../views/SeatPage.vue')
  },
  {
    path: '/form/:dispatch_point_id/:arrival_point_id/:date/:race_id',
    name: 'Form',
    component: () => import('../views/form.vue')
  },
  {
    path: '/account',
    name: 'Account',
    component: () => import('../views/PersonalAccount.vue')
  },
  {
    path: '/return/conditions',
    name: 'ReturnConditions',
    component: () => import('../views/ReturnConditions.vue')
  },
  {
    path: '/faq',
    name: 'Faq',
    component: () => import('../views/Faq.vue')
  },
  {
    path: '/'+encodeURI('контакты'),
    name: 'Contacts',
    component: () => import('../views/Contacts.vue')
  },
  {
    path: '/'+encodeURI('новости'),
    name: 'News',
    component: () => import('../views/News.vue')
  },  
  {
    path: '/'+encodeURI('новости')+'/:id',
    name: 'New',
    component: () => import('../views/New.vue')
  },

  // {
  //   path: '/fj239f3j984jsdiaisja',
  //   component: () => import('../components/admin/Admin.vue'),
  //   children: [
  //     {
  //       path: 'login',
  //       component: () => import('../views/admin/ALogin.vue'),
  //       name: 'ALogin'
  //     },
  //     {
  //       path: 'tickets',
  //       component: () => import('../views/admin/Tickets.vue'),
  //       name: 'Tickets'
  //     },
  //     {
  //       path: 'order/:order_id',
  //       component: () => import('../views/admin/Order.vue'),
  //       name: 'Order'
  //     },
  //     {
  //       path: 'transactions',
  //       component: () => import('../views/admin/Transactions.vue'),
  //       name: 'Transactions'
  //     },
  //     {
  //       path: 'sms',
  //       component: () => import('../views/admin/Sms.vue'),
  //       name: 'Sms'
  //     },
  //     {
  //       path: 'whatsapp',
  //       component: () => import('../views/admin/WhatsAppSms.vue'),
  //       name: 'WhatsAppSms'
  //     },
  //     {
  //       path: 'reports',
  //       component: () => import('../views/admin/Reports.vue'),
  //       name: 'Reports'
  //     },
  //     {
  //       path: 'feedback',
  //       component: () => import('../views/admin/Feedback.vue'),
  //       name: 'Feedback'
  //     },
  //     {
  //       path: 'debugging',
  //       component: () => import('../views/admin/Debugging.vue'),
  //       name: 'Debugging'
  //     },
  //     {
  //       path: 'edit/main',
  //       component: () => import('../views/admin/EditMain.vue'),
  //       name: 'EditMain'
  //     },
  //     {
  //       path: 'edit/upcoming/trips',
  //       component: () => import('../views/admin/EditUpcomingTrips.vue'),
  //       name: 'EditUpcomingTrips'
  //     },
  //     {
  //       path: 'edit/points',
  //       component: () => import('../views/admin/EditPoints.vue'),
  //       name: 'EditPoints'
  //     },
  //     {
  //       path: 'kladr/station/page',
  //       component: () => import('../views/admin/KladrStationPage.vue'),
  //       name: 'KladrStationPage'
  //     },
  //     {
  //       path: 'stations',
  //       component: () => import('../views/admin/Stations.vue'),
  //       name: 'Stations'
  //     },

  //     {
  //       path: 'bus/routes',
  //       component: () => import('../views/admin/BusRoutes.vue'),
  //       name: 'BusRoutes'
  //     },
  //     {
  //       path: 'bus/routes/edit/:dispatchPointName/:arrivalPointName',
  //       component: () => import('../views/admin/BusRoutesEdit.vue'),
  //       name: 'BusRoutesEdit'
  //     },

  //     {
  //       path: 'event/create',
  //       name: 'EventCreate',
  //       component: () => import('../views/admin/EventCreate.vue')
  //     }, 
  //     {
  //       path: 'event/edit',
  //       name: 'EventEdit',
  //       component: () => import('../views/admin/EventEdit.vue')
  //     }, 
  //     {
  //       path: 'bonuses/transactions',
  //       name: 'BonusesTransactions',
  //       component: () => import('../views/admin/BonusesTransactions.vue')
  //     }, 
  //     {
  //       path: 'bonuses/users',
  //       name: 'BonusesUsers',
  //       component: () => import('../views/admin/BonusesUsers.vue')
  //     }, 
  //     {
  //       path: 'bonuses/user/:user_id',
  //       name: 'BonusesUser',
  //       component: () => import('../views/admin/BonusesUser.vue')
  //     }, 
  //     {
  //       path: 'settings',
  //       name: 'Settings',
  //       component: () => import('../views/admin/Settings.vue')
  //     }, 
  //     {
  //       path: 'edit/robots/txt',
  //       name: 'EditRobotsTxt',
  //       component: () => import('../views/admin/EditRobotsTxt.vue')
  //     }, 
  //     {
  //       path: 'kladr',
  //       name: 'Kladr',
  //       component: () => import('../views/admin/Kladr.vue')
  //     }, 
  //   ]
  // },
  {
    path: '/:catchAll(.*)',
    name: 'Error',
    component: () => import('../views/Error.vue')
  },
]


// const routes = [
//   {
//     path: '/',
//     name: 'Main',
//     component: Main
//   },
//   {
//     path: '/'+encodeURI('расписание')+'/:region_code/:settlement_name',
//     name: 'KladrPage',
//     component: () => KladrPage
//   },
//   {
//     path: '/'+encodeURI('автовокзал')+'/:region_code/:settlement_name',
//     name: 'StationPage',
//     component: StationPage
//   },
//   {
//     path: '/'+encodeURI('автобус')+'/:dispatch_name/:arrival_name',
//     name: 'Races',
//     component: Races
//   },
//   {
//     path: '/'+encodeURI('обратный')+'/'+encodeURI('билет')+'/:dispatch_point_id/:dispatch_station_name/:arrival_point_id/:arrival_station_name/:order_id',
//     name: 'ReturnRace',
//     component: ReturnRace
//   },
//   {
//     path: '/seats/:dispatch_point_id/:arrival_point_id/:date/:race_id',
//     name: 'SeatPage',
//     component: SeatPage
//   },
//   {
//     path: '/form/:dispatch_point_id/:arrival_point_id/:date/:race_id',
//     name: 'Form',
//     component: form
//   },
//   {
//     path: '/account',
//     name: 'Account',
//     component: PersonalAccount
//   },
//   {
//     path: '/return/conditions',
//     name: 'ReturnConditions',
//     component: ReturnConditions
//   },
//   {
//     path: '/faq',
//     name: 'Faq',
//     component: Faq
//   },
//   {
//     path: '/'+encodeURI('контакты'),
//     name: 'Contacts',
//     component: Contacts
//   },
//   {
//     path: '/'+encodeURI('новости'),
//     name: 'News',
//     component: News
//   },  
//   {
//     path: '/'+encodeURI('новости')+'/:id',
//     name: 'New',
//     component: New
//   },

//   {
//     path: '/fj239f3j984jsdiaisja',
//     component: Admin,
//     children: [
//       {
//         path: 'login',
//         component: ALogin,
//         name: 'ALogin'
//       },
//       {
//         path: 'tickets',
//         component: Tickets,
//         name: 'Tickets'
//       },
//       {
//         path: 'order/:order_id',
//         component: Order,
//         name: 'Order'
//       },
//       {
//         path: 'transactions',
//         component: Transactions,
//         name: 'Transactions'
//       },
//       {
//         path: 'sms',
//         component: Sms,
//         name: 'Sms'
//       },
//       {
//         path: 'whatsapp',
//         component: WhatsAppSms,
//         name: 'WhatsAppSms'
//       },
//       {
//         path: 'reports',
//         component: Reports,
//         name: 'Reports'
//       },
//       {
//         path: 'feedback',
//         component: Feedback,
//         name: 'Feedback'
//       },
//       {
//         path: 'debugging',
//         component: Debugging,
//         name: 'Debugging'
//       },
//       {
//         path: 'edit/main',
//         component: EditMain,
//         name: 'EditMain'
//       },
//       {
//         path: 'edit/upcoming/trips',
//         component: EditUpcomingTrips,
//         name: 'EditUpcomingTrips'
//       },
//       {
//         path: 'edit/points',
//         component: EditPoints,
//         name: 'EditPoints'
//       },
//       {
//         path: 'kladr/station/page',
//         component: KladrStationPage,
//         name: 'KladrStationPage'
//       },
//       {
//         path: 'stations',
//         component: Stations,
//         name: 'Stations'
//       },

//       {
//         path: 'bus/routes',
//         component: BusRoutes,
//         name: 'BusRoutes'
//       },
//       {
//         path: 'bus/routes/edit/:dispatchPointName/:arrivalPointName',
//         component: BusRoutesEdit,
//         name: 'BusRoutesEdit'
//       },

//       {
//         path: 'event/create',
//         name: 'EventCreate',
//         component: EventCreate
//       }, 
//       {
//         path: 'event/edit',
//         name: 'EventEdit',
//         component: EventEdit
//       }, 
//       {
//         path: 'bonuses/transactions',
//         name: 'BonusesTransactions',
//         component: BonusesTransactions
//       }, 
//       {
//         path: 'bonuses/users',
//         name: 'BonusesUsers',
//         component: BonusesUsers
//       }, 
//       {
//         path: 'bonuses/user/:user_id',
//         name: 'BonusesUser',
//         component: BonusesUser
//       }, 
//       {
//         path: 'settings',
//         name: 'Settings',
//         component: Settings
//       }, 
//       {
//         path: 'edit/robots/txt',
//         name: 'EditRobotsTxt',
//         component: EditRobotsTxt
//       }, 
//       {
//         path: 'kladr',
//         name: 'Kladr',
//         component: Kladr
//       }, 
//     ]
//   },
//   {
//     path: '/:catchAll(.*)',
//     name: 'Error',
//     component: Error
//   },
// ]




const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authToken = localStorage.getItem('authToken')
  // const authAdminToken = localStorage.getItem('authAdminToken')

  // if(!authToken){
  //   if(to.name === 'Account'){
  //     return next({name: 'Main'})
  //   }
  // }
  console.log('to.query')
  console.log(to.query.utm_source)
  // if(authAdminToken){
  //   if(to.name === 'ALogin'){
  //     return next({name: 'Tickets'})
  //   }
  // }
  // else{
  //   if(to.name === 'Tickets' || to.name === 'Order' || to.name === 'Transactions' || to.name === 'Sms' || to.name === 'Sms' || to.name === 'Reports' || to.name === 'Feedback'
  //    || to.name === 'Debugging' || to.name === 'EditMain' || to.name === 'EditUpcomingTrips' || to.name === 'EditPoints' || to.name === 'BusStations' ||  to.name === 'BusRoutes'
  //    || to.name === 'BusRoutesEdit' || to.name === 'EventCreate' || to.name === 'EventEdit' || to.name === 'EventEdit' || to.name === 'BonusesTransactions' ||  to.name === 'BonusesUsers'
  //    || to.name === 'BonusesUser' || to.name === 'Settings' || to.name === 'EditRobotsTxt'
  //   ){
  //     return next({name: 'ALogin'})
  //   }
  // }

  if(to.name == 'Main' || to.name == 'Races' || to.name == 'BusStation' || to.name == 'New' || to.name == 'News'){
    let utm_data_old = null;
    let utm_data_new = null;
    let newReferrer = '';
    let oldReferrer = '';

    let tempUtm = JSON.parse(localStorage.getItem('utm_data'))
    if(tempUtm && tempUtm.referrer_url){
      oldReferrer = tempUtm.referrer_url
    }
    newReferrer = (!document.referrer.includes(location.hostname) && document.referrer) ? document.referrer : oldReferrer
    
    if(to.query.utm_source || to.query.utm_medium || to.query.utm_campaign || to.query.utm_content){
      utm_data_new = {
        utm_source: to.query.utm_source ? to.query.utm_source : '',
        utm_medium: to.query.utm_medium ? to.query.utm_medium : '',
        utm_campaign: to.query.utm_campaign ? to.query.utm_campaign : '',
        utm_content: to.query.utm_content ? to.query.utm_content : '',
        referrer_url: newReferrer,
      }
      localStorage.setItem('utm_data', JSON.stringify(utm_data_new))
      return next({name: to.name, params: to.params})
    }
    else{
      utm_data_old = JSON.parse(localStorage.getItem('utm_data'))
      if(utm_data_old){
        utm_data_old.referrer_url = newReferrer
      }
      else{
        utm_data_old = {
            utm_source: '',
            utm_medium: '',
            utm_campaign: '',
            utm_content: '',
            referrer_url: newReferrer,
        }
      }
      localStorage.setItem('utm_data', JSON.stringify(utm_data_old))
    }
  }
  //utm the end

  next()
})

export default router