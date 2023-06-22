<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
<div class="background-close" @click=" reloadPage(); $emit('CloseWindow');"></div>
<div class="popup-container">
    <!-- <div class="closeWindow" @click="$emit('CloseWindow')">✖</div> -->
    <!-- <Seat v-if="this.content==1"></Seat> -->
    <div v-if="this.content==2">{{ UserAgreement }}</div>
    <div v-if="this.content==3" class="content-popap">
      <a class="go-back-popap" href="/">&#8592; Вернуться назад</a>
        <Login v-if="option == 'login'" @resetSection="option = 'reset'" @registrationSection="option = 'registration'" @authSelf="$emit('authSelf')" @authenticateForForm="$emit('authenticateForForm')" @log="login = false" />
        <Registration v-else-if="option == 'registration'" @loginSection="option = 'login'" @log="login = true" @authSelf="$emit('authSelf')" @authenticateForForm="$emit('authenticateForForm')"/>
        <ResetPassword v-else @loginSection="option = 'login'" @authSelf="$emit('authSelf')" @authenticateForForm="$emit('authenticateForForm')"/>
    </div>
    <div v-if="this.content==4" style="min-height: 200px;">
      <div v-if="returnInfo.step==1">
        <h6>Выберите билет, который хотите вернуть</h6>
        <ul>
          <template v-for="ticket in order.tickets">
            <li v-if="ticket.status == 'S'"><span>Вернуть билет - <a href="" @click.prevent="$emit('returnTicket', ticket.id, order.id)">Место {{ticket.seat}}</a></span></li>
            <li v-if="ticket.status == 'R'"><span>Билет Место {{ticket.seat}} возвращён</span></li>
          </template>
        </ul>
      </div> 
      <div v-if="returnInfo.step==2">
          <div v-if="returnInfo.loading" class="loader__outside">
            <img src="../assets/bus_loading.png" style="max-width: 90%;">
            <p style="color: grey;">Загрузка.....</p>  
            <div class="loader"></div>
          </div>
          <div v-else>
            Билет успешно возвращён! По ссылке на билет находится квитанция о возврате!
          </div>
      </div> 
      <div>

      </div>
    </div>
</div>
</template>
<script scoped>
import UserAgreement from '../data/UserAgreement';
import PhoneConfirmation from '../data/PhoneConfirmation';
import Seat from '../components/Seat.vue';
import axiosClient from '../axios'
import Login from "../components/Login.vue";
import Registration from "../components/Registration.vue";
import ResetPassword from "../components/ResetPassword.vue";

export default
{
  data(){
    return{
      login: true,
      option: 'login',
    }
  },
  props: ['content', 'user', 'order', 'returnInfo'],
  emits: ['confirmBook', 'authSelf', 'authenticateForForm', 'returnTicket', 'CloseWindow'],
  components: { Seat, Login, Registration, ResetPassword },
  computed: {
    UserAgreement() {
      return UserAgreement;
    },
  },
  methods: {
		reloadPage(){
			if(this.content==4 && this.returnInfo.step == 2){
				location.reload(); return false;
			}
		}
  }
};
</script>
<style>

.popup-container
{
    position: fixed;
    top: 50%;
     left: 50%;
    transform: translate(-50%, -50%);
    /* text-align: center; */
    background-color: white;
    max-width: 800px;
    width: 100%;
    padding: 40px;
    z-index: 10;
    border-radius: 20px;
    max-height: 80vh;
    overflow: auto;
}
.popup-container::-webkit-scrollbar {
  width: 0;
}
.closeWindow
{
    cursor: pointer;
    color: var(--blue);
    position: relative;
    text-align: end;
}
.background-close
{
    position: fixed;
    top: 0px;
    left: 0px;
    z-index: 9;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.521);
}
.content-popap
{
  position: relative;
}
.go-back-popap
{
  color: white;
  position: absolute;
  top: -80px;
}
.content-popap a{
  color: white;
}
</style>
