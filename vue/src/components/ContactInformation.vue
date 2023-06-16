<!-- eslint-disable max-len -->
<!-- eslint-disable vuejs-accessibility/label-has-for -->
<template>
<div>
    <h3>Контактная информация</h3>
    <div class="row g-5">
        <div class="col-sm-6 ">
            <div class="contact-information__block ">
                <p>На электронную почту высылаем маршрутные квитанции</p>
                <label for="" class="label-gray">E-mail</label>
                <input type="text" class="form-control inp-gray" v-model="email" :disabled="!editEmailActive">
                <div class="filled-email">
                <a href="#" v-if="!editEmailActive" @click="editEmailActive = true"> &#9998; Изменить</a> <a href="#" v-if="editEmailActive" @click="editEmailActive = false">Отменить</a> <a href="#" v-if="editEmailActive" @click="editEmail">Сохранить</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class=" contact-information__block">
                <p>С помощью телефона мы сможем связаться с вами,
                     в случае отмены, переноса рейса или в других критических случаях</p>
                <label for="" class="label-gray">Телефон</label>
                <input type="text" class="form-control inp-gray" :value="this.user.phone" disabled>
            </div>
        </div>
    </div>
    </div>
</template>
<style>
.filled-email
{
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
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
        })
        await promise
        this.editEmailActive = false
    }
  }

};
</script>