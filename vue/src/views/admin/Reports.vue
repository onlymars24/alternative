<template>
    <div class="common-layout">
        <Header/>
            
            <el-container>
                <el-main>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <span>Статистика за период</span>
                                </div>
                                <div class="block" style="margin-top: 10px;" v-if="!loading">
                                    <!-- {{ value2 }} -->
                                    <el-date-picker
                                    v-model="value2"
                                    type="datetimerange"
                                    start-placeholder="Start Date"
                                    end-placeholder="End Date"
                                    :default-time="defaultTime2"
                                    :clearable="true"
                                    />
                                </div>
                            </template>
                            <table class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Количество</th>
                                        <th>Тариф</th>
                                        <th>Сбор автовокзала</th>
                                        <th>Сбор агента</th>
                                        <th>Итого</th>
                                        <th>Комиссия сайта</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Продажа билетов</td>
                                        <td>{{ salesAmount }}</td>
                                        <td>{{ salesSupplierFares }}₽</td>
                                        <td>{{ salesSupplierDues }}₽</td>
                                        <td>{{ salesDues }}₽</td>
                                        <td>{{ salesTotal }}₽</td>
                                        <td>{{ salesSiteCommission }}₽</td>
                                    </tr>
                                    <tr>
                                        <td>Возврат</td>
                                        <td>{{ returnsAmount }}</td>
                                        <td></td>
                                        <td>{{ returnsSupplierDues }}₽</td>
                                        <td>{{ returnsDues }}₽</td>
                                        <td>{{ repayments }}₽</td>
                                        <td>{{ returnsSiteCommission }}₽</td>
                                    </tr>
                                    <tr>
                                        <td>Удержание</td>
                                        <td>{{  }}</td>
                                        <td>{{ holds }}₽</td>
                                        <td>{{ holdsSupplierDues }}₽</td>
                                        <td>{{ holdsDues }}₽</td>
                                        <td>{{ holdsTotal }}₽</td>
                                        <td>{{ holdsSiteCommission }}₽</td>
                                    </tr>
                                </tbody>
                            </table>

                        </el-card>
                    </div>
                    <el-button @click="exportDataToCsv" type="primary">Скачать</el-button>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <span>Список билетов</span>
                                </div>
                                
                            </template>
                            <el-table :data="tickets" style="width: 100%">
                                <el-table-column prop="dispatchDate" label="Дата и время отправления" width="200" />
                                <el-table-column prop="created_at" label="Дата и время брони" width="200" />
                                <el-table-column prop="ticketNum" label="Номер билета" width="120" />
                                <el-table-column prop="order_id" label="ID заказа" width="120" />
                                <el-table-column prop="dispatchStation" label="Пункт отправления" width="180" />
                                <el-table-column prop="arrivalStation" label="Пункт прибытия" width="180" />                                
                                <el-table-column prop="lastName" label="Фамилия" width="150" />
                                <el-table-column prop="firstName" label="Имя" width="150" />
                                <el-table-column prop="middleName" label="Отчество" width="150" />
                                <el-table-column prop="status" label="Статус" width="120" />
                                <el-table-column prop="price" label="Стоимость" width="120" />
                                <el-table-column prop="diffPrice" label="Сбор поставщика" width="150" />
                                <el-table-column prop="duePrice" label="Сбор агента" width="150" />
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

export default
{
    components: {Header},
    data() {
        return {
            ticketsArray: [],
            testArr: [{name: 'Marsel', surname: 'Galimov' }, {name: 'Marsel1', surname: 'Galimov1' }],
            defaultTime2: [],
            newDate: '',
            loading: false,
            value2: null
        }
    },
    async mounted(){
        this.loading = true
        let date = new Date();
        this.defaultTime2 = [
            new Date(date.getFullYear(), date.getMonth(), 1, 0, 0, 0),
            new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59),
        ]    
        this.value2 = [
            new Date(date.getFullYear(), date.getMonth(), 1, 0, 0, 0),
            new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59),
        ]
        console.log(this.defaultTime2)
        
        const promise = axiosClient
        .get('/tickets')
        .then(response => {
            console.log(response.data)
            this.ticketsArray = response.data.tickets
            console.log(this.ticketsArray)
        })
        .catch( error => {

        })
        await promise
        this.ticketsArray.forEach(ticket => {
            ticket.diffPrice = ticket.price - ticket.repayment
            ticket.created_at = dayjs(ticket.created_at).format('YYYY-MM-DD HH:mm:ss')
        })
        
        this.loading = false
    },
    methods: {
        exportDataToCsv(){
            const csvContent = this.convertToCSV(this.testArr)
            console.log(csvContent)
            const blob = new Blob([csvContent], {type: 'text/csv;charset=utf-8'})
            const url = URL.createObjectURL(blob)
            const link = document.createElement('a')
            link.href = url
            link.setAttribute('download', 'export_data.csv')
            link.click()
        },
        convertToCSV(data){
            const headers = Object.keys(data[0])
            const rows = data.map(obj => headers.map(header => obj[header]))
            const headerRow = headers.join(',')
            const csvRows = [headerRow, ...rows.map(row => row.join(','))]
            return csvRows.join('\n')
        }
    },
    watch: {
        value2(value2){
            if(!value2){
                this.value2 = this.defaultTime2
            }
        }
    },
    computed: {
        comparingDates(){
            let date = new Date()
            if(this.value2){
                return [
                    this.value2[0],
                    this.value2[1]
                ]
            }
            else{
                return [
                    this.defaultTime2[0],
                    this.defaultTime2[1]
                ]
            }
        },
        tickets(){
            return this.ticketsArray.filter(ticket => {
                return  ticket.status != 'B' && 
                        ticket.created_at > dayjs(this.comparingDates[0]).format('YYYY-MM-DD HH:mm:ss') &&
                        ticket.created_at < dayjs(this.comparingDates[1]).format('YYYY-MM-DD HH:mm:ss')
            })
        },
        returnedTickets(){
            return this.tickets.filter(ticket => {
                return ticket.status == 'R'
            })
        },
        salesAmount(){
            return this.tickets.length;
        },
        salesSupplierFares(){
            let price = 0
            this.tickets.forEach(ticket => {
                price += Number(ticket.price)
            })
            return price;
        },
        salesSupplierDues(){
            let supplierDues = 0
            this.tickets.forEach(ticket => {
                supplierDues += Number(ticket.supplierDues)
            })
            return supplierDues
        },
        salesDues(){
            let dues = 0
            this.tickets.forEach(ticket => {
                dues += Number(ticket.dues)
            })
            return dues
        },
        salesTotal(){
            let prices = 0
            this.tickets.forEach(ticket => {
                prices += Number(ticket.price)
            })
            return prices
        },
        salesSiteCommission(){
            let siteCommission = 0
            this.tickets.forEach(ticket => {
                siteCommission += ticket.duePrice
            })
            return siteCommission;
        },
        returnsAmount(){
            return this.returnedTickets.length;
        },
        returnsSupplierDues(){
            let returnsSupplierDues = 0
            this.returnedTickets.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsSupplierDues += Number(ticket.supplierDues)
                }
                
            })
            return returnsSupplierDues
        },
        returnsDues(){
            let returnsDues = 0
            this.returnedTickets.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsDues += Number(ticket.dues)
                }
                
            })
            return returnsDues
        },
        repayments(){
            let repayments = 0
            this.returnedTickets.forEach(ticket => {
                repayments += Number(ticket.repayment)
            })
            return repayments;
        },
        returnsSiteCommission(){
            let returnsSiteCommission = 0
            this.returnedTickets.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsSiteCommission += Number(ticket.duePrice)
                }
            })
            return returnsSiteCommission;
        },
        holds(){
            let holds = 0;
            this.returnedTickets.forEach(ticket => {
                if(ticket.price - ticket.repayment){
                    console.log(ticket)
                }
                holds += (ticket.price - ticket.repayment)
            })
            return holds;
        },
        holdsSupplierDues(){
            let holdsSupplierDues = 0
            this.returnedTickets.forEach(ticket => {
                if(!ticket.raceCanceled){
                    holdsSupplierDues += Number(ticket.supplierDues)
                }
            })
            return holdsSupplierDues
        },
        holdsDues(){
            let holdsDues = 0
            this.returnedTickets.forEach(ticket => {
                if(!ticket.raceCanceled){
                    holdsDues += Number(ticket.dues)
                }
                
            })
            return holdsDues
        },
        holdsTotal(){
            return this.holdsDues + this.holdsSupplierDues + this.holds
        },
        holdsSiteCommission(){
            let returnsDues = 0
            this.returnedTickets.forEach(ticket => {
                if(!ticket.raceCanceled){
                    returnsDues += Number(ticket.duePrice)
                }
                
            })
            return returnsDues
        },
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