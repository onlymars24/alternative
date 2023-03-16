<template>
    <HeaderСrumbsVue />
    <div class="container">
      <div class="window-bus">
        <Seat />
      </div>
    </div>
  </template>
<script>
import Seat from '../components/Seat.vue';
import HeaderСrumbsVue from '../components/HeaderСrumbs.vue';

export default {
  components: { Seat, HeaderСrumbsVue },

};
</script>
