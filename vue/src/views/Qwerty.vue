<template>
    <multiselect v-model="value" :options="options"></multiselect>

    <v-select :options="[{label: 'Canada', code: 'ca'}]"></v-select>
</template>

<script>
import store from '../store'
import Multiselect from 'vue-multiselect'
import vSelect from 'vue-select'

export default {
  // OR register locally
  components: { Multiselect, 'v-select': vSelect },
  data () {
    return {
      value: null,
      options: ['list', 'of', 'options']
    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>
@import 'vue-select/dist/vue-select.css';

</style>