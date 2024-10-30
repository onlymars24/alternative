<template>
    <div class="common-layout">
        <Header/>
        <el-container v-loading.fullscreen.lock="loading">
            <el-main>
                <div style="width: 15%;  margin-bottom: 15px;">
                    <span class="demo-input-label">Комиссия сайта</span>
                    <div style="display: flex;">
                        <el-input v-model="dues.clusterDue" />
                        <el-button style="margin-left: 10px;" type="primary" @click="setDue('clusterDue', dues.clusterDue)">Изменить</el-button>
                    </div>
                </div>
                <div style="width: 15%; margin-bottom: 15px;">
                    <span class="demo-input-label">Комиссия эквайринга(карта)</span>
                    <div style="display: flex;">
                        <el-input v-model="dues.acqCardDue" />
                        <el-button style="margin-left: 10px;" type="primary" @click="setDue('acqCardDue', dues.acqCardDue)">Изменить</el-button>
                    </div>
                </div>
                <div style="width: 15%;  margin-bottom: 15px;">
                    <span class="demo-input-label">Комиссия эквайрина(СБП)</span>
                    <div style="display: flex;">
                        <el-input v-model="dues.acqSbpDue" />
                        <el-button style="margin-left: 10px;" type="primary" @click="setDue('acqSbpDue', dues.acqSbpDue)">Изменить</el-button>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <el-card class="box-card">
                        <template #header>
                            <div class="card-header" style="display: flex; justify-content: space-between;">
                                <span>Список расходов</span>
                            </div>
                        </template>
                        <el-table :data="expenses" style="width: 100%">
                            <el-table-column prop="period" label="Временной интервал" width="200" />
                            <el-table-column prop="whatsapp" label="Whatsapp" width="140" />
                            <el-table-column prop="sms" label="СМС" width="140" />
                            <el-table-column prop="server_host" label="Сервер" width="120" />
                            <el-table-column prop="ofd_ferma" label="Онлайн касса" width="120" />
                            <el-table-column label="" >
                                <template #default="scope">
                                    <el-button type="danger" @click="deleteExpense(scope.row)">Удалить</el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-card>
                    <el-card class="box-card" style="margin-top: 20px;">
                    <h4>Новый набор расходов</h4>
                    <div style="display: flex; width: 60%; justify-content: space-between;">
                        <div style="width: 18%;">
                            <span class="demo-input-label">Временной интервал</span>
                            <el-input v-model="newExpense.period"  v-mask="'####-##'"/>
                        </div>
                        <div style="width: 18%;">
                            <span class="demo-input-label">WhatsApp</span>
                            <el-input v-model="newExpense.whatsapp" />
                        </div>
                        <div style="width: 18%;">
                            <span class="demo-input-label">СМС</span>
                            <el-input v-model="newExpense.sms" />
                        </div>
                        <div style="width: 18%;">
                            <span class="demo-input-label">Сервер</span>
                            <el-input v-model="newExpense.server_host" />
                        </div>
                        <div style="width: 18%;">
                            <span class="demo-input-label">Онлайн касса</span>
                            <el-input v-model="newExpense.ofd_ferma" />
                        </div>
                    </div>
                    <div>
                        <el-button style="margin-top: 10px;" type="primary" @click="createExpense">Добавить</el-button>
                    </div>
                    </el-card>
                </div>
                <AdvertisingPdf/>
                <div style="margin-top: 20px;">
                    <el-space :fill="false" wrap :size="17"></el-space>
                </div>
                
            </el-main>
         </el-container>
    </div>
</template>
<script>
import Header from '../../components/admin/Header.vue'
import AdvertisingPdf from '../../components/admin/AdvertisingPdf.vue'
import axiosAdmin from '../../axiosAdmin'
import dayjs from 'dayjs';
import TheMask from 'vue-the-mask'

export default {
 components: {
    Header,
    AdvertisingPdf
 },
 data() {
    return {
        dues: {
            
        },
        expenses: [],
        newExpense: {
            period: '',
            whatsapp: '',
            sms: '',
            server_host: '',
            ofd_ferma: ''
        },
        varb2: '',
        loading: false
    }
 },
 async mounted() {
    this.loading = true
    const promise1 = axiosAdmin
    .get('/dues')
    .then(response => {
        this.dues = response.data.dues
        console.log(response)
    })
    .catch(error => {
        console.log(error)
    })
    await promise1
    const promise2 = axiosAdmin
    .get('/expenses')
    .then(response => {
        this.expenses = response.data.expenses
        console.log(response)
    })
    .catch(error => {
        console.log(error)
    })
    await promise2
    this.loading = false
 },
 methods: {
    async setDue(name, percent){
        this.loading = true
        const promise = axiosAdmin
        .post('/dues/set', {name, percent})
        .then(response => {
            console.log(response)
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        this.loading = false
        window.location.replace(window.location.origin + window.location.pathname);
    },
    async createExpense(){
        this.loading = true
        const promise = axiosAdmin
        .post('/expense/create', this.newExpense)
        .then(response => {
            console.log(response)
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        // this.loading = false
        window.location.replace(window.location.origin + window.location.pathname);
    },
    async deleteExpense(expense){
        this.loading = true
        const promise = axiosAdmin
        .post('/expense/delete', {expenseId: expense.id})
        .then(response => {
            console.log(response)
        })
        .catch(error => {
            console.log(error)
        })
        await promise 
        // this.loading = false
        window.location.replace(window.location.origin + window.location.pathname);
    }
 }
}
</script>
<style type="text/css">


</style>