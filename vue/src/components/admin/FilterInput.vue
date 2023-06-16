<template>
    <div class="text item" style="margin-bottom: 10px;">
        <div>{{title}}:</div>   
        <template v-if="!filterEl.set">
            <el-input
                v-model="filterEl.value"
                style="width: 120px; margin-right: 6px;"
                size="small"
            />
            <el-button type="primary" @click="$emit('setFilter', ind, filterEl.value)" :disabled="!filterEl.value" circle><i class="el-icon-check"></i></el-button>                                    
        </template>   
        <template v-else>
            <el-tag style="max-width: 100%;" size="large" closable @close="$emit('deleteFilter', ind)">{{filterEl.value.length < 15 ? filterEl.value : filterEl.value.slice(0,15)+'...'}}</el-tag>
        </template>
    </div>
  </template>
  <script>
    export default {
      emits: ['setFilter', 'deleteFilter'],
      props: ['title', 'ind', 'filterEl'],
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