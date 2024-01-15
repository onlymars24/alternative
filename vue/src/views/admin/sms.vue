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
                                <el-table-column prop="firstName" label="Имя получателя" width="160" />
                                <el-table-column prop="lastName" label="Фамилия получателя" width="160" />
                                <el-table-column prop="middleName" label="Отчество получателя" width="180" />
                                <el-table-column prop="text" label="Текст" width="250" />
                                <el-table-column prop="status" label="Статус" width="135" />
                                <el-table-column prop="price" label="Стоимость" width="120" />
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

export default {
    components: {
        Header
    },
    data() {
        return {
            smsLoading: false,
            smsArray: [],
            currency: 'RUB'
        }
    },
    async mounted() {
        this.smsLoading = true

        // Запрос на получение смсок
        // const promise = axiosClient
        // .get('/sms')
        // .then(response => {
        //     console.log(response.data)
        //     this.smsArray = response.data.sms
        // })
        // .catch( error => {

        // })
        // await promise
        this.smsArray = [
            {
                date: '19.12.2023 12:12:12',
                firstName: 'Иван',
                lastName: 'Иванов',
                middleName: 'Иванович',
                text: 'Бла бла бла бла бла бла',
                status: 'Отправлено',
                price: '2 ' + this.currency
            },
            {
                date: '21.04.2023 12:12:12',
                firstName: 'Иван',
                lastName: 'Иванов',
                middleName: 'Иванович',
                text: 'Бла бла бла бла бла бла',
                status: 'Отправлено',
                price: '2 ' + this.currency
            },
            {
                date: '19.12.2023 12:12:12',
                firstName: 'Иван',
                lastName: 'Иванов',
                middleName: 'Иванович',
                text: 'Бла бла бла бла бла бла',
                status: 'Отправлено',
                price: '2 ' + this.currency
            },
            {
                date: '19.12.2023 12:12:12',
                firstName: 'Иван',
                lastName: 'Иванов',
                middleName: 'Иванович',
                text: 'Бла бла бла бла бла бла',
                status: 'Отправлено',
                price: '2 ' + this.currency
            },
            {
                date: '19.12.2023 12:12:12',
                firstName: 'Иван',
                lastName: 'Иванов',
                middleName: 'Иванович',
                text: 'Бла бла бла бла бла бла',
                status: 'Отправлено',
                price: '2 ' + this.currency
            },
        ]

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