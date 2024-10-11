import axios from 'axios'
import store from './store'

const axiosAdmin = axios.create({
    baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
})

axiosAdmin.interceptors.request.use(config => {
    config.headers.Authorization = 'Bearer '+ localStorage.getItem('authAdminToken')
    return config;
})

export default axiosAdmin;
