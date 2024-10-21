import axios from 'axios'
import store from './store'
import router from './router'

const axiosAdmin = axios.create({
    baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
})

// axiosAdmin.interceptors.request.use(config => {
//     config.headers.Authorization = 'Bearer '+ localStorage.getItem('authAdminToken')
//     return config;
// })

axiosAdmin.defaults.withCredentials = true;
axiosAdmin.defaults.withXSRFToken = true;


axiosAdmin.interceptors.response.use(response => {
    return response;
  }, error => {
    if (error.response.status === 401) {
      store.commit('unsetMember')
      router.push({name: 'ALogin'})
    }
    if(error.response.status === 403) {
      router.push({name: 'KladrStationPage'})
    }
    throw error;
  })


export default axiosAdmin;