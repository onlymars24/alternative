<template>
        <div class="personal-account__content">
		<template v-for="order in orders">
        	<RaceCardAccount :order="order"/>
		</template>
        <!-- <div class="personal-account__content-empty">
        <h2>У вас пока нет предстоящих поездок</h2>
        <p>Чтобы найти билеты на автобус выберите направление,
             дату поездки и количество пассажиров</p>
        </div> -->
        </div>
</template>
<script>
import axiosClient from '../axios';
import RaceCardAccount from '../components/RaceCardAccount.vue';

export default
{
  components: { RaceCardAccount },
  data(){
      return{
			orders: {},
			loading: false 
      }
  },
  async mounted(){
	const promise = axiosClient
	.get('/orders')
	.then(response => {
		this.orders = response.data.orders
		console.log(this.orders)
	})
	await promise
  }

};
</script>
