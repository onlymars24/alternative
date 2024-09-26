import axios from 'axios'
// import store from './store'

const axiosClient = axios.create({
    baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
})

axiosClient.interceptors.request.use(config => {
    config.headers.Authorization = 'Bearer '+ localStorage.getItem('authToken')
    return config;
})

export default axiosClient;