<template>
  <BusLoading v-if="loading"/>
  <div v-else>
    <h2 style="margin-bottom: 20px;">Операции с бонусами</h2>
    <div v-if="bonuses.length != 0">
      
      <div style="padding: 15px; margin-bottom: 15px;" class="menu__ticket menu__ticket menu__ticket-bonuses">Мои бонусы: {{user.bonuses_balance}} руб</div>
      
      <table style="" class="table table-bordered">
      <thead>
        <tr>
          <th scope="col">Дата</th>
          <th scope="col">Бонусы</th>
          <!-- <th scope="col">Источник транзакции</th> -->
          
          <th scope="col">Описание</th>
          
        </tr>
      </thead>
      <tbody>
        <tr v-for="bonuse in bonuses">
          <td>{{ bonuse.date }} (по московскому времени)</td>
          <td v-if="bonuse.transaction == 'plus'"><span style="color: green;">+{{bonuse.amount}}</span></td>
          <td v-if="bonuse.transaction == 'minus'"><span style="color: red;">-{{bonuse.amount}}</span></td>        
          <td>{{ bonuse.descr }}</td>
          

          <!-- <td>{{ bonuse.amount }}</td> -->
          
          <!-- <td>{{ bonuse.order_id }}</td> -->
          
        </tr>
      </tbody>
      </table>
    </div>
    <div v-else class="not__found">
        <div class="not__found-img">
            <img src="../assets/free-icon-sad-3350122.png">
        </div>
        <div class="not__found-text">
            <p class="not__found-title">
                На вашем аккаунте не было транзакций с бонусами
            </p>
            <!-- <p class="not__found-descr">
                К сожалению, такого маршрута у нас нет. Мы делаем всё возможное, чтобы подключать как можно больше маршрутов. Возможно, он скоро появится.
            </p> -->
        </div>
    </div>    
  </div>

</template>
<script>
import axiosClient from '../axios';
import RaceCardAccount from '../components/RaceCardAccount.vue';
import BusLoading from '../components/BusLoading.vue';
import dayjs from 'dayjs';

export default
{
  components: { RaceCardAccount, BusLoading },
  data(){
      return{
        orders: [],
        loading: false,
        user: {},
        bonuses: []
      }
  },
  async mounted(){
    this.loading = true
    const promise1 = axiosClient
    .get('/user')
    .then(response => {
        this.user = response.data.user
    })
    await promise1

    const promise2 = axiosClient
    .get('/bonuses/user?id='+this.user.id)
    .then(response => {
        this.bonuses = response.data.bonuses.reverse()
    })
    .catch(error => {

    })
    await promise2
    this.bonuses.forEach(bonus => {
        bonus.date = dayjs(bonus.created_at).format('YYYY-MM-DD HH:mm:ss')
    })

    this.loading = false
  },
  methods: {

  }

};
</script>

<style scoped>

.not__found{
    box-shadow: rgb(0 0 0 / 15%) 0px 2px 17px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.not__found{
    display: flex;
    align-items: center;
    padding: 30px;
}
.not__found img{
    width: 200px;
}
.not__found-text{
    margin-left: 60px;
}
.not__found-title{
    font-size: 24px;
    margin-bottom: 20px;
}
.not__found-descr{
    font-size: 30px;
}
@media(max-width: 425px){
  .not__found{
    flex-direction: column;
  }
  .not__found-text{
    margin-left: 0;
    margin-top: 20px;
  }
}
</style>