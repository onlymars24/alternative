<script>
import axiosClient from '../axios'

export default
{
  data(){
    return{
      feedbackInfo: {
        step: 1,
        loading: false,
        response: [  ],
      },
      feedbackCredentials: {
        name: '',
        phone: '',
        email: '',
        descr: '',
        topic: '',
        accepted: false
      },
      // unfixedUserStep: 1
    }
  },
  methods: {
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
  },
  mounted(){

  }
};

</script>

<template>
  <div v-if="this.feedbackInfo.step==1">
    <form @submit.prevent="sendMail">
    <h4>Задайте нам вопрос</h4>
    <!-- {{ feedbackCredentials }} -->
    <ul style="margin-top: 15px; padding-left: 0; list-style: none;" class="feedback__list">
      <li>
        <!-- <label for="tel" class="form-label label-gray">Ваше имя</label> -->
        <input v-model="feedbackCredentials.name" class="form-control inp-gray phone__input" placeholder="Ваше имя" required></li>
      <li>
        <!-- <label for="tel" class="form-label label-gray">Ваш телефон</label> -->
        <input v-mask="'+7 (9##) ### ####'" ref="refPhoneInput" v-model="feedbackCredentials.phone" class="form-control inp-gray phone__input" placeholder="Ваш телефон" required></li>
      <li>
        <!-- <label for="tel" class="form-label label-gray">Ваш email</label> -->
        <input v-model="feedbackCredentials.email" class="form-control inp-gray" placeholder="Ваш email" required></li>
      <li>
        <!-- <label for="tel" class="form-label label-gray">Тема вопроса</label> -->
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
        <!-- <label for="tel" class="form-label label-gray">Описание</label> -->
        <textarea v-model="feedbackCredentials.descr" style="height: 90px;" cols="30" rows="40" resize="none" class="textarea__feedback form-control inp-gray" placeholder="Описание"></textarea>
      </li>
      <li>
        <div class="block__check" style="padding-top: 4px;">
          <label @click="" class="check">Я принимаю условия <a :href="baseUrl+'/agreement/offercontract.pdf'" target="_blank" style="color: var(--blue);">Пользовательского соглашения</a> и <a :href="baseUrl+'/agreement/privacypolicy.pdf'" target="_blank" style="color: var(--blue);">Политики конфиденциальности</a>
              <input required type="checkbox" v-model="feedbackCredentials.accepted">
              <span class="checkmark is-invalid"></span>
          </label>
        </div>
      </li>          
      <li><button type="submit" style="margin-top: 10px;" class="btn btn-primary btn-code">Отправить</button></li>
    </ul>
    </form>
  </div> 
  <div v-if="this.feedbackInfo.step==2">
      <div v-if="this.feedbackInfo.loading" class="loader__outside">
        <img src="../assets/bus_loading.png" style="max-width: 90%;">
        <p style="color: grey;">Загрузка.....</p>  
        <div class="loader"></div>
      </div>
      <div v-else>
        <h5>
          С вами свяжется оператор в течение нескольких часов!
        </h5>
        <div style="margin-top: 20px;"><a href="/">Вернуться на главную</a></div>
      </div>

  </div> 
</template>

<style scoped>
.feedback__list li{
  list-style: none;
}
</style>