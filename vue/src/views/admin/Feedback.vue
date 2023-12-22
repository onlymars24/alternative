<template>
    <div class="common-layout">
        <Header/>
        <el-container v-if="!loading">
            <el-main>
                <div style="margin-top: 20px;">
                    <el-card class="box-card">
                        <template #header>
                            <div class="card-header" style="display: flex; justify-content: space-between;">
                                <span>Список обращений</span>
                            </div>
                        </template>
                        <el-table :data="feedback" style="width: 100%">
                            <el-table-column prop="created_at" label="Дата вопроса" width="160" />
                            <el-table-column prop="name" label="Имя" width="160" />
                            <el-table-column prop="phone" label="Номер телефона" width="150" />
                            <el-table-column prop="email" label="Email" width="190" />
                            <el-table-column prop="topic" label="Тема" width="210" />
                            <el-table-column prop="descr" label="Описание" width="1100"></el-table-column>
                        </el-table>
                    </el-card>
                </div>
                <div style="margin-top: 20px;">
                    <el-space :fill="false" wrap :size="17"></el-space>
                </div>
            </el-main>
        </el-container>
    </div>
</template>
<script>
import axiosClient from '../../axios'
import router from '../../router'
import Header from '../../components/admin/Header.vue'
import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import axios from 'axios'
import ticketStatuses from '../../data/TicketStatuses'
import ru from 'element-plus/dist/locale/ru.mjs'

export default
{
    components: {Header},
    data() {
        return {
            feedback: []
        }
    },
    async mounted(){
        this.loading = true
        const promise = axiosClient
        .get('/get/feedback')
        .then(response => {
            this.feedback = response.data.feedback
            console.log(response)
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        this.loading = false
        this.feedback.forEach(el => {
            el.created_at = dayjs(el.created_at).format('YYYY-MM-DD HH:mm:ss')
        })
    },
    methods: {

    },
    watch: {

    },
    computed: {

    }
}
</script>
<style>
.table-cell-word-wrap {
    white-space: normal;
    word-wrap: break-word;
}
</style>