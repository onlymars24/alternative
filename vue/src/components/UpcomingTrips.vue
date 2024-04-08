<template>
  <BusLoading v-if="loading"/>
  <div v-else-if="orders.length != 0" class="personal-account__content">
    <div style="padding: 15px; margin-bottom: 15px;" class="menu__ticket menu__ticket-bonuses">Мои бонусы: {{user.bonuses_balance}} руб</div>
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
        orders: [],
        loading: false,
        user: {}
      }
  },
  async mounted(){
    this.loading = true
    const promise = axiosClient
    .get('/user')
    .then(response => {
        this.user = response.data.user
    })
    await promise
	  this.updateOrders()
  },
  methods: {
    async updateOrders(){
      this.loading = true
      const promise1 = axiosClient
      .get('/orders')
      .then(response => {
        this.orders = response.data.orders
      })
      await promise1
      this.loading = false
    },
  }

};
</script>
<style>
  .menu__ticket-bonuses{
    width: 40%; 
  }


  @media (max-width: 425px){
    .menu__ticket-bonuses{
      width: 70%;
    }
  }
</style>