<template>
    <div class="text item" style="margin-bottom: 10px;">
        <div>{{title}}:</div>   
        <template v-if="!filterEl.set">
          <el-select v-model="filterEl.value" placeholder=" " @change="$emit('setSelectFilter', ind, filterEl.value, selectData)">
            <el-option
              v-for="item in selectData"
              :key="item.value"
              :label="item.label"
              :value="item.value"
              >
            </el-option>
          </el-select>
        </template>
        <template v-else>
            <el-tag style="max-width: 100%;" size="large" closable @close="$emit('deleteSelectFilter', ind)">{{filterEl.label.length < 15 ? filterEl.label : filterEl.label.slice(0,15)+'...'}}</el-tag>
        </template>
    </div>
  </template>
  <script>
    export default {
      emits: ['setSelectFilter', 'deleteSelectFilter'],
      props: ['title', 'ind', 'filterEl', 'selectData', 'data'],
      data() {
        return {
          value: '',
          set: false
        };
      },
      watch: {

      }
    };
  </script>
  <style scoped>
      .block{
          max-width: 100%;
      }
  </style>