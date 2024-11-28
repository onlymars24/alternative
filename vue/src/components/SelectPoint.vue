<script>
import store from '../store'
export default
{
  props: [ 'typeRu', 'typeEn' ],
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
      <el-select
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
              v-for="item in $store.state[typeEn+'Data']"
              :key="item.sourceId"
              :label="item.name"
              :value="item"
          >

          <span v-if="item.sourceId.includes('stations') && item.kladr" style="font-size: 15px;">{{ item.name+(item.kladr.region ? ', '+item.kladr.region : '')+(item.kladr.district ? ', '+item.kladr.district : '') }}</span>
          <span v-if="item.sourceId.includes('kladrs')" style="font-size: 18px;">{{ item.name+(item.region ? ', '+item.region : '')+(item.district ? ', '+item.district : '') }}</span>
          <span v-if="item.sourceId.includes('cache_arrival_points')" style="font-size: 13px;">{{ item.name+(item.region ? ', '+item.region : '')+(item.details ? ', '+item.details : '') }}</span>
        </el-option>
      </el-select>
  </div>

</template>

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