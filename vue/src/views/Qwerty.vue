<template>
    <multiselect v-model="value" :options="options"></multiselect>
</template>

<script>
import store from '../store'
import Multiselect from 'vue-multiselect'

export default {
  // OR register locally
  components: { Multiselect },
  data () {
    return {
      value: null,
      options: ['list', 'of', 'options']
    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>