import { createStore } from 'vuex';
import axiosClient from '../axios'

export default createStore({
  state: {
    windowHeader: 0,

    kladrPage: null,
    stationPage: null,

    dispatchItem: null,
    dispatchData: [],
    dispatchNameConst: null,

    arrivalItem: null,
    arrivalData: [],
    arrivalNameConst: null,


    selectDataLoading: false
  },
  getters: {

  },
  mutations: {
    windowHeader(state, e) {
      state.windowHeader = e;
    },
    selectDataLoadingStart(state){
      state.selectDataLoading = true
    },
    selectDataLoadingEnd(state){
      state.selectDataLoading = false
    },
    dispatchDataSet(state, selectData){
      state.dispatchData = selectData
    },
    arrivalDataSet(state, selectData){
      state.arrivalData = selectData
    },
    arrivalItemDelete(state){
      state.arrivalItem = null
      state.arrivalData = []
    },
    selectDataSetBySlugs(state, selectData){
      state.dispatchItem = selectData['dispatchItem']
      state.dispatchNameConst = selectData['dispatchItem'].name
      state.dispatchData = [selectData['dispatchItem']]
      state.arrivalItem = selectData['arrivalItem']
      state.arrivalNameConst = selectData['arrivalItem'].name
      state.arrivalData = [selectData['arrivalItem']]
    },
    kladrPageSet(state, kladrPage){
      state.kladrPage = kladrPage
      state.dispatchItem = kladrPage.kladr
      state.dispatchNameConst = state.dispatchItem.name
      state.dispatchData = [state.dispatchItem]
    },
    stationPageSet(state, stationPage){
      state.stationPage = stationPage
      if(stationPage.station.kladr.name == stationPage.station.name){
        state.dispatchItem = stationPage.station.kladr
      }
      else{
        state.dispatchItem = stationPage.station
      }
      state.dispatchNameConst = state.dispatchItem.name
      state.dispatchData = [state.dispatchItem]
    }
  },
  actions: {
    async selectDataSearch(store, playload){
      let query = playload.query
      let selectItemType = playload.selectItemType
      let search = query
      if(query.length < 1 && store.state[selectItemType+'Item']){
        search = store.state[selectItemType+'Item'].name
      }
      else if(query.length < 2){
        return
      }
      store.commit('selectDataLoadingStart')
      await axiosClient
      .get('/'+selectItemType+'/data?search='+search+(selectItemType == 'arrival' ? '&sourceId='+store.state.dispatchItem.sourceId : ''))
      .then(response => {
          store.commit(selectItemType+'DataSet', Object.values(response.data[selectItemType+'Data']))
      })
      .catch(error => {
          console.log(error)
      })
      store.commit('selectDataLoadingEnd')
    },
  },
  modules: {
  },
});