<template>
    <div class="common-layout" v-loading.fullscreen.lock="false">
        <!-- <div class="container"> -->
            <Header/>
            <el-container>
                <el-main style="min-height: 500px;">
                    <el-input
                        v-model="robotsTxt"
                        :autosize="{ minRows: 20, maxRows: 30 }"
                        type="textarea"
                        placeholder="Please input"
                    /><br/>
                    <el-button style="margin-top: 10px;" @click="editRobotsTxt" type="primary" :loading="loading">{{loading ? 'Загрузка' : 'Сохранить' }}</el-button>
                </el-main>

            </el-container>
        <!-- </div> -->
    </div>
</template>

<script>

// import axios from 'axios';
import axiosAdmin from '../../axiosAdmin'
import Header from '../../components/admin/Header.vue'


export default {
  components: { Header },
  data(){
    return {
        robotsTxt: '',
        loading: false
    }
  },
  async mounted(){
    const promise = axiosAdmin
    .get('/robots/txt')
    .then(response => {
        this.robotsTxt = response.data.robotsTxt
        console.log(response)
    })
    .catch(error => {
        console.log(error)
    })
    await promise
  },
  methods: {
    async editRobotsTxt(){
        this.loading = true
        const promise = axiosAdmin
        .post('/robots/txt', {robotsTxt: this.robotsTxt})
        .then(response => {
            // this.robotsTxt = response.data.robotsTxt
            console.log(response)
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        this.loading = false
    }
  }
}
</script>
<style>

</style>