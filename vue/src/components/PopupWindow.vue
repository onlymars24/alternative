<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
<div class="background-close" @click="reloadPage(); $emit('CloseWindow'); $emit('CloseFeedbackWindow')"></div>
<div class="popup-container">
    <div class="closeWindow" @click="reloadPage(); $emit('CloseWindow'); $emit('CloseFeedbackWindow')">✖</div>
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
            <p v-if="ticket.status == 'S'"><a :href="ticket.insurance.resources[0]" target="_blank">{{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}}</a></p>
            <p v-if="ticket.status == 'R'">{{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}} - полис возвращён</p>
          </template>
        </ul>
      </div> 
    </div>
    <div v-if="this.content==7" style="min-height: 200px;">
      <div v-if="this.feedbackInfo.step==1">
        <form action="#">
        <h4>Задайте нам вопрос</h4>
        <!-- {{ feedbackCredentials }} -->
        <ul style="margin-top: 15px;">
          <li><label for="tel" class="form-label label-gray">Ваш телефон</label><input v-model="feedbackCredentials.phone" class="form-control inp-gray phone__input" required></li>
          <li><label for="tel" class="form-label label-gray">Ваш email</label><input v-model="feedbackCredentials.email" class="form-control inp-gray" required></li>
          <li>
            <label for="tel" class="form-label label-gray">Тема вопроса</label>
            <select 
              class="form-select form-control "
              maxlength="60"
              v-model="feedbackCredentials.topic" 
              required
            >
              <option value="" disabled selected hidden>Выберите тему</option>
              <option value="Проблема с поиском рейса">Проблема с поиском рейса</option>
              <option value="Проблема при покупке">Проблема при покупке</option>
              <option value="Проблема с возвратом билета">Проблема с возвратом билета</option>
              <option value="На сайте ошибка и что-то не работает">На сайте ошибка и что-то не работает</option>
              <option value="Другое">Другое</option>
            </select>
          </li>

          <li>
            <label for="tel" class="form-label label-gray">Описание</label>
            <textarea v-model="feedbackCredentials.descr" style="height: 200px;" cols="30" rows="40" resize="none" class="textarea__feedback form-control inp-gray"></textarea>
          </li>
          <li>
            <div class="block__check" style="padding-top: 4px;">
              <label @click="" class="check">Я принимаю условия <a :href="baseUrl+'/agreement/offercontract.pdf'" target="_blank" style="color: var(--blue);">Пользовательского соглашения</a> и <a :href="baseUrl+'/agreement/privacypolicy.pdf'" target="_blank" style="color: var(--blue);">Политики конфиденциальности</a>
                  <input required type="checkbox" v-model="feedbackCredentials.accepted">
                  <span class="checkmark is-invalid"></span>
              </label>
            </div>
          </li>          
          <li><button @click="sendMail" style="margin-top: 10px;" class="btn btn-primary btn-code">Отправить</button></li>
        </ul>
        </form>
      </div> 
      <div v-if="this.feedbackInfo.step==2">
          <div v-if="this.feedbackInfo.loading" class="loader__outside">
            <img src="../assets/bus_loading.png" style="max-width: 90%;">
            <p style="color: grey;">Загрузка.....</p>  
            <div class="loader"></div>
          </div>
          <h5 v-else>
            С вами свяжется оператор в течение нескольких часов!
          </h5>
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
      feedbackInfo: {
        step: 1,
        loading: false,
        response: [  ],
      },
      baseUrl: '',
      feedbackCredentials: {
        phone: '',
        email: '',
        descr: '',
        topic: '',
        accepted: false
      }
    }
  },
  props: ['content', 'user', 'order', 'returnInfo', 'returnTransactionsInfo', 'insurancesInfo', 'feedbackInfo'],
  emits: ['confirmBook', 'authSelf', 'authenticateForForm', 'returnTicket', 'CloseWindow', 'returnOrder', 'CloseFeedbackWindow'],
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
		},
    async sendMail(){
      this.feedbackInfo.loading = true
      this.feedbackInfo.step = 2
      const promise = axiosClient.post('/send/feedback', this.feedbackCredentials).then(response => {

      })
      .catch(error => {
        
      })
      await promise
      this.feedbackInfo.loading = false
    }
  },
  mounted(){
    // v-on:keydown.enter="reloadPage(); $emit('CloseWindow');"
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
</style>
