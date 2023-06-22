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
                    <div v-if="notExistingUserMessage" class="alert alert-danger" role="alert">
                        {{notExistingUserMessage}}
                    </div>
                      <label for="tel1" class="form-label label-gray">Телефон</label>
                      <input @focus="$emit('putRedFromLoginAway')" type="tel" v-model="user.phone" class="form-control phone__input" id="tel1" maxlength="17">
                      <div v-if="userErrors['phone']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{userErrors['phone'][0]}}
                      </div>
                    <button @click="sendCode" class="btn btn-primary btn-code" ><div>Сбросить пароль</div></button>
                    <div v-if="resetLoading" class="text-center" style="margin-top: 10px;">
                        <div class="spinner-border" role="status"></div>
                    </div>
                  </div>
                  <div class="code" v-else-if="stepLog==2">
                    <div v-if="wrongCodeMessage" class="alert alert-danger" role="alert">
                        {{ wrongCodeMessage }}
                    </div>
                      <p class="code__tel">Введите код, отправленный на номер <br><strong>{{ user.phone }}</strong></p>
                      <label for="tel" class="form-label label-gray">Код подтверждения</label>
                      <input type="text" class="form-control inp-gray"  id="code" v-model="code">
                      <button @click="confirmCode" class="btn btn-primary btn-code">Подтвердить</button>
                      <p v-if="countDown" style="font-size: 14px; margin-top: 8px;">Повторное СМС можно отправить через {{ countDown }} сек.</p>
                      <button v-else @click="sendCode" class="btn-code btn__new-code">Повторное СМС</button>
                      <div v-if="resetLoading" class="text-center" style="margin-top: 10px;">
                        <div class="spinner-border" role="status"></div>
                      </div>
                  </div>
                  <div class="code" v-else-if="stepLog==3">
                      <label for="tel" class="form-label label-gray">Новый пароль</label>
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
            notExistingUserMessage: '',
            userErrors: {},
            wrongCodeMessage: '',
            code: '',
            sendingCodeDisable: false,
            countDown: 0,
            sms: [],
            resetResponseStatus: null,
            successfulResetMessage: '',
            resetLoading: false,
        };
    },
    mounted(){
    //Маска
    [].forEach.call(document.querySelectorAll(".phone__input"), function (input) {
        var keyCode;
        function mask(event) {
        event.keyCode && (keyCode = event.keyCode);
        var pos = this.selectionStart;
        if (pos < 3) event.preventDefault();
        var matrix = "+7 (___) ___ ____",
            i = 0,
            def = matrix.replace(/\D/g, ""),
            val = this.value.replace(/\D/g, ""),
            new_value = matrix.replace(/[_\d]/g, function (a) {
            return i < val.length ? val.charAt(i++) || def.charAt(i) : a;
            });
        i = new_value.indexOf("_");
        if (i != -1) {
            i < 5 && (i = 3);
            new_value = new_value.slice(0, i);
        }
        var reg = matrix
            .substr(0, this.value.length)
            .replace(/_+/g, function (a) {
            return "\\d{1," + a.length + "}";
            })
            .replace(/[+()]/g, "\\$&");
        reg = new RegExp("^" + reg + "$");
        if (
            !reg.test(this.value) ||
            this.value.length < 5 ||
            (keyCode > 47 && keyCode < 58)
        )
            this.value = new_value;
        if (event.type == "blur" && this.value.length < 5) this.value = "";
        }
        input.addEventListener("input", mask, false);
        input.addEventListener("focus", mask, false);
        input.addEventListener("blur", mask, false);
        input.addEventListener("keydown", mask, false);
    });
    
    },
    methods: {
        async sendCode(){
            this.successfulResetMessage = ''
            this.resetLoading = true;
            this.userErrors = {};
            this.notExistingUserMessage = ''
            const promise = axiosClient
            .post('/sms/reset', {  phone: this.user.phone })
            .then(response => {
                this.sms = response.data.sms
                console.log(this.sms)
            })
            .catch(error => {
                if(error.response.status == 422){
                    this.notExistingUserMessage =  error.response.data.error
                }
            });
            await promise
            this.resetLoading = false;
            if(!this.notExistingUserMessage){
                this.stepLog=2
                this.sendingCodeDisable = true
                this.countDown = 10
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
            this.sms = []
            this.resetLoading = true;
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
        async reset(){
            this.resetLoading = true;
            this.userErrors = []
            this.successfulResetMessage = ''
            const promise = axiosClient
            .post('/reset', {
                phone: this.user.phone, 
                code: this.code, 
                password: this.user.password, 
                password_confirmation: this.user.password_confirmation, 
            })
            .then(response => {
                // this.resetResponseStatus = response.status
                localStorage.setItem('authToken', response.data.token)
                this.$emit('authSelf');
                this.$emit('authenticateForForm');
            })
            .catch(error => {
                if(error.response.status == 422){
                    this.userErrors = error.response.data.errors
                }
            })
            await promise
            this.resetLoading = false;
            // if(this.resetResponseStatus == 200){
            //     this.successfulResetMessage = 'Пароль успешно изменён!'
            //     this.stepLog=1
            // }            
        },
    }
};
</script>