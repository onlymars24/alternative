import axios from 'axios'
import store from './store'

const axiosClient = axios.create({
    baseURL: 'http://localhost:8000/api'
})

axiosClient.interceptors.request.use(config => {
    config.headers.Authorization = 'Bearer '+ localStorage.getItem('authToken')
    return config;
})

export default axiosClient;