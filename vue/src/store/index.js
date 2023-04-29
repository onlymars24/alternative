import { createStore } from 'vuex';

export default createStore({
  state: {
    windowHeader: 0
  },
  getters: {
  },
  mutations: {
    windowHeader(state, e) {
      state.windowHeader = e;
    },
  },
  actions: {
  },
  modules: {
  },
});