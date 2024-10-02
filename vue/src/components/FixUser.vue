<template>
      <div>
        <div class="row row-mobil">
              <div class="col-sm-6 possible-block">
                <div class="login-head"><h4>Подтверждение номера</h4></div>
                  <p class="possible">В личном кабинете вы можете:</p>
                  <ul class="all-possible">
                      <li class="possible-item">Посмотреть информацию о рейсе</li>
                      <li class="possible-item">Скачать купленный билет</li>
                      <li class="possible-item">Вернуть билет</li>
                      <li class="possible-item">Оставить отзыв о поездке</li>
                  </ul>
              </div>
              <div class="col-sm-6 block-form" >
                <div class="tel" v-if="step==1">
                    <div class="alert alert-danger" role="alert">
                        <strong style="font-size: 18px;">Убедитесь, что номер телефона указан верно. Для входа в личный кабинет на этот номер будет отправлен код подтверждения.</strong>
                    </div>
                    <div>
                      <label for="tel1" class="form-label label-gray">Телефон</label>
                      <input style="font-size: 24px;" @focus="phoneErrorMessage=''" ref="refPhoneInput" placeholder="+7 (___) ___ ____" v-mask="'+7 (9##) ### ####'" type="tel" v-model="unfixedUserData.phone" class="form-control phone__input" :class="{'is-invalid': phoneErrorMessage}" id="tel1" maxlength="17">
                      <div v-if="phoneErrorMessage" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{phoneErrorMessage}}
                      </div>  
                    </div>
                    <button @click="smsCodeSend" :disabled="loading" class="btn btn-primary btn-code" ><div>Всё верно</div></button>
                    <div class="block__check">
                    <label style="padding-left: 0; margin-top: 10px;" class="check">Я принимаю условия <a :href="baseUrl+'/agreement/offercontract.pdf'" target="_blank" style="color: var(--blue);">Пользовательского соглашения</a> и <a :href="baseUrl+'/agreement/privacypolicy.pdf'" target="_blank" style="color: var(--blue);">Политики конфиденциальности</a>
                        <!-- <input type="checkbox" v-model="user.formConditionTop"> -->
                        <!-- <span class="checkmark is-invalid"></span>
                        <div v-if="userErrors['formConditionTop']" class="invalid-feedback">{{ userErrors['formConditionTop'][0] }}</div> -->
                    </label>
                </div>
                    <div v-if="loading" class="text-center" style="margin-top: 10px;">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>

                <div class="code" v-else-if="step==2">
                    <div v-if="wrongCodeMessage" class="alert alert-danger" role="alert">
                      {{ wrongCodeMessage }}
                    </div>
                    <div v-else-if="phoneErrorMessage" class="alert alert-danger" role="alert">
                        {{ phoneErrorMessage }}
                    </div>                    
                    <p class="code__tel">Введите код, отправленный <strong v-if="this.whatsAppSent">НА WHATSAPP</strong><strong v-else>ПО СМС</strong> на номер <br><strong>{{ unfixedUserData.phone }}</strong></p>
                    <label for="tel" class="form-label label-gray">Код подтверждения</label>
                    <input type="text" class="form-control inp-gray"  id="code" v-model="code">
                    <button @click="smsCodeConfirm" class="btn btn-primary btn-code">Подтвердить</button>
                    <p v-if="countDown" style="font-size: 14px; margin-top: 8px;">Повторно код подтверждения можно отправить через {{ countDown }} сек.</p>
                    <div v-else class="">
                        <button v-if="this.whatsAppSent" @click="this.whatsAppChosen = true; smsCodeSend()" class="btn-code btn__new-code">Отправить код повторно на WhatsApp</button>
                        <button @click="this.whatsAppChosen = false; smsCodeSend()" class="btn-code btn__new-code">Отправить код повторно по СМС</button>
                    </div>

                    <div v-if="loading" class="text-center" style="margin-top: 10px;">
                      <div class="spinner-border" role="status"></div>
                    </div>
                </div>
              </div>
          </div>
      </div> 
</template>

<script>
import axiosClient from '../axios'
import router from '../router'

    export default{
        props: ['unfixedUserData'],
        emits: ['makeFixed'],
        data(){
            return{
                step: 1,
                sendingCodeDisable: true,
                countDown: 0,
                phoneErrorMessage: '',
                loading: false,
                code: '',
                wrongCodeMessage: '',
                whatsAppChosen: true,
                whatsAppSent: true
            }
        },
        methods: {
            async smsCodeSend(){
                this.phoneErrorMessage = ''
                this.wrongCodeMessage = ''
                this.loading = true
                const promise1 = axiosClient
                .get('/unfixed/user?bankOrderId='+this.unfixedUserData.bankOrderId)
                .then(response => {
                    console.log('need fixing')
                    // location.reload(); return false;
                })
                .catch(error => {
                    console.log(error)
                    if(error.response.status == 422){
                        console.log('its 422')
                        this.$emit('makeFixed')
                    }
                })
                await promise1
                const promise2 = axiosClient
                .post('/send/sms/auth/', { user: {phone: this.unfixedUserData.phone}, whatsAppChosen: this.whatsAppChosen })
                .then(response => {
                    this.whatsAppSent = response.data.whatsAppSent
                    console.log(response.data)
                    this.step = 2
                    if(this.whatsAppSent){
                        this.countDown = 30
                    }
                    else{
                        this.countDown = 120
                    }
                    console.log('start')
                    this.countDownTimer()
                })
                .catch(error => {
                    console.log(error)
                    console.log('end')
                    if(error.response.status == 422){
                        if(error.response.data.errors && error.response.data.errors.phone && error.response.data.errors.phone[0]){
                            this.phoneErrorMessage = error.response.data.errors.phone[0]
                        }
                        if(error.response.data.errorMessage){
                            this.phoneErrorMessage = error.response.data.errorMessage
                        }
                    }
                });
                await promise2
                this.loading = false
            },
            async smsCodeConfirm(){
                this.phoneErrorMessage = ''
                this.wrongCodeMessage = ''
                this.loading = true
                const promise = axiosClient
                .get('/fix/user/sms?code='+this.code+'&phone='+this.unfixedUserData.phone)
                .then(response => {
                    console.log(response)
                    this.fix()
                })
                .catch(error => {
                    console.log(error)
                    if(error.response.status == 422){
                        if(error.response.data.errorMessage){
                            this.wrongCodeMessage = error.response.data.errorMessage
                        }
                    }
                });
                await promise
                this.loading = false
            },
            async fix(){
                this.loading = true
                const promise = axiosClient
                .post('/fix/user/', {phone: this.unfixedUserData.phone, bankOrderId: this.unfixedUserData.bankOrderId})
                .then(response => {
                    console.log(response)
                    localStorage.setItem('authToken', response.data.token)
                    localStorage.removeItem('unfixedUser')
                    // location.reload();
                    // router.push({name: 'Account'})
                    window.location.replace(window.location.origin + '/account');
                })
                .catch(error => {
                    console.log(error)
                    this.loading = false
                });
                await promise
                
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
        },
        computed: {
            
        },
        mounted(){
            
        }
    }
</script>

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