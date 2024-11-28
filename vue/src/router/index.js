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
import Error from '../views/Error.vue'
import store from '../store'
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
import axiosClient from '../axios';



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
        path: '/'+encodeURI('автобус')+'/:dispatchSlug/:arrivalSlug',
        name: 'Races',
        component: () => import('../views/Races.vue'),
        async beforeEnter(to, from){
          // let status = 404
    
          if(import.meta.env.VITE_ENV == 'production'){
              let status = null
              await axiosClient
              .get(window.location.href)
              .then(response => {
                  console.log('router-response')
                  console.log(response)
    
              })
              .catch(error => {
                status = error.response.status
                console.log(error)
                  
              })
              if(status == 404){
                console.log(decodeURIComponent(to.path))
                  return {
                    name: 'Error',
                    params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
                    query: to.query,
                    hash: to.hash
                  }
              }
          }

          let selectData = null
          await axiosClient
          .get('/dispatch/arrival/check?dispatchSlug='+to.params.dispatchSlug+'&arrivalSlug='+to.params.arrivalSlug)
          .then(response => {
            selectData = response.data
            console.log(selectData)
          })
          .catch(error => {
            console.log(error)
          })
          if(!selectData['dispatchItem'] || !selectData['arrivalItem']){
            // 404
            console.log('зашел')
            return {
              name: 'Error',
              params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
              query: to.query,
              hash: to.hash
            }
          }
          console.log('вышел')
          store.commit('selectDataSetBySlugs', selectData)
          

    
          // } 

        }
      },
      {
        path: '/'+encodeURI('расписание')+'/:region_code/:settlement_name',
        name: 'KladrPage',
        component: () => import('../views/KladrPage.vue'),
        async beforeEnter(to, from){
          // let status = 404
    
          if(import.meta.env.VITE_ENV == 'production'){
              let status = null
              await axiosClient
              .get(window.location.href)
              .then(response => {
                  console.log('router-response')
                  console.log(response)
    
              })
              .catch(error => {
                status = error.response.status
                console.log(error)
                  
              })
              if(status == 404){
                console.log(decodeURIComponent(to.path))
                  return {
                    name: 'Error',
                    params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
                    query: to.query,
                    hash: to.hash
                  }
              }
          }
          let kladrPage = null
          console.log(to.params['settlement_name'], to.params['region_code'])
          await axiosClient
          .get('/kladr/station/page?url_settlement_name='+(to.params['settlement_name'])
          +'&url_region_code='+to.params['region_code']
          +'&pageType=k'
          )
          .then(response => {
              console.log(response)
              kladrPage = response.data.page
              // this.content = JSON.parse(this.station.data).content
          })
          .catch(error => {
              console.log(error)
          })
          if(!kladrPage || !kladrPage.kladr){
            return {
              name: 'Error',
              params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
              query: to.query,
              hash: to.hash
            }
          }
          store.commit('kladrPageSet', kladrPage)
        } 
      },
      {
        path: '/'+encodeURI('автовокзал')+'/:region_code/:settlement_name',
        name: 'StationPage',
        component: () => import('../views/StationPage.vue'),
        async beforeEnter(to, from){
          // let status = 404
    
          if(import.meta.env.VITE_ENV == 'production'){
              let status = null
              await axiosClient
              .get(window.location.href)
              .then(response => {
                  console.log('router-response')
                  console.log(response)
    
              })
              .catch(error => {
                status = error.response.status
                console.log(error)
                  
              })
              if(status == 404){
                console.log(decodeURIComponent(to.path))
                  return {
                    name: 'Error',
                    params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
                    query: to.query,
                    hash: to.hash
                  }
              }
          }
          let stationPage = null
          console.log(to.params['settlement_name'], to.params['region_code'])
          await axiosClient
          .get('/kladr/station/page?url_settlement_name='+(to.params['settlement_name'])
          +'&url_region_code='+to.params['region_code']
          +'&pageType=s'
          )
          .then(response => {
              console.log(response)
              stationPage = response.data.page
              // this.content = JSON.parse(this.station.data).content
          })
          .catch(error => {
              console.log(error)
          })
          if(!stationPage || !stationPage.station || !stationPage.station.kladr){
            return {
              name: 'Error',
              params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
              query: to.query,
              hash: to.hash
            }
          }
          store.commit('stationPageSet', stationPage)
    
        } 
      },      


  {
    path: '/'+encodeURI('обратный')+'/'+encodeURI('билет')+'/:order_id',
    name: 'ReturnRace',
    component: () => import('../views/ReturnRace.vue'),
    async beforeEnter(to, from){
      let kladrSlugs
      await axiosClient
      .get('/check/return/race?orderId='+to.params['order_id'])
      .then(response => {
        console.log(response)
        kladrSlugs = response.data
      })
      .catch(error => {
        console.log(error)
      })
      console.log(kladrSlugs)
      if(!kladrSlugs['dispatchKladrSlug'] || !kladrSlugs['arrivalKladrSlug']){
        return {
          name: 'Error',
          params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
          query: to.query,
          hash: to.hash
        }
      }
      window.location.replace(window.location.origin+'/автобус/'+kladrSlugs['arrivalKladrSlug']+'/'+kladrSlugs['dispatchKladrSlug']);
    }
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
    path: '/:pathMatch(.*)*',
    name: 'Error',
    component: Error
  },
]






const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {

  // let status
  // await axiosClient
  // .get(window.location.href)
  // .then(response => {
  //     console.log('router-response')
  //     console.log(response)

  // })
  // .catch(error => {
  //   status = error.response.status
  //   console.log(error)
      
  // })
  // if(status == 404){
  //   console.log(decodeURIComponent(to.path))
  //     return {
  //       name: 'Error',
  //       params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
  //       query: to.query,
  //       hash: to.hash
  //     }
  // }


// if(import.meta.env.VITE_ENV == 'production'){
    // let status = 404
    // await axiosClient
    // .get(window.location.href)
    // .then(response => {
    //     console.log('router-response')
    //     console.log(response)

    // })
    // .catch(error => {
    //   status = error.response.status
    //   console.log(error)
        
    // })
    // if(status == 404){
    //   console.log(decodeURIComponent(to.path))
    //     return {
    //       name: 'Error',
    //       params: {pathMatch: decodeURIComponent(to.path).split('/').slice(1)},
    //       query: to.query,
    //       hash: to.hash
    //     }
    // }  

// }

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