<template>
    <div class="common-layout">
        <Header/>
            <el-container>
                <el-main>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <!-- <span>Выберите период для отладки</span> -->
                                    <span>Введите ID заказа для проверки</span>
                                </div>
                                <div class="block" style="margin-top: 10px; align-items: center;" v-if="!loading">
                                    <el-config-provider :locale="locale">
                                    <!-- <el-date-picker
                                        v-model="value2"
                                        type="datetimerange"
                                        start-placeholder="От"
                                        end-placeholder="До"
                                        :default-time="defaultTime2"
                                        :clearable="true"
                                    /> -->
                                    <el-input placeholder="ID" v-model="this.orderId"/>
                                    <el-button type="primary" @click="debugging" :loading="loading" :disable="orderId">Запустить</el-button>
                                    </el-config-provider>
                                    <div>
                                    </div>
                                    

                                </div>
                            </template>
                            <BusLoading v-if="loading"/>
                            <div v-if="!loading">
                                <template v-if="done">
                                    <div v-if="bugs.length != 0">
                                        <el-alert
                                            title="Обнаружены неточности в заказе"
                                            type="error"
                                            center
                                            show-icon
                                            :closable="false">
                                        </el-alert>

                                        <pre>{{ bugs }}</pre>
                                    </div>
                                    <div v-else>
                                        <el-alert
                                            title="Ошибок в заказе не обнаружено"
                                            type="success"
                                            show-icon
                                            :closable="false">
                                        </el-alert>
                                    </div>
                                </template>
                                <template v-else>
                                    
                                </template>
                                
                                <!-- <pre>{{ order }}</pre> -->
                            </div> 
                            
                        </el-card>
                    </div>
                </el-main>
            </el-container>
    </div>
</template>

<script>
import ru from 'element-plus/dist/locale/ru.mjs'
import axiosAdmin from '../../axiosAdmin'
import Header from '../../components/admin/Header.vue'


export default
{
    components: { Header},
    data() {
        return {
            defaultTime2: [],
            locale: ru,
            value2: null,
            orderId: '',
            bugs: [],
            order: [],
            loading: false,
            done: false
        }
    },
    mounted(){
        let date = new Date();
        this.date1 = new Date()
        this.defaultTime2 = [
            new Date(date.getFullYear(), date.getMonth(), 1, 0, 0, 0),
            new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59),
        ]    
        this.value2 = [
            new Date(date.getFullYear(), date.getMonth(), 1, 0, 0, 0),
            new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59),
        ]
    },
    methods: {
        async debugging(){
            this.loading = true
            const promise = axiosAdmin
            .post('/debugging', {orderId: this.orderId})
            .then(response => {
                console.log(response)
                this.bugs = response.data.bugs
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.done = true
            this.loading = false
        }
    }
}
</script>

