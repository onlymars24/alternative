<template>
    <div class="common-layout" v-loading.fullscreen.lock="false">
        <!-- <div class="container"> -->
            <Header/>
            <el-container v-if="true">
                <el-main>
                    <div class="">
                        {{ content }}
                        <QuillEditor style="height: 300px; margin-bottom: 10px;" theme="snow" toolbar="full" v-model:content="content" contentType="html"/>
                        <el-button @click="editMain" type="primary" :loading="loading">{{loading ? 'Загрузка' : 'Сохранить' }}</el-button>
                    </div>
                </el-main>
            </el-container>
        <!-- </div> -->
    </div>
</template>

<script>

// import axios from 'axios';
import axiosClient from '../../axios';
import Header from '../../components/admin/Header.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

export default {
  components: { Header, QuillEditor },
  data(){
    return {
      race: [],
      loading: false,
      content: ''
    }
  },
  async mounted(){
    const promise = axiosClient
    .get('/page/main')
    .then(response => {
        console.log(response)
        this.content = response.data.pageMain
    })
    .catch(error => {
        console.log(error)
    })
    await promise
  },
  methods: {
    async editMain(){
        this.loading = true
        const promise = axiosClient
        .post('/page/main', {content: this.content})
        .then(response => {
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