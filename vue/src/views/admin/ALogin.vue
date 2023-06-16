<template>
  <el-form
    label-position="top"
    style="max-width: 460px; margin-top: 20px;"
  >
    <el-alert v-if="wrongPassword" title="Неверный пароль" type="error" @close="wrongPassword = false" style="margin-bottom: 10px;"/>
    <el-form-item label="Пароль">
      <el-input type="password" v-model="password"/>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="login" :loading="loginLoading">Войти</el-button>
    </el-form-item>
  </el-form>

</template>
<script>
import axiosClient from '../../axios'
import router from '../../router'

export default
{
    data() {
        return {
          loginLoading: false,
          wrongPassword: false,
          password: ''
        }
    },
    methods: {
      async login(){
            this.loginLoading = true
            this.wrongCredentialsMessage = ''
            const promise = axiosClient
            .post('/admin/login', {password: this.password})
            .then(response => {
                localStorage.setItem('authAdminToken', response.data.token)
                console.log(response.data.token)
                router.push({ name: 'Tickets'})
            })
            .catch(error => {
              console.log(error)
              this.wrongPassword = true
            })
            await promise
            this.loginLoading = false
      }
    }

}
</script>