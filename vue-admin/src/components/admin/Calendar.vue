<template>
    <div class="block">
      <el-config-provider :locale="locale">
        <el-date-picker
          v-model="selectedDate"
          type="date"
          style="max-width: 100%;"
        >
        </el-date-picker>
      </el-config-provider>
    </div>
  </template>
  <script>
    import ru from 'element-plus/dist/locale/ru.mjs'
    export default {
        props: {
            value: {
                type: Date,
                default: null
            }
        },
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
          locale: ru,
          selectedDate: this.value,
          val: ''
        };
      },
      watch: {
        value(newValue) {
            this.selectedDate = newValue;
        }
      }
    };
  </script>
  <style scoped>
      .block{
          max-width: 100%;
      }
  </style>