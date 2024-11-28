<script>
import store from '../store'
import Multiselect from 'vue-multiselect'
export default
{
  props: [ 'typeRu', 'typeEn' ],
  components: { Multiselect },
  data(){
    return {

    }
  },
  methods: {
    selectDataSearch(query){
        console.log(this.typeEn)
        store.dispatch('selectDataSearch', {query, selectItemType: this.typeEn})
    }
  }
}
</script>

<template>
    <div class="main__table-table">
      <p style="margin: 0; padding-left: 12px; padding-top: 12px; padding-bottom: 5px; font-size: 12px;">{{ typeRu }}</p>
      <multiselect 
      v-model="$store.state[typeEn+'Item']" 
      label="name"
      :searchable="true"
      :options="$store.state[typeEn+'Data']"
      :loading="$store.state.selectDataLoading"
      :placeholder="'Заполните '+typeRu.toLowerCase()"
      @search-change="selectDataSearch"
      :disabled="typeEn == 'arrival' && !$store.state.dispatchItem"
      selectLabel=""
      deselectLabel=""
      >
    <!-- Слот для отображения каждого элемента в списке -->
    <template #option="{ option }">
      <div>
        <span v-if="option.sourceId.includes('stations') && option.kladr" style="font-size: 15px;">{{ option.name+(option.kladr.region ? ', '+option.kladr.region : '')+(option.kladr.district ? ', '+option.kladr.district : '') }}</span>
          <span v-if="option.sourceId.includes('kladrs')" style="font-size: 18px;">{{ option.name+(option.region ? ', '+option.region : '')+(option.district ? ', '+option.district : '') }}</span>
          <span v-if="option.sourceId.includes('cache_arrival_points')" style="font-size: 13px;">{{ option.name+(option.region ? ', '+option.region : '')+(option.details ? ', '+option.details : '') }}</span>
      </div>
    </template>

    <!-- Слот для отображения выбранного элемента -->
    <template #singleLabel="{ option }">
      {{ option.name }}
    </template>
      </multiselect>
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