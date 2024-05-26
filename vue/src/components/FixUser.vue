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
                      <input style="font-size: 24px;" @focus="phoneErrorMessage=''" ref="refPhoneInput" v-mask="'+7 (###) ### ####'" type="tel" v-model="unfixedUserData.phone" class="form-control phone__input" :class="{'is-invalid': phoneErrorMessage}" id="tel1" maxlength="17">
                      <div v-if="phoneErrorMessage" id="validationServer03Feedback" class="invalid-feedback" style="margin-bottom: 10px;">
                        {{phoneErrorMessage}}
                      </div>  
                    </div>
                    <button @click="smsCodeSend" :disabled="loading" class="btn btn-primary btn-code" ><div>Всё верно</div></button>
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
                    <p v-if="countDown" style="font-size: 14px; margin-top: 8px;">Повторное СМС можно отправить через {{ countDown }} сек.</p>
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
                .post('/fix/user/sms/', { phone: this.unfixedUserData.phone, bankOrderId: this.unfixedUserData.bankOrderId, whatsAppChosen: this.whatsAppChosen })
                .then(response => {
                    this.whatsAppSent = response.data.whatsAppSent
                    console.log(response.data)
                    this.step = 2
                    this.countDown = 120
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
                        if(error.response.data.error){
                            this.wrongCodeMessage = error.response.data.error
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