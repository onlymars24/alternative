<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
<div class="background-close" @click="reloadPage(); $emit('CloseWindow'); $emit('CloseFeedbackWindow')"></div>
<div class="popup-container" :class="{'feedback-popup': this.content==7, 'rejection__popup': this.content==8 || 9}">
    <div v-if="this.content != 8 && this.content != 10 && this.content != 12" class="closeWindow" @click="reloadPage(); $emit('CloseWindow'); $emit('CloseFeedbackWindow')">✖</div>
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
          <li v-if="order.status != 'R'"><span><a href="" @click.prevent="$emit('returnOrder', order.id)">Вернуть весь заказ</a></span></li>
          <template v-for="ticket in order.tickets">
            <li v-if="ticket.status == 'S'"><span>Вернуть билет - <a href="" @click.prevent="$emit('returnTicket', ticket.id, order.id)">{{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}}</a></span></li>
            <li v-if="ticket.status == 'R'"><span>Билет {{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}} - возвращён</span></li>
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
      <div v-if="returnInfo.step==3">
        <div class="alert alert-danger" role="alert">
            Ошибка возврата! Попробуйте ещё раз или обратитесь в тех. поддержку!
        </div>
      </div> 
      <div>

      </div>
    </div>
    <div v-if="this.content==5" style="min-height: 200px;">
      <div v-if="returnTransactionsInfo.loading" class="loader__outside">
        <img src="../assets/bus_loading.png" style="max-width: 90%;">
        <p style="color: grey;">Загрузка.....</p>  
        <div class="loader"></div>
      </div>
      <div v-else>
        <h6>История операций</h6>
        <ul>
          <template v-for="transaction in returnTransactionsInfo.response">
            <p v-if="transaction.StatusCode != 2">Чек {{transaction.type == 'Income' ? ' платежа' : ' возврата'}} ещё не готов</p>
            <p v-if="transaction.StatusCode == 2"><a :href="transaction.OfdReceiptUrl" target="_blank">Чек</a>{{transaction.type == 'Income' ? ' платежа' : ' возврата'}}</p>
          </template>
        </ul>
      </div> 
      <div>

      </div>
    </div>
    <div v-if="this.content==6" style="min-height: 200px;">
      <div v-if="insurancesInfo.loading" class="loader__outside">
        <img src="../assets/bus_loading.png" style="max-width: 90%;">
        <p style="color: grey;">Загрузка.....</p>  
        <div class="loader"></div>
      </div>
      <div v-else>
        <h6>Список полисов</h6>
        <ul>
          <template v-for="ticket in insurancesInfo.response">
            <p v-if="ticket.status == 'S' && ticket.ticketType != 'Багажный'"><a :href="ticket.insurance.resources[0]" target="_blank">{{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}}</a></p>
            <p v-if="ticket.status == 'R' && ticket.ticketType != 'Багажный'">{{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}} - полис возвращён</p>
          </template>
        </ul>
      </div> 
    </div>
    <div v-if="this.content==7" style="min-height: 200px;">
      <Feedback/>
    </div>
    <div v-if="this.content==8">
      <div>
        <p style="font-size: 24px;">Страховка стоит всего {{insurancePrice}} руб и может оказаться полезной в непредвиденной ситуации</p>
        <div class="rejection__buttons" style="display: flex; justify-content: space-between; margin-top: 10px;">
          <button class="btn btn-outline-secondary btn-code" @click="$emit('confirmRejection')"><div style="font-size: 20px;">Беру все риски на себя</div></button>
          <button class="btn btn-primary btn-code" @click="$emit('changeMind')"><div style="font-size: 20px;">Оставить страховку</div></button>            
        </div> 
      </div> 
    </div>
    <div v-if="this.content==9">
      <div>
        <form @submit.prevent="emailEdit">
        <h4>Укажите email, на который будет отправляться билет</h4>
        <!-- {{ feedbackCredentials }} -->
        <ul style="margin-top: 15px;">
          <li>
            <!-- <label for="tel" class="form-label label-gray">Укажите свой email, на который будет приходить билет</label> -->
            <input v-model="emailEditObj.value" class="form-control inp-gray phone__input" placeholder="Ваш email" type="email" required>
          </li>
          <li>
            <button v-if="!emailEditObj.loading" type="submit" style="margin-top: 10px; color: #fff;" class="btn btn-primary btn-code">Отправить</button>
            <button v-else class="btn btn-primary btn-code" type="submit" disabled>
              <span style="margin-right: 10px;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <span class="sr-only">Загрузка...</span>
            </button>
          </li>
        </ul>
        </form>
      </div> 
    </div>
    <div v-if="this.content==10">
      <FixUser :unfixedUserData="unfixedUserData" @makeFixed="$emit('makeFixed')"/>
    </div>
    <div v-if="this.content==11">
      <div v-if="this.existingRaces.step == 1">
        <p style="font-size: 24px;">Найти ближайшие рейсы на другую дату?</p>
        <div class="rejection__buttons" style="display: flex; justify-content: space-between; margin-top: 10px;">
          <button class="btn btn-primary btn-code" @click="$emit('findOtherDates')"><div style="font-size: 20px;">Да</div></button>    
          <button class="btn btn-outline-secondary btn-code" @click="$emit('CloseWindow')"><div style="font-size: 20px;">Нет</div></button>
        </div> 
      </div>
      <div v-if="this.existingRaces.step == 2">
          <div v-if="this.existingRaces.loading" class="loader__outside">
            <img src="../assets/bus_loading.png" style="max-width: 90%;">
            <p style="color: grey;">Загрузка.....</p>  
            <div class="loader"></div>
          </div>
          <div v-else>
            <div v-if="!existingRaces.date">
              <p style="font-size: 24px;">На ближайшие 7 дней рейсы не найдены.</p>
            </div>
          </div>
      </div>
    </div>
    <div v-if="this.content==12">
      <div style="font-size: 23px;">
          <p style="margin-bottom: 15px;">
            <strong>Проверьте дату рождения!</strong>
          </p>
          <p style="margin-bottom: 15px;">Для детей до 12 лет оформляется билет со скидкой 50%. <br/>C 12 лет проезд по полной стоимости билета.</p>
          <p style="margin-bottom: 15px;">
            <strong>Возраст ребенка определяется на день начала поездки.</strong>
          </p>
          <div class="rejection__buttons" style="display: flex; justify-content: space-between; margin-top: 10px;">
            <button class="btn btn-primary btn-code" @click="$emit('CloseWindow')"><div style="font-size: 20px;">Ок</div></button>            
          </div> 
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
import FixUser from "../components/FixUser.vue";
import TheMask from 'vue-the-mask';
import Feedback from '../components/Feedback.vue';

export default
{
  data(){
    return{
      login: true,
      option: 'login',
      baseUrl: '',
      emailEditObj: {
        value: '',
        loading: false
      },
      // unfixedUserStep: 1
    }
  },
  props: ['content', 'user', 'order', 'returnInfo', 'returnTransactionsInfo', 'insurancesInfo', 'feedbackInfo', 'insurancePrice', 'unfixedUserData', 'existingRaces'],
  emits: ['makeFixed', 'confirmBook', 'authSelf', 'authenticateForForm', 'returnTicket', 'CloseWindow', 'returnOrder', 'CloseFeedbackWindow', 'confirmRejection', 'changeMind', 'editEmail', 'findOtherDates'],
  components: { Seat, Login, Registration, ResetPassword, FixUser, Feedback },
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
		},
    async sendMail(){
      this.feedbackInfo.loading = true
      this.feedbackInfo.step = 2
      const promise = axiosClient.post('/send/feedback', this.feedbackCredentials).then(response => {
        console.log(response)
      })
      .catch(error => {
        console.log(error)
      })
      await promise
      this.feedbackInfo.loading = false
    },
    async emailEdit(){
      this.emailEditObj.loading = true
      this.$emit('editEmail', this.emailEditObj.value)
      this.$emit('CloseWindow')
      this.emailEditObj.loading = false
    }
  },
  mounted(){
    // v-on:keydown.enter="reloadPage(); $emit('CloseWindow');"
    // this.$refs.refPhoneInput.focus()
    let that = this
    window.addEventListener('keyup', function (evt) {
        if (evt.keyCode === 27) {
          that.reloadPage(); 
          that.$emit('CloseWindow');
        }
    });
    this.baseUrl = import.meta.env.VITE_API_BASE_URL

  }
};
</script>
<style>
  .textarea__feedback {
    height: 200px;
  }
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
    position: absolute;
    right: 15px;
    top: 8px;
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
  .feedback-popup{
    padding: 20px;
  }  
  .rejection__popup{
    max-width: 720px;
  }
  .rejection__buttons{
  }  
  .rejection__buttons button{
    width: 49%;
  }
@media(max-width: 768px){
  .rejection__buttons{
    flex-direction: column;
  }  
  .rejection__buttons button{
    width: 100%;
    margin-top: 5px;
  }
}



</style>