<style scoped>
.all-possible{
  padding-left: 0;
}
ul{
    list-style-type: none;
}
.bus-stop-decor {
  background-image: url("/public/img/bus-stop.svg");
  background-position: left bottom;
  background-size: contain;
  background-repeat: no-repeat;
  height: 100%;
  width: 50%;
  margin-left: 160px;
  transform: scale(1.2);
}
.ticket-decor {
  background-image: url("/public/img/ticket.svg");
  background-position: right bottom;
  background-size: contain;
  background-repeat: no-repeat;
  height: 100%;
  width: 50%;
  margin-right: 140px;
}
.decor {
  display: flex;
  position: absolute;
  opacity: 0.1;
  top: 65vh;
  width: 100vw;
  height: 30vh;
}
.btn__new-code {
  background-color: transparent;
}
.code__tel {
  font-size: 14px;
}
.login-head {
  display: flex;
}
.head-link {
  color: var(--blue);
  margin-top: 12px;
  cursor: pointer;
}
.form-label {
  margin-bottom: 0px;
}
.login-block {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  max-width: 800px;
  width: 100%;
  padding: 10px;
  z-index: 10;
  border-radius: 10px;
  padding: 30px 50px 50px 50px;
  box-shadow: rgb(0 0 0 / 10%) 0px 20px 90px 0px;
}
.approval {
  font-size: 14px;
}
.possible {
  color: var(--gray);
  margin: 0px;
}
.possible-item {
  position: relative;
  padding-left: 22px;
  margin: 0;
}

.go-back {
  position: relative;
  top: -60px;
  right: 40px;
}
/* .btn-code {
  width: 100%;
  padding: 12px;
} */
.possible-item::before {
  content: "";
  background-image: url("/public/img/check.svg");
  height: 20px;
  width: 20px;
  position: absolute;
  background-position: center center;
  display: block;
  background-size: contain;
  left: 0px;
}
@media (max-width: 1000px) {
  .decor {
    display: none;
  }
}
@media (max-width: 768px) {
  .possible-block {
    font-size: 13px;
  }
}
@media (max-width: 580px) {
  .row-mobil {
    flex-direction: column;
  }
  .possible-block
  {
    margin-bottom: 20px;
  }
}
</style>
<template>
    <div class="row row-mobil">
        <div class="col-sm-6 possible-block">
            <div class="login-head"><h2>Войти</h2></div>
            <!-- <a href="https://api.whatsapp.com/send?phone=79000000000" target="_blank" title="Написать в Whatsapp" rel="noopener noreferrer"><div class="whatsapp-button"><i class="fa fa-whatsapp"></i></div></a> -->
            <!-- <span class="head-link" @click="$emit('registrationSection')">Регистрация</span> -->
            <p class="possible">В личном кабинете вы можете:</p>
            <ul class="all-possible">
                <li class="possible-item">Выкупить забронированный билет</li>
                <li class="possible-item">Посмотреть информацию о рейсе</li>
                <li class="possible-item">Скачать купленный билет</li>
                <li class="possible-item">Вернуть билет</li>
                <li class="possible-item">Оставить отзыв о поездке</li>
            </ul>
        </div>
        <div class="col-sm-6 block-form" >
            <div v-if="stepLog==1" class="tel" >
                <div v-if="errorMessage" class="alert alert-danger" role="alert">
                    {{ errorMessage }}
                </div>
                <label for="tel" class="form-label label-gray">Телефон</label>
                <input ref="refPhoneInput" placeholder="+7 (___) ___ ____" v-mask="'+7 (9##) ### ####'" @focus="$emit('putRedFromLoginAway')" type="text" class="form-control inp-gray phone__input" :class="{'is-invalid': userErrors['phone']}" v-model="user.phone" maxlength="17" id="tel">
                <div v-if="userErrors['phone']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                    {{userErrors['phone'][0]}}
                </div>
                
                <button @click="this.whatsAppChosen = true; sendCode()" :disabled="loginLoading" class="btn btn-primary btn-code" style="background-color: #25D366; border: none;" >
                    <svg style="margin-right: 3px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                        width="23px" height="23px" viewBox="0 0 30.667 30.667" fill="white"
                        xml:space="preserve">
                        <g>
                            <path d="M30.667,14.939c0,8.25-6.74,14.938-15.056,14.938c-2.639,0-5.118-0.675-7.276-1.857L0,30.667l2.717-8.017
                                c-1.37-2.25-2.159-4.892-2.159-7.712C0.559,6.688,7.297,0,15.613,0C23.928,0.002,30.667,6.689,30.667,14.939z M15.61,2.382
                                c-6.979,0-12.656,5.634-12.656,12.56c0,2.748,0.896,5.292,2.411,7.362l-1.58,4.663l4.862-1.545c2,1.312,4.393,2.076,6.963,2.076
                                c6.979,0,12.658-5.633,12.658-12.559C28.27,8.016,22.59,2.382,15.61,2.382z M23.214,18.38c-0.094-0.151-0.34-0.243-0.708-0.427
                                c-0.367-0.184-2.184-1.069-2.521-1.189c-0.34-0.123-0.586-0.185-0.832,0.182c-0.243,0.367-0.951,1.191-1.168,1.437
                                c-0.215,0.245-0.43,0.276-0.799,0.095c-0.369-0.186-1.559-0.57-2.969-1.817c-1.097-0.972-1.838-2.169-2.052-2.536
                                c-0.217-0.366-0.022-0.564,0.161-0.746c0.165-0.165,0.369-0.428,0.554-0.643c0.185-0.213,0.246-0.364,0.369-0.609
                                c0.121-0.245,0.06-0.458-0.031-0.643c-0.092-0.184-0.829-1.984-1.138-2.717c-0.307-0.732-0.614-0.611-0.83-0.611
                                c-0.215,0-0.461-0.03-0.707-0.03S9.897,8.215,9.56,8.582s-1.291,1.252-1.291,3.054c0,1.804,1.321,3.543,1.506,3.787
                                c0.186,0.243,2.554,4.062,6.305,5.528c3.753,1.465,3.753,0.976,4.429,0.914c0.678-0.062,2.184-0.885,2.49-1.739
                                C23.307,19.268,23.307,18.533,23.214,18.38z"/>
                        </g>
                    </svg>   
                    Получить код по WhatsApp
                </button>
                <div class="">
                    <button :disabled="loginLoading" @click="this.whatsAppChosen = false; sendCode()" class="btn-code btn__new-code" style="font-size: 12px;">Отправить код по СМС</button>
                </div>
                <div class="block__check">
                    <label style="padding-left: 0;" class="check">Я принимаю условия <a :href="baseUrl+'/agreement/offercontract.pdf'" target="_blank" style="color: var(--blue);">Пользовательского соглашения</a> и <a :href="baseUrl+'/agreement/privacypolicy.pdf'" target="_blank" style="color: var(--blue);">Политики конфиденциальности</a>
                        <!-- <input type="checkbox" v-model="user.formConditionTop"> -->
                        <!-- <span class="checkmark is-invalid"></span>
                        <div v-if="userErrors['formConditionTop']" class="invalid-feedback">{{ userErrors['formConditionTop'][0] }}</div> -->
                    </label>
                </div>
                <div v-if="loginLoading" class="text-center" style="margin-top: 10px;">
                    <div class="spinner-border" role="status"></div>
                </div>
            </div>
            <div class="code" v-else="stepLog==2">
                    <div v-if="wrongCodeMessage" class="alert alert-danger" role="alert">
                        {{ wrongCodeMessage }}
                    </div>
                      <p class="code__tel">Введите код, отправленный <strong v-if="this.whatsAppSent">НА WHATSAPP</strong><strong v-else>ПО СМС</strong><br>на номер <strong>{{user.phone}}</strong></p>
                      <input type="text" class="form-control inp-gray"  id="code" v-model="code">
                      <button @click="confirmCode" class="btn btn-primary btn-code" style="background-color: #25D366; border: none; margin-bottom: 19px;">
                        <svg style="margin-right: 3px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                            width="23px" height="23px" viewBox="0 0 30.667 30.667" fill="white"
                            xml:space="preserve">
                            <g>
                                <path d="M30.667,14.939c0,8.25-6.74,14.938-15.056,14.938c-2.639,0-5.118-0.675-7.276-1.857L0,30.667l2.717-8.017
                                    c-1.37-2.25-2.159-4.892-2.159-7.712C0.559,6.688,7.297,0,15.613,0C23.928,0.002,30.667,6.689,30.667,14.939z M15.61,2.382
                                    c-6.979,0-12.656,5.634-12.656,12.56c0,2.748,0.896,5.292,2.411,7.362l-1.58,4.663l4.862-1.545c2,1.312,4.393,2.076,6.963,2.076
                                    c6.979,0,12.658-5.633,12.658-12.559C28.27,8.016,22.59,2.382,15.61,2.382z M23.214,18.38c-0.094-0.151-0.34-0.243-0.708-0.427
                                    c-0.367-0.184-2.184-1.069-2.521-1.189c-0.34-0.123-0.586-0.185-0.832,0.182c-0.243,0.367-0.951,1.191-1.168,1.437
                                    c-0.215,0.245-0.43,0.276-0.799,0.095c-0.369-0.186-1.559-0.57-2.969-1.817c-1.097-0.972-1.838-2.169-2.052-2.536
                                    c-0.217-0.366-0.022-0.564,0.161-0.746c0.165-0.165,0.369-0.428,0.554-0.643c0.185-0.213,0.246-0.364,0.369-0.609
                                    c0.121-0.245,0.06-0.458-0.031-0.643c-0.092-0.184-0.829-1.984-1.138-2.717c-0.307-0.732-0.614-0.611-0.83-0.611
                                    c-0.215,0-0.461-0.03-0.707-0.03S9.897,8.215,9.56,8.582s-1.291,1.252-1.291,3.054c0,1.804,1.321,3.543,1.506,3.787
                                    c0.186,0.243,2.554,4.062,6.305,5.528c3.753,1.465,3.753,0.976,4.429,0.914c0.678-0.062,2.184-0.885,2.49-1.739
                                    C23.307,19.268,23.307,18.533,23.214,18.38z"/>
                            </g>
                        </svg>                        
                        Подтвердить</button>
                      <p v-if="countDown" style="font-size: 14px; margin-top: 8px;">Повторно код подтверждения можно отправить через {{ countDown }} сек.</p>
                      <div v-else class="">
                        <button @click="this.whatsAppChosen = true; sendCode()" class="btn-code btn__new-code">Отправить код повторно на WhatsApp</button>
                        <button @click="this.whatsAppChosen = false; sendCode()" class="btn-code btn__new-code">Отправить код повторно по СМС</button>
                      </div>
                      <div v-if="loginLoading" class="text-center" style="margin-top: 10px;">
                        <div class="spinner-border" role="status"></div>
                      </div>
                  </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import router from '../router'
import axiosClient from '../axios'
import TheMask from 'vue-the-mask'

export default {
    emits: ["log", 'authenticateForForm', 'putRedFromLoginAway', 'authSelf', 'resetSection', 'registrationSection'], 
    data() {
        return {
            user: {
                phone: '',
            },
            loginLoading: false,
            userErrors: [],
            stepLog: 1,
            code: '',
            countDown: 10,
            whatsAppSent: false,
            errorMessage: '',
            wrongCodeMessage: ''
        };
    },
    mounted(){
        this.$refs.refPhoneInput.focus()
    },
    methods: {
        async sendCode(){
            this.loginLoading = true;
            this.errorMessage = ''
            this.userErrors = [];
            const promise = axiosClient
            .post('/send/sms/auth', {  user: this.user, whatsAppChosen: this.whatsAppChosen })
            .then(response => {
                this.whatsAppSent = response.data.whatsAppSent
                console.log(response)
            })
            .catch(error => {
                console.log(error)
                if(error.response.status == 422){
                    if(error.response.data.errorMessage){
                        this.errorMessage = error.response.data.errorMessage
                    }
                    else{
                        this.userErrors = error.response.data.errors
                    }
                }
            });
            await promise
            this.loginLoading = false;
            if(this.userErrors.length == 0 && !this.errorMessage){
                this.stepLog=2
                this.sendingCodeDisable = true
                if(this.whatsAppSent){
                    this.countDown = 30
                }
                else{
                    this.countDown = 120
                }
                
                this.countDownTimer()
            }
        },


        countDownTimer () {
            if (this.countDown > 0) {
                setTimeout(() => {
                    this.countDown -= 1
                    this.countDownTimer()
                }, 1000)
            }
            else{
                this.sendingCodeDisable = false
            }
        },
        async confirmCode(){
            this.loginLoading = true
            this.wrongCodeMessage = ''
            const promise = axiosClient
            .get('/confirm/sms/auth?phone='+this.user.phone+'&code='+this.code)
            .then(response => {
                localStorage.setItem('authToken', response.data.token)
                this.$emit('authSelf');
                this.$emit('authenticateForForm');
            })
            .catch(error => {
                console.log(error)
                if(error.response.status && error.response.status == 422 && error.response.data.error){
                    this.wrongCodeMessage = error.response.data.error
                }
            })
            await promise
            this.loginLoading = false
        }
    }
};
</script>