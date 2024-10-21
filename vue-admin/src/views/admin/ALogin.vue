<template>
  <h1>Войти</h1>
  <!-- <el-button type="primary" @click="logout" :loading="loading">Выйти</el-button> -->
  <el-form
    v-if="step==1"
    label-position="top"
    style="max-width: 460px; margin-top: 20px;"
  >
    <el-alert v-if="errorMessage" :title="errorMessage" type="error" @close="errorMessage = ''" style="margin-bottom: 5px;"/>
    <el-form-item label="Email">
      <el-input v-model="email"/>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="sendCode" :loading="loading">Запросить код подтверждения</el-button>
    </el-form-item>
  </el-form>
  <el-form
    v-if="step==2"
    label-position="top"
    style="max-width: 460px; margin-top: 20px;"
  >
    <el-alert v-if="errorMessage" :title="errorMessage" type="error" @close="errorMessage = ''" style="margin-bottom: 5px;"/>
    
    <el-form-item :label="'Введите код, отправленный НА ПОЧТУ '+email">
      <el-input v-model="code"/>
    </el-form-item>
    <el-link style="margin-bottom: 15px;" type="primary" @click="step=1">Ввести другую почту</el-link>
    <el-form-item>
      <el-button type="primary" @click="confirmCode" :loading="loading">Подтвердить</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import axiosAdmin from '../../axiosAdmin'
import router from '../../router'
import store from '../../store/index'

export default
{
    data() {
        return {
          loading: false,
          wrongPassword: false,
          errorMessage: '',
          email: '',
          code: '',
          step: 1
        }
    },
    mounted(){
      // router.push({ name: 'KladrStationPage'})
    },
    methods: {
      async sendCode(){
            this.loading = true
            this.errorMessage = ''
            const promise = axiosAdmin
            .post('/member/send/code', {email: this.email})
            .then(response => {
                this.step = 2
                console.log(response)
            })
            .catch(error => {
              console.log(error)
              if(error.status == 429){
                this.errorMessage = 'Слишком много попыток ввода. Попробуйте позже.'
              }
              else{
                this.errorMessage = error.response.data.errorMessage
              }
            })
            await promise
            this.loading = false
      },
      async confirmCode(){
        this.loading = true
        this.errorMessage = ''
        const promise = axiosAdmin
        .post('/member/confirm/code', {email: this.email, code: this.code})
        .then(async response => {
            await store.dispatch('getMember')
            router.push({ name: 'KladrStationPage'})
        })
        .catch(error => {
          console.log(error)
          if(error.status == 429){
            this.errorMessage = 'Слишком много попыток ввода. Попробуйте позже.'
          }
          else{
            this.errorMessage = error.response.data.errorMessage
          }
        })
        await promise
        this.loading = false
      },
      async logout(){
            this.loading = true
            await axiosAdmin
            .post('/member/logout')
            .then(response => {
                // localStorage.setItem('authAdminToken', response.data.token)
                // router.push({ name: 'Tickets'})
                console.log(response)
            })
            .catch(error => {
              console.log(error)
            })
            this.loading = false
      }
    }
}
</script>