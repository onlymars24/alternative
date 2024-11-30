<script>
import store from '../store'
import Multiselect from 'vue-multiselect'
import vSelect from 'vue-select'

export default
{
  props: [ 'typeRu', 'typeEn' ],
  components: { Multiselect, 'v-select': vSelect },
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
    <div class="main__table-table">
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
        <span v-if="option.sourceId.includes('stations') && option.kladr" style="font-size: 17px;">{{ option.name}}<br v-if="option.kladr.region"/>
          {{(option.kladr.region ? option.kladr.region : '')}}<br v-if="option.kladr.district"/>
          {{(option.kladr.district ? option.kladr.district : '') }}</span>
        <span v-if="option.sourceId.includes('kladrs')" style="font-size: 20px;">{{ option.name}}<br v-if="option.region"/>
          {{(option.region ? option.region : '')}}<br v-if="option.district"/>
          {{(option.district ? option.district : '') }}</span>
        <span v-if="option.sourceId.includes('cache_arrival_points')" style="font-size: 15px;">{{ option.name}}<br v-if="option.region"/>
          {{(option.region ? option.region : '')}}<br v-if="option.details"/>
          {{(option.details ? option.details : '') }}</span>
      </div>
    </template>
    <template #no-options="{ search, searching, loading }">
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
      <!-- <template #option="{ author, title }">
        {{ title }}
         :get-option-label="(option) => option.title"
        <br />
        <cite>{{ author.firstName }} {{ author.lastName }}</cite>
      </template> -->
    </v-select>

      <!-- <multiselect
      v-model="$store.state[typeEn+'Item']" 
      label="name"
      :searchable="true"
      :options="$store.state[typeEn+'Data']"
      :loading="$store.state.selectDataLoading"
      :placeholder="'Заполните '+typeRu.toLowerCase()"
      @search-change="selectDataSearch"
      :disabled="typeEn == 'arrival' && !$store.state.dispatchItem"
      select-label=""
      deselect-label=""
      no-options=""
      no-result=""
      @Open="selectDataSearch"
      @Close="closeEvent"
      >
    <template #option="{ option }">
      <div style="cursor: pointer;">
        <span v-if="option.sourceId.includes('stations') && option.kladr" style="font-size: 15px;">{{ option.name}}<br v-if="option.kladr.region"/>
          {{(option.kladr.region ? option.kladr.region : '')}}<br v-if="option.kladr.district"/>
          {{(option.kladr.district ? option.kladr.district : '') }}</span>
        <span v-if="option.sourceId.includes('kladrs')" style="font-size: 18px;">{{ option.name}}<br v-if="option.region"/>
          {{(option.region ? option.region : '')}}<br v-if="option.district"/>
          {{(option.district ? option.district : '') }}</span>
        <span v-if="option.sourceId.includes('cache_arrival_points')" style="font-size: 13px;">{{ option.name}}<br v-if="option.region"/>
          {{(option.region ? option.region : '')}}<br v-if="option.details"/>
          {{(option.details ? option.details : '') }}</span>
      </div>
    </template>
    <template #singleLabel="{ option }">
      {{ option.name }}
    </template>
    <template style="height: 0;" #noResult>
      <span></span>
    </template>
    <template style="height: 0;" #noOptions>
      <span></span>
    </template>
      </multiselect> -->
      <!-- <el-select
          size="large"
          v-model="$store.state[typeEn+'Item']"
          filterable
          remote
          :default-first-option="true"
          :placeholder="'Заполните '+typeRu.toLowerCase()"
          :remote-method="selectDataSearch"
          :loading="$store.state.selectDataLoading"
          style="width: 100%; cursor: text;"
          loading-text="Поиск..."
          :disabled="typeEn == 'arrival' && !$store.state.dispatchItem"
          :value-key="'sourceId'"
          :label="$store.state[typeEn+'Item'] ? $store.state[typeEn+'Item'].name : ''"
          popper-append-to-body="false"
      >
          <el-option
              style="width: 100%;"
              v-for="option in $store.state[typeEn+'Data']"
              :key="option.sourceId"
              :label="option.name"
              :value="option"
          >

          <span v-if="option.sourceId.includes('stations') && option.kladr" style="font-size: 15px;">{{ option.name+(option.kladr.region ? ', '+option.kladr.region : '')+(option.kladr.district ? ', '+option.kladr.district : '') }}</span>
          <span v-if="option.sourceId.includes('kladrs')" style="font-size: 18px;">{{ option.name+(option.region ? ', '+option.region : '')+(option.district ? ', '+option.district : '') }}</span>
          <span v-if="option.sourceId.includes('cache_arrival_points')" style="font-size: 13px;">{{ option.name+(option.region ? ', '+option.region : '')+(option.details ? ', '+option.details : '') }}</span>
        </el-option>
      </el-select> -->
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