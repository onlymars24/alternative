<template>
       <div class="common-layout">
        <Header/>
        <div class="container" v-loading.fullscreen.lock="smsLoading">  
            <el-container v-if="!smsLoading">
                <el-main>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <span>Список СМС</span>
                                </div>
                            </template>
                            <el-table :data="smsArray" style="width: 100%">
                                <el-table-column prop="date" label="Дата и время отправления" width="200" />
                                <el-table-column prop="phone" label="Телефон" width="140" />
                                <el-table-column prop="type" label="Тип" width="120" />
                                <el-table-column prop="cost" label="Стоимость" width="120" />
                                <el-table-column prop="balance" label="Баланс после отправки" width="200" />
                                <el-table-column prop="textStatus" label="Статус" width="135" />
                            </el-table>
                        </el-card>
                    </div>
                    <div style="margin-top: 20px;">
                        <el-space :fill="false" wrap :size="17"></el-space>
                    </div>
                </el-main>
            </el-container>
        </div>
    </div>
</template>
<script>
import Header from '../../components/admin/Header.vue'
import axiosAdmin from '../../axiosAdmin'
import dayjs from 'dayjs';

export default {
    components: {
        Header
    },
    data() {
        return {
            smsLoading: false,
            smsArray: [],
            currency: 'RUB',
            statuses: {
                0: 'В очереди', 1: 'Доставлено', 2: 'Не доставлено', 3: 'Передано', 8: 'На модерации', 6: 'Сообщение отклонено', 4: 'Ожидание статуса сообщения'
            }
        }
    },
    async mounted() {
        this.smsLoading = true

        // Запрос на получение смсок
        const promise = axiosAdmin
        .get('/sms/all')
        .then(response => {
            console.log(response.data)
            this.smsArray = response.data.sms
            console.log(this.smsArray)
        })
        .catch( error => {

        })
        await promise
        this.smsArray.forEach(sms => {
            sms.date = dayjs(sms.created_at).format('YYYY-MM-DD HH:mm:ss')
            if(sms.status != null){
                sms.textStatus = this.statuses[sms.status]
            }
        })
        this.smsLoading = false
    }
}
</script>
<style type="text/css">
.table {
	width: 100%;
	margin-bottom: 20px;
	border-collapse: collapse;
}
.table th {
	font-weight: bold;
	padding: 5px;
	background: #efefef;
	border: 1px solid #dddddd;
}
.table td {
	border: 1px solid #dddddd;
	padding: 5px;
}
.table tr td:first-child, .table tr th:first-child {
	border-left: none;
    background: #efefef;
    font-weight: bold;
}
.table tr td:last-child, .table tr th:last-child {
	border-right: none;
}

</style>