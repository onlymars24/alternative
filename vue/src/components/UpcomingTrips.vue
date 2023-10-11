<template>
  <BusLoading v-if="loading"/>
  <div v-else-if="orders.length != 0" class="personal-account__content">
    <template v-for="order in orders">
          <RaceCardAccount :order="order" @updateOrders="updateOrders"/>
    </template>
  </div>
  <div v-else class="not__found">
      <div class="not__found-img">
          <img src="../assets/free-icon-sad-3350122.png">
      </div>
      <div class="not__found-text">
          <p class="not__found-title">
              У вас нет заказов
          </p>
          <!-- <p class="not__found-descr">
              К сожалению, такого маршрута у нас нет. Мы делаем всё возможное, чтобы подключать как можно больше маршрутов. Возможно, он скоро появится.
          </p> -->
      </div>
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
      console.log(this.orders.length)
    }
  }

};
</script>
