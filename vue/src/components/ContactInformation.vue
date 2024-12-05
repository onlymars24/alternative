<!-- eslint-disable max-len -->
<!-- eslint-disable vuejs-accessibility/label-has-for -->
<template>
<BusLoading v-if="contactLoading"/>
<div class="contacts" v-else>
  <h2>Контактная информация</h2>  
  <div class="menu__ticket menu__ticket menu__ticket-bonuses contacts__balance">Мои бонусы: {{user.bonuses_balance}} руб</div>
  <div class="row g-5">
    <div class="col-sm-6 ">
        <form @submit.prevent="editEmail" class="contact-information__block">
            <p>Укажите email, на который будет отправляться билет</p>
            <label for="" class="label-gray">E-mail</label>
            <input type="email" class="form-control inp-gray" v-model="email" :disabled="!editEmailActive" placeholder="E-mail не указан">
            <div class="filled-email">
              <a href="#" v-if="!editEmailActive" @click="editEmailActive = true"> &#9998; Изменить</a> 
              <a href="#" v-if="editEmailActive" @click="editEmailActive = false">Отменить</a> 
              <button type="submit" v-if="editEmailActive" style="color: var(--bs-link-color);">Сохранить</button>
            </div>
        </form>
    </div>
    <div class="col-sm-6">
      <div class=" contact-information__block">
        <p>Ваш номер, указанный при регистрации на сайте, является логином для входа в личный кабинет и может быть использован для общения с технической поддержкой сайта, а также для передачи технической информации и уведомлений</p>
        <label for="" class="label-gray">Телефон</label>
        <input type="text" class="form-control inp-gray" :value="this.user.phone" disabled>
      </div>
    </div>
  </div>
</div>
</template>
<style scoped>
.filled-email
{
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
.contacts__balance{
  padding: 15px; margin-bottom: 15px;
}
</style>
<script>
import axiosClient from '../axios'
import BusLoading from '../components/BusLoading.vue'

export default
{
  components: {
      BusLoading,
  },
  data() {
    return {
      activeTab: 'UpcomingTrips',
      user: [],
      editEmailActive: false,
      email: '',
      contactLoading: false
    };
  },
  async mounted(){
    this.contactLoading = true
    const promise = axiosClient
    .get('/user')
    .then(response => {
        this.user = response.data.user
    })
    await promise
    this.email = this.user.email
    this.contactLoading = false
  },
  methods: {
    async editEmail(){
        const promise = axiosClient
        .post('/edit/email', {email: this.email})
        .then(response => {
          console.log(response)
        })
        .catch(error => {
          console.log(error)
        })
        await promise
        this.editEmailActive = false
    }
  }

};
</script>