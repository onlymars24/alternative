<template>
  <BusLoading v-if="loading"/>
  <div v-else class="personal-account__content">
    <template v-for="order in orders">
          <RaceCardAccount :order="order" @updateOrders="updateOrders"/>
    </template>
  </div>
</template>
<script>
import axiosClient from '../axios';
import RaceCardAccount from '../components/RaceCardAccount.vue';
import BusLoading from '../components/BusLoading.vue';

export default
{
  components: { RaceCardAccount, BusLoading },
  data(){
      return{
        orders: {},
        loading: false 
      }
  },
  async mounted(){
	  this.updateOrders()
  },
  methods: {
    async updateOrders(){
      this.loading = true
      const promise = axiosClient
      .get('/orders')
      .then(response => {
        this.orders = response.data.orders
      })
      await promise
      this.loading = false
    }
  }

};
</script>
