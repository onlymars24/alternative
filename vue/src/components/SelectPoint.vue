<script>
import store from '../store'
import Multiselect from 'vue-multiselect'
import vSelect from 'vue-select'
import SelectPointLabel from '../components/SelectPointLabel.vue'

export default
{
  props: [ 'typeRu', 'typeEn' ],
  components: { Multiselect, 'v-select': vSelect, SelectPointLabel },
  data(){
    return {
      query: ''
    }
  },
  methods: {
    selectDataSearch(query){
      this.query = query ? query : this.query
        store.dispatch('selectDataSearch', {query: this.query, selectItemType: this.typeEn})
    },
    closeEvent(){
      console.log('blur')
      if(!store.state[this.typeEn+'Item']){
        store.commit(this.typeEn+'DataSet', [])
      }
      else{
        store.commit(this.typeEn+'DataSet', [store.state[this.typeEn+'Item']])
      }
    }
  }
}
</script>

<template>
    <div class="main__table-table" :class="$store.state[typeEn+'Data'].length == 0 ? 'empty__dropdown' : ''">
      <p style="margin: 0; padding-left: 20px; padding-top: 12px; padding-bottom: 5px; font-size: 12px;">{{ typeRu }}</p>
      <v-select
        @search="selectDataSearch"
        @search:focus="selectDataSearch"
        @search:blur="closeEvent"
        :options="$store.state[typeEn+'Data']"
        label="name"
        v-model="$store.state[typeEn+'Item']"
        :disabled="typeEn == 'arrival' && !$store.state.dispatchItem"
        :placeholder="'Заполните '+typeRu.toLowerCase()"
        :loading="$store.state.selectDataLoading"
      >
      <template #option="option">
        <div style="cursor: pointer;">
          <SelectPointLabel v-if="option.sourceId.includes('kladrs')" :sourceId="option.sourceId" :name="option.name" :region="option.region" :district="option.district"/>
          <SelectPointLabel v-if="option.sourceId.includes('stations') && option.kladr" :sourceId="option.sourceId" :name="option.name" :region="option.kladr.region" :district="option.kladr.district"/>
          <SelectPointLabel v-if="option.sourceId.includes('cache_arrival_points')" :sourceId="option.sourceId" :name="option.name" :region="option.region" :district="option.details"/>
        </div>
      </template>
      <template #no-options="{ search, searching, loading }" style="height: 0; width: 0; display: none;">
      <span></span>
      </template>
      <template #spinner="{ loading }">
        <div
          v-if="loading"
          style="border-left-color: rgba(88, 151, 251, 0.71)"
          class="vs__spinner"
        >
          The .vs__spinner class will hide the text for me.
        </div>
      </template>
      </v-select>
  </div>

</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style >
@import 'vue-select/dist/vue-select.css';
/* .el-select {

} */
.main__table-table *, .is-focus {
  border: none !important;
  box-shadow: none !important;
  outline: none !important;
  cursor: text;
}
/* Убираем все границы у el-select */
.el-select {
  border: none !important;
  box-shadow: none !important;
  outline: none !important;
  cursor: text;
}

/* Убираем границы у выпадающего списка */
.el-select .el-input__inner {
  border: none !important;
  box-shadow: none !important;
  outline: none !important;
  cursor: text;
}

/* Убираем фокусные стили */
.el-select:focus-within .el-input__inner {
  box-shadow: none !important;
  border: none !important;
  outline: none !important;
  cursor: text;
}
.el-select .el-input.is-focus .el-input__wrapper{
  box-shadow: none !important;
  cursor: text;
}

.el-select .el-input__wrapper.is-focus{
  box-shadow: none !important;
  cursor: text;
}

</style>