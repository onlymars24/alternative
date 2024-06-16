<style scoped>
.whatsapp-button {
       background: #25D366;
       border: 3px solid #1cc15a;
       border-radius: 50%;
       box-shadow: 0 8px 10px rgba(7, 206, 112, 0.6);
       cursor: pointer;
       height: 68px;
       text-align: center;
       width: 68px;
       position: fixed;
       right: 5%;
       bottom: 8%;
       padding: 0px !important;
       z-index: 9999;
       transition: .3s;
       -webkit-animation: hoverWave linear 1s infinite;
       animation: hoverWave linear 1s infinite;
}
  
.whatsapp-button .text-button {
        height:68px;
        width:68px;        
        border-radius:50%;
        position:relative;
        overflow:hidden;
}
.whatsapp-button .text-button span {
    text-align: center;
    color:#23a455;
    opacity: 0;
    font-size: 0;
        position:absolute;
        right: 8px;
        top: 27px;
    line-height: 14px;
        font-weight: 600;
    transition: opacity .3s linear;
    font-family: 'montserrat', Arial, Helvetica, sans-serif;
}
.whatsapp-button .text-button:hover span {
        opacity: 1;
        font-size: 11px;
}
.whatsapp-button:hover i {
        display:none;
        color:#25D366;
        font-size:44px;
        transition:.3s;
}
.whatsapp-button:hover {
        z-index:1;
        background:#fff;
        color:transparent;
        transition:.3s;
}

.whatsapp-button i {
        color:#fff;
        font-size:44px;
        transition:.3s;
        line-height: 66px;transition: .5s ease-in-out;
        animation: 1200ms ease 0s normal none 1 running shake;
        animation-iteration-count: infinite;
        -webkit-animation: 1200ms ease 0s normal none 1 running shake;
        -webkit-animation-iteration-count: infinite;
}
@-webkit-keyframes hoverWave {
        0% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 0 rgba(7,206,112,0.2),0 0 0 0 rgba(7,206,112,0.2)
}
40% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 15px rgba(7,206,112,0.2),0 0 0 0 rgba(7,206,112,0.2)
}
80% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 30px rgba(7,206,112,0),0 0 0 26.7px rgba(7,206,112,0.067)
}
100% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 30px rgba(7,206,112,0),0 0 0 40px rgba(7,206,112,0.0)
}
}@keyframes hoverWave {
        0% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 0 rgba(7,206,112,0.2),0 0 0 0 rgba(7,206,112,0.2)
}
40% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 15px rgba(7,206,112,0.2),0 0 0 0 rgba(7,206,112,0.2)
}
80% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 30px rgba(7,206,112,0),0 0 0 26.7px rgba(7,206,112,0.067)
}
100% {
        box-shadow:0 8px 10px rgba(7,206,112,0.3),0 0 0 30px rgba(7,206,112,0),0 0 0 40px rgba(7,206,112,0.0)
}
}
@keyframes shake {
0% {
    transform: rotateZ(0deg);
        -ms-transform: rotateZ(0deg);
        -webkit-transform: rotateZ(0deg);
}
10% {
    transform: rotateZ(-30deg);
        -ms-transform: rotateZ(-30deg);
        -webkit-transform: rotateZ(-30deg);
}
20% {
    transform: rotateZ(15deg);
        -ms-transform: rotateZ(15deg);
        -webkit-transform: rotateZ(15deg);
}
30% {
    transform: rotateZ(-10deg);
        -ms-transform: rotateZ(-10deg);
        -webkit-transform: rotateZ(-10deg);
}
40% {
    transform: rotateZ(7.5deg);
        -ms-transform: rotateZ(7.5deg);
        -webkit-transform: rotateZ(7.5deg);
}
50% {
    transform: rotateZ(-6deg);
        -ms-transform: rotateZ(-6deg);
        -webkit-transform: rotateZ(-6deg);
}
60% {
    transform: rotateZ(5deg);
        -ms-transform: rotateZ(5deg);
        -webkit-transform: rotateZ(5deg);
}
70% {
    transform: rotateZ(-4.28571deg);
        -ms-transform: rotateZ(-4.28571deg);
        -webkit-transform: rotateZ(-4.28571deg);
}
80% {
    transform: rotateZ(3.75deg);
        -ms-transform: rotateZ(3.75deg);
        -webkit-transform: rotateZ(3.75deg);
}
90% {
    transform: rotateZ(-3.33333deg);
        -ms-transform: rotateZ(-3.33333deg);
        -webkit-transform: rotateZ(-3.33333deg);
}
100% {
    transform: rotateZ(0deg);
        -ms-transform: rotateZ(0deg);
        -webkit-transform: rotateZ(0deg);
}
}
@-webkit-keyframes shake {
0% {
    transform: rotateZ(0deg);
        -ms-transform: rotateZ(0deg);
        -webkit-transform: rotateZ(0deg);
}
10% {
    transform: rotateZ(-30deg);
        -ms-transform: rotateZ(-30deg);
        -webkit-transform: rotateZ(-30deg);
}
20% {
    transform: rotateZ(15deg);
        -ms-transform: rotateZ(15deg);
        -webkit-transform: rotateZ(15deg);
}
30% {
    transform: rotateZ(-10deg);
        -ms-transform: rotateZ(-10deg);
        -webkit-transform: rotateZ(-10deg);
}
40% {
    transform: rotateZ(7.5deg);
        -ms-transform: rotateZ(7.5deg);
        -webkit-transform: rotateZ(7.5deg);
}
50% {
    transform: rotateZ(-6deg);
        -ms-transform: rotateZ(-6deg);
        -webkit-transform: rotateZ(-6deg);
}
60% {
    transform: rotateZ(5deg);
        -ms-transform: rotateZ(5deg);
        -webkit-transform: rotateZ(5deg);
}
70% {
    transform: rotateZ(-4.28571deg);
        -ms-transform: rotateZ(-4.28571deg);
        -webkit-transform: rotateZ(-4.28571deg);
}
80% {
    transform: rotateZ(3.75deg);
        -ms-transform: rotateZ(3.75deg);
        -webkit-transform: rotateZ(3.75deg);
}
90% {
    transform: rotateZ(-3.33333deg);
        -ms-transform: rotateZ(-3.33333deg);
        -webkit-transform: rotateZ(-3.33333deg);
}
100% {
    transform: rotateZ(0deg);
        -ms-transform: rotateZ(0deg);
        -webkit-transform: rotateZ(0deg);
}
}
</style>

<template>
          <div class="row row-mobil">
              <div class="col-sm-6 possible-block">
                <div class="login-head"><h2>Сброс пароля</h2></div>
                <span class="head-link" @click="$emit('loginSection')">Войти</span>
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
                  <div class="tel" v-if="stepLog==1">
                    <div v-if="successfulResetMessage" class="alert alert-success" role="alert">
                        {{successfulResetMessage}}
                    </div>
                    <div v-if="userErrors['phone']" class="alert alert-danger" role="alert">
                        {{userErrors['phone'][0]}} <strong style="font-size: 18px;">{{ notExistingUser ?  user['phone'] : ''}}</strong>
                    </div>
                      <div v-show="!notExistingUser">
                        <!-- <input type="tel" v-mask="'+7 (###) ###-####'" class="form-control" id="tel2" maxlength="17"> -->


                        <label for="tel1" class="form-label label-gray">Телефон</label>
                        <input ref="refPhoneInput" v-mask="'+7 (###) ### ####'" @focus="$emit('putRedFromLoginAway')" type="tel" v-model="user.phone" class="form-control phone__input" id="tel1" maxlength="17">
                        <div v-if="userErrors['phone']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                            {{userErrors['phone'][0]}}
                        </div>
                      </div>
                      
                    <button v-if="!notExistingUser" @click="sendCode" class="btn btn-primary btn-code" ><div>Сбросить пароль</div></button>
                    <button v-if="notExistingUser" @click="hiddenRegister" class="btn btn-primary btn-code" ><div>Всё верно</div></button>
                    <button v-if="notExistingUser" @click="changeNumber" class="btn btn-outline-secondary btn-code" style="margin-top: 5px;"><div>Исправить</div></button>
                    
                    <div v-if="resetLoading" class="text-center" style="margin-top: 10px;">
                        <div class="spinner-border" role="status"></div>
                    </div>
                  </div>
                  <div class="code" v-else-if="stepLog==2">
                    <div v-if="wrongCodeMessage" class="alert alert-danger" role="alert">
                        {{ wrongCodeMessage }}
                    </div>
                    <a href="https://api.whatsapp.com/send?phone=79260000000" target="_blank" rel="noopener noreferrer"><div type="button" class="whatsapp-button"><div class="text-button"><i class="fa fa-whatsapp"></i><span>WhatsApp</span></div></div></a>
                      <p class="code__tel">Введите код, отправленный <strong v-if="this.whatsAppSent">НА WHATSAPP</strong><strong v-else>ПО СМС</strong> на номер <br><strong>{{ user.phone }}</strong></p>
                      <label for="tel" class="form-label label-gray">Код подтверждения</label>
                      <input type="text" class="form-control inp-gray"  id="code" v-model="code">
                      <button @click="confirmCode" class="btn btn-primary btn-code">Подтвердить</button>
                      <p v-if="countDown" style="font-size: 14px; margin-top: 8px;">Повторное СМС можно отправить через {{ countDown }} сек.</p>
                      <div v-else class="">
                        <button v-if="this.whatsAppSent" @click="this.whatsAppChosen = true; sendCode()" class="btn-code btn__new-code">Отправить код повторно на WhatsApp</button>
                        <button @click="this.whatsAppChosen = false; sendCode()" class="btn-code btn__new-code">Отправить код повторно по СМС</button>
                      </div>
                      <div v-if="resetLoading" class="text-center" style="margin-top: 10px;">
                        <div class="spinner-border" role="status"></div>
                      </div>
                  </div>
                  <div class="code" v-else-if="stepLog==3">
                      <input type="hidden" name="phone" :value="user.phone">
                      <label for="tel" class="form-label label-gray">Новый пароль</label><input style="max-width: 2px;" type="text" name="phone" :value="user.phone">
                      <input @focus="$emit('putRedFromLoginAway')" type="password" v-model="user.password" class="form-control inp-gray" :class="{'is-invalid': userErrors['password']}" id="pas">
                      <div v-if="userErrors['password']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{userErrors['password'][0]}}
                      </div>
                      <label for="tel" class="form-label label-gray">Повторите пароль</label>
                      <input @focus="$emit('putRedFromLoginAway')" type="password" v-model="user.password_confirmation" class="form-control inp-gray" :class="{'is-invalid': userErrors['password_confirmation']}" id="pas_с">
                      <div v-if="userErrors['password_confirmation']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{userErrors['password_confirmation'][0]}}
                      </div>
                      <button @click="reset" class="btn btn-primary btn-code">Подтвердить</button>
                      <div v-if="resetLoading" class="text-center" style="margin-top: 10px;">
                        <div class="spinner-border" role="status"></div>
                      </div>
                  </div>
                </div>
          </div>
</template>
<script>
import axios from 'axios'
import axiosClient from '../axios'
import TheMask from 'vue-the-mask'

export default {
    emits: ["log", 'putRedFromLoginAway', 'loginSection', 'authSelf', 'authenticateForForm'],
    data() {
        return {
            stepLog: 1,
            user: {
                phone: '',
                password: '',
                password_confirmation: ''
            },
            errorMessage: '',
            userErrors: [],
            wrongCodeMessage: '',
            code: '',
            sendingCodeDisable: false,
            countDown: 0,
            sms: [],
            resetResponseStatus: null,
            successfulResetMessage: '',
            resetLoading: false,
            notExistingUser: false,
            whatsAppChosen: true,
            whatsAppSent: true
        };
    },
    mounted(){
        this.$refs.refPhoneInput.focus()
    },
    methods: {
        randomString(length){
            let result = '';
            let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return result
        },
        async sendCode(){
            this.successfulResetMessage = ''
            this.resetLoading = true;
            this.userErrors = [];
            this.errorMessage = ''
            const promise = axiosClient
            .post('/sms/reset', {  phone: this.user.phone, whatsAppChosen: this.whatsAppChosen })
            .then(response => {
                this.whatsAppSent = response.data.whatsAppSent
                console.log(response)
            })
            .catch(error => {
                console.log(error)
                if(error.response.status == 422){
                    this.userErrors =  error.response.data.errors
                    if(this.userErrors['phone'] && this.userErrors['phone'][0] && this.userErrors['phone'][0] == 'Проверьте правильность ввода номера телефона:'){
                        this.notExistingUser = true
                    }
                }
            });
            await promise
            this.resetLoading = false;
            if(this.userErrors.length == 0){
                this.stepLog=2
                this.sendingCodeDisable = true
                this.countDown = 120
                this.countDownTimer()
            }
        },
        countDownTimer(){
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
            this.sms = []
            this.resetLoading = true;
            this.userErrors = [];
            this.wrongCodeMessage = ''
            const promise = axiosClient
            .get('/sms/reset?code='+this.code+'&phone='+this.user.phone)
            .then(response => {
                this.sms = response.data
            })
            .catch(error => {
                if(error.response.status == 422){
                    this.wrongCodeMessage = error.response.data.error
                }
            })
            await promise
            this.resetLoading = false;
            
            if(!this.wrongCodeMessage){
                this.stepLog=3
            }
        },
        changeNumber(){
            this.notExistingUser = false
            // this.$refs.refPhoneInput.focus()
            this.user.phone = ''
            this.userErrors = []
            
        },
        async hiddenRegister(){
            let tempPassword = this.randomString(15)
            this.userErrors = []
            this.resetLoading = true
            const promise = axiosClient
            .post('/register', {  phone: this.user.phone, password: tempPassword, password_confirmation: tempPassword })
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
                if(error.response.status == 422){
                    this.userErrors = error.response.data.errors
                }
            })
            await promise
            this.resetLoading = false
            if(this.userErrors.length == 0){
                // console.log('Нет ошибок')
                this.sendCode()
            }
        },
        async reset(){
            console.log('done0')
            this.resetLoading = true;
            this.userErrors = []
            this.successfulResetMessage = ''
            console.log('done1')
            const promise = axiosClient
            .post('/reset', {
                phone: this.user.phone, 
                password: this.user.password, 
                password_confirmation: this.user.password_confirmation, 
            })
            .then(response => {
                console.log('done2')
                localStorage.setItem('authToken', response.data.token)
                this.$emit('authSelf');
                this.$emit('authenticateForForm');
            })
            .catch(error => {
                console.log(error)
                if(error.response.status == 422){
                    this.userErrors = error.response.data.errors
                }
            })
            await promise
            this.resetLoading = false;
        },
    }
};
</script>