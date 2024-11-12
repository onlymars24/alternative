import { createStore } from 'vuex';
import axios from 'axios'
import axiosAdmin from '../axiosAdmin.js'
import router from '../router'

export default createStore({
  state: {
    windowHeader: 0,
    member: null
  },
  getters: {
    
  },
  mutations: {
    windowHeader(state, e) {
      state.windowHeader = e;
    },
    setMember(state, member) {
      state.member = member
    },
    unsetMember(state){
      state.member = null
    }
  },
  actions: {
    async getMember(context){
      const axiosRouter = axios.create({
        baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`
      })
      axiosRouter.defaults.withCredentials = true;
      axiosRouter.defaults.withXSRFToken = true;
      await axiosRouter
      .get('/member')
      .then(response => {
        console.log('getMember')
        console.log(response)
        context.commit('setMember', response.data.member)
      })
      .catch(error => {
        console.log(error)
      })
    },
    async logout(context){
      await axiosAdmin
      .post('/member/logout')
      .then(response => {
          context.commit('unsetMember')
          router.push({ name: 'ALogin'})
          console.log(response)
      })
      .catch(error => {
        console.log(error)
      })
    }
  },
  modules: {
  },
});