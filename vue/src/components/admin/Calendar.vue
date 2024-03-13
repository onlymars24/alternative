<template>
  <div class="block">
    <el-config-provider :locale="locale">
      <el-date-picker
        v-model="value1"
        type="date"
        style="max-width: 100%;"
        @change="$emit('setDateFilter', filterName, value1)"
      >
      </el-date-picker>
    </el-config-provider>
  </div>
</template>
<script>
import ru from 'element-plus/dist/locale/ru.mjs'
  export default {
    emits: ['setDateFilter'],
    props: ['filterName'],
    data() {
      return {
        pickerOptions: {
          disabledDate(time) {
            return time.getTime() > Date.now();
          },
          shortcuts: [{
            text: 'Today',
            onClick(picker) {
              picker.$emit('pick', new Date());
            }
          }, {
            text: 'Yesterday',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() - 3600 * 1000 * 24);
              picker.$emit('pick', date);
            }
          }, {
            text: 'A week ago',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', date);
            }
          }]
        },
        value1: '',
        locale: ru
      };
    },
    watch: {
        // value1(newValue){
        //     this.$emit('setBirthday', newValue)
        // }
    }
  };
</script>
<style scoped>
    .block{
        max-width: 100%;
    }
</style>