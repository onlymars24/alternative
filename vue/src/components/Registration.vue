<template>
          <div class="row row-mobil">
              <div class="col-sm-6 possible-block">
                <div class="login-head"><h2>Регистрация</h2></div>
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
                    <!-- <pre>
                        {{ userErrors }}
                    </pre> -->
                        <div v-if="successfulRegisterMessage" class="alert alert-success" role="alert">
                            {{successfulRegisterMessage}}
                        </div>
                      <label for="tel1" class="form-label label-gray">Телефон</label>
                      <input @focus="$emit('putRedFromLoginAway')" type="tel" v-model="user.phone" class="form-control phone__input" :class="{'is-invalid': userErrors['phone']}" id="tel1" maxlength="17">
                      <div v-if="userErrors['phone']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{userErrors['phone'][0]}}
                      </div>
                      <label for="tel" class="form-label label-gray">Пароль</label>
                      <input @focus="$emit('putRedFromLoginAway')" type="password" v-model="user.password" class="form-control inp-gray" :class="{'is-invalid': userErrors['password']}" id="pas">
                      <div v-if="userErrors['password']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{userErrors['password'][0]}}
                      </div>
                      <label for="tel" class="form-label label-gray">Повторите пароль</label>
                      <input @focus="$emit('putRedFromLoginAway')" type="password" v-model="user.password_confirmation" class="form-control inp-gray" :class="{'is-invalid': userErrors['password_confirmation']}" id="pas_с">
                      <div v-if="userErrors['password_confirmation']" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{userErrors['password_confirmation'][0]}}
                      </div>
                      <div class="block__check">
                        <label @click="userErrors['formConditionTop'][0] = ''" class="check">Я принимаю условия <span class="blue__link">Пользовательского соглашения</span> (публичной оферты) и <span class="blue__link">политики конфиденциальности</span>
                            <input type="checkbox" v-model="user.formConditionTop">
                            <span class="checkmark is-invalid"></span>
                            <div v-if="userErrors['formConditionTop']" class="invalid-feedback">{{ userErrors['formConditionTop'][0] }}</div>
                        </label>
                        <label @click="userErrors['formConditionBottom'][0] = ''" class="check">Я даю <span class="blue__link">Cогласие на обработку персональных данных</span>
                            <input type="checkbox" v-model="user.formConditionBottom">
                            <span class="checkmark is-invalid"></span>
                            <div v-if="userErrors['formConditionBottom']" class="invalid-feedback">{{ userErrors['formConditionBottom'][0] }}</div>
                        </label>
                    </div>
                    <button @click="sendCode" class="btn btn-primary btn-code" ><div>Выслать код в СМС</div></button>
                    <div v-if="registerLoading" class="text-center" style="margin-top: 10px;">
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
                      <span class="approval">Вы предоставляете и подтверждаете <a href="">согласие на обработку персональных данных.</a> </span>
                      <button @click="confirmCode" class="btn btn-primary btn-code">Подтвердить</button>
                      <p v-if="countDown" style="font-size: 14px; margin-top: 8px;">Повторное СМС можно отправить через {{ countDown }} сек.</p>
                      <button v-else @click="sendCode" class="btn-code btn__new-code">Повторное СМС</button>
                      <div v-if="registerLoading" class="text-center" style="margin-top: 10px;">
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
    emits: ["log", 'putRedFromLoginAway', 'loginSection'], 
    data() {
        return {
            stepLog: 1,
            user: {
                phone: '',
                password: '',
                password_confirmation: '',
                formConditionTop: false,
                formConditionBottom: false,
            },
            userErrors: {},
            wrongCodeMessage: '',
            successfulRegisterMessage: '',
            code: '',
            sendingCodeDisable: false,
            countDown: 0,
            sms: [],
            resUser: [],
            registerLoading: false,
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
    // if(!Object.keys(this.userErrors).length){
    //     console.log(this.userErrors)
    // }
    
    },
    methods: {
        async sendCode(){
            this.registerLoading = true;
            this.successfulRegisterMessage = ''
            this.wrongCodeMessage = ''
            this.userErrors = {};
            const promise = axiosClient
            .post('/sms', { user: this.user, type: 'register' })
            .then(response => (
                this.sms = response.data
            ))
            .catch(error => {
                if(error.response.status == 422){
                    this.userErrors =  error.response.data.errors
                }
            });
            await promise
            console.log(this.sms)
            this.registerLoading = false;
            if(!Object.keys(this.userErrors).length){
                this.stepLog=2
                this.sendingCodeDisable = true
                this.countDown = 60
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
            this.registerLoading = true;
            const promise = axiosClient
            .get('/sms/'+this.user.phone+'?code='+this.code+'&type=register')
            .then(response => (
                this.sms = response.data
            ));
            await promise
            this.registerLoading = false;
            console.log(this.sms)
            if(this.sms){
                this.wrongCodeMessage = ''
                this.register()
            }
            else{
                this.wrongCodeMessage = 'Код подтверждения неверный!'
            }
        },
        async register(){
            const promise = axiosClient
            .post('/register', this.user)
            .then(response => {
                console.log(response)
                this.successfulRegisterMessage = 'Вы успешно зарегистрировались.'
                this.user.phone = ''
                this.user.password = ''
                this.user.password_confirmation = ''
                this.stepLog=1
            })
            .catch(error => {
                if(error.response.status == 422){
                    this.userErrors = error.response.data.errors
                }
            })
        }
    }
};
</script>