<template>
  <BusLoading v-if="loading"/>
  <div v-else>
    <h2>Список моих заказов</h2>
    <div style="padding: 15px; margin-bottom: 15px;" class="menu__ticket menu__ticket-bonuses">Мой баланс: {{user.bonuses_balance}} руб</div>
    <div v-if="content" style="padding: 15px; margin-bottom: 15px;">
      <div v-html="content"></div>
      <div style="margin-top: 10px;" v-html="vkShare"></div>
    </div>    
    <div v-if="orders.length != 0" class="personal-account__content">
      <template v-for="order in orders">
            <RaceCardAccount :order="order" @updateOrders="updateOrders"/>
      </template>
    </div>
    <div v-else class="not__found">
        <!-- DONT YOU FORGET -->
        <div class="not__found-img">
            <img src="../assets/free-icon-sad-3350122.png">
        </div>
        <div class="not__found-text">
            <p class="not__found-title">
                У вас нет заказов
            </p>
        </div>
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
        user: {},
        content: ''
      }
  },
  async mounted(){
    this.loading = true
    // document.getElementById('vk_share_button').innerHTML = 'Smth new'
    // document.getElementById('vk_share_button').innerHTML = VK.Share.button('http://mysite.com', {type: 'link'});
    let authToken = localStorage.getItem('authToken')
    if(authToken){
      const promise1 = axiosClient
      .get('/page/upcoming/trips')
      .then(response => {
          console.log(response)
          this.content = response.data.pageUpcomingTrips
      })
      .catch(error => {
          console.log(error)
      })
      await promise1
      const promise2 = axiosClient
      .get('/user')
      .then(response => {
          this.user = response.data.user
      })
      await promise2
      this.updateOrders()
    }
	  
  },
  computed: {
    vkShare(){
      console.log('vkShare')
      // console.log(document.write(VK.Share.button({url: 'http://mysite.com', title: 'Заголовок страницы'}, {type: 'custom', text: '<img src="http://vk.com/images/vk32.png" />'})))
      // return VK.Share.button({url: 'http://mysite.com', title: 'Заголовок страницы'}, {type: 'custom', text: '<img src="http://vk.com/images/vk32.png" />'})
      return VK.Share.button({url: 'https://xn--80adplhnbnk0i.xn--p1ai', title: 'Недорогие билеты на автобус', image: 'https://api.xn--80adplhnbnk0i.xn--p1ai/img/vk_bus_mail.png',}, 
      {type: "custom", 
      text: '<button style="padding: 9px 12px; display: flex; align-items: center; border-radius: 5px; color: #fff; background-color: #4C75A3;"><img style="width: 27px; margin-right: 7px;" src="/img/vk_logo_new.png" alt=""><span style="font-size: 18px;">Поделиться</span></button>'
    }); 
    }
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