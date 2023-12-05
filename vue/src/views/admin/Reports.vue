<template>
    <!-- {{ value2[0] }} -->
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
                                    <el-config-provider :locale="locale">
                                    <el-date-picker
                                        v-model="value2"
                                        type="datetimerange"
                                        start-placeholder="От"
                                        end-placeholder="До"
                                        :default-time="defaultTime2"
                                        :clearable="true"
                                    />
                                    </el-config-provider>

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
                                    <tr>
                                        <td>Сумма для E-traffic</td>
                                        <td>{{  }}</td>
                                        <td>{{  }}</td>
                                        <td>{{  }}</td>
                                        <td>{{  }}</td>
                                        <td>{{ eTrafficTotal }}₽</td>
                                        <td>{{  }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </el-card>
                    </div>
                    <el-button @click="exportDataToCsv" type="primary">Скачать Excel</el-button>
                    <el-button @click="exportDataToPdf" type="primary">Скачать PDF</el-button>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <span>Список билетов</span>
                                </div>
                            </template>
                            <el-table :data="tickets" style="width: 100%">
                                <el-table-column prop="dispatchDate" label="Дата и время отправления (местное)" width="200" />
                                <el-table-column prop="created_at" label="Дата и время брони (GMT +3)" width="160" />
                                <el-table-column prop="dateReturned" label="Дата и время возврата (GMT +3)" width="180" />
                                <el-table-column prop="timezone" label="Часовой пояс" width="135" />
                                <el-table-column prop="ticketNum" label="Номер билета" width="120" />
                                <el-table-column prop="order_id" label="ID заказа" width="120" />
                                <el-table-column prop="dispatchStation" label="Пункт отправления" width="180" />
                                <el-table-column prop="arrivalStation" label="Пункт прибытия" width="180" />
                                <el-table-column prop="lastName" label="Фамилия" width="150" />
                                <el-table-column prop="firstName" label="Имя" width="150" />
                                <el-table-column prop="middleName" label="Отчество" width="150" />
                                <el-table-column prop="raceCancelledLabel" label="Статус рейса" width="120" />
                                <el-table-column prop="fullStatus" label="Статус билета" width="120" />
                                <el-table-column prop="price" label="Стоимость" width="120" />
                                <el-table-column prop="supplierDues" label="Сбор поставщика" width="150" />
                                <el-table-column prop="dues" label="Сбор агента" width="150" />
                                <el-table-column prop="diffPrice" label="Удержание" width="150" />
                                <el-table-column prop="duePrice" label="Комиссия сайта" width="150" />
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
            ticketsArray: [],
            testArr: [{name: 'Marsel', surname: 'Galimov', patr: 'qwert' }, {name: 'Marsel1', surname: 'Galimov1', patr: 'qwerty' }],
            defaultTime2: [],
            newDate: '',
            loading: false,
            value2: null,
            date1: null,
            ticketStatuses: ticketStatuses,
            locale: ru
        }
    },
    async mounted(){
        console.log(this.downloadPDF)
        this.loading = true
        let date = new Date();
        this.date1 = new Date()
        console.log(new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59))
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
            ticket.updated_at = dayjs(ticket.updated_at).format('YYYY-MM-DD HH:mm:ss')
            if(ticket.status == 'R'){
                ticket.diffPrice = (ticket.price - ticket.repayment).toFixed(2)
                ticket.dateReturned = ticket.updated_at
            }
            else{
                ticket.diffPrice = (0).toFixed(2)
                ticket.dateReturned = null
            }
            if(ticket.raceCancelled){
                ticket.raceCancelledLabel = 'Отменён'
            }
            else{
                ticket.raceCancelledLabel = 'Не отменён'
            }
            ticket.duePrice = Number(ticket.duePrice).toFixed(2)
            ticket.created_at = dayjs(ticket.created_at).format('YYYY-MM-DD HH:mm:ss')
            ticket.fullStatus = ticketStatuses[ticket.status].label
        })
        
        this.loading = false
    },
    methods: {
        exportDataToCsv(){
            window.open(this.downloadExcel, '_blank');
        },
        exportDataToPdf(){
            window.open(this.downloadPDF, '_blank');
        },
    },
    watch: {
        value2(value2){
            if(!value2){
                this.value2 = this.defaultTime2
            }
        }
    },
    computed: {
        downloadExcel(){
            return import.meta.env.VITE_API_BASE_URL+
            '/export/excel/?comparingDate1='+dayjs(this.comparingDates[0]).format('YYYY-MM-DD HH:mm:ss')
            +'&comparingDate2='+dayjs(this.comparingDates[1]).format('YYYY-MM-DD HH:mm:ss')
            +'&salesAmount='+this.salesAmount
            +'&salesSupplierFares='+this.salesSupplierFares
            +'&salesSupplierDues='+this.salesSupplierDues
            +'&salesDues='+this.salesDues
            +'&salesTotal='+this.salesTotal
            +'&salesSiteCommission='+this.salesSiteCommission
            +'&returnsAmount='+this.returnsAmount
            +'&returnsSupplierDues='+this.returnsSupplierDues
            +'&returnsDues='+this.returnsDues
            +'&repayments='+this.repayments
            +'&returnsSiteCommission='+this.returnsSiteCommission
            +'&holds='+this.holds
            +'&holdsSupplierDues='+this.holdsSupplierDues
            +'&holdsDues='+this.holdsDues
            +'&holdsTotal='+this.holdsTotal
            +'&holdsSiteCommission='+this.holdsSiteCommission
            +'&eTrafficTotal='+this.eTrafficTotal
        },
        downloadPDF(){
            return import.meta.env.VITE_API_BASE_URL+'/export/pdf/?comparingDate1='+dayjs(this.comparingDates[0]).format('YYYY-MM-DD HH:mm:ss')
            +'&comparingDate2='+dayjs(this.comparingDates[1]).format('YYYY-MM-DD HH:mm:ss')
            +'&salesTotal='+this.salesTotal
            +'&repayments='+this.repayments
            +'&holdsTotal='+this.holdsTotal
            +'&eTrafficTotal='+this.eTrafficTotal
        },
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
            return this.ticketsArray.filter(ticket => {
                //console.log(ticket.created_at)
                 return ticket.status == 'R' &&
                        ticket.updated_at > dayjs(this.comparingDates[0]).format('YYYY-MM-DD HH:mm:ss') &&
                        ticket.updated_at < dayjs(this.comparingDates[1]).format('YYYY-MM-DD HH:mm:ss')
            })
        },
        salesAmount(){
            return this.tickets.length;
        },
        salesSupplierFares(){
            let supplierFare = 0
            this.tickets.forEach(ticket => {
                supplierFare += Number(ticket.supplierFare)
            })
            return supplierFare.toFixed(2);
        },
        salesSupplierDues(){
            let supplierDues = 0
            this.tickets.forEach(ticket => {
                supplierDues += Number(ticket.supplierDues)
            })
            return supplierDues.toFixed(2)
        },
        salesDues(){
            let dues = 0
            this.tickets.forEach(ticket => {
                dues += Number(ticket.dues)
            })
            return dues.toFixed(2)
        },
        salesTotal(){
            let prices = 0
            this.tickets.forEach(ticket => {
                prices += Number(ticket.price)
            })
            return prices.toFixed(2)
        },
        salesSiteCommission(){
            let siteCommission = 0
            this.tickets.forEach(ticket => {
                siteCommission += Number(ticket.duePrice)
            })
            return siteCommission.toFixed(2);
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
            return returnsSupplierDues.toFixed(2)
        },
        returnsDues(){
            let returnsDues = 0
            this.returnedTickets.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsDues += Number(ticket.dues)
                }
                
            })
            return returnsDues.toFixed(2)
        },
        repayments(){
            let repayments = 0
            //this.returnedTickets.forEach(ticket => {
            //    repayments += Number(ticket.repayment)
            //})
            this.returnedTickets.forEach(ticket => {
                repayments += Number(ticket.price)
            })
            return repayments.toFixed(2);
        },
        returnsSiteCommission(){
            let returnsSiteCommission = 0
            this.returnedTickets.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsSiteCommission += Number(ticket.duePrice)
                }
            })
            return returnsSiteCommission.toFixed(2);
        },
        holds(){
            let holds = 0;
            this.returnedTickets.forEach(ticket => {
                if(ticket.price - ticket.repayment){
                    console.log(ticket)
                }
                holds += (ticket.price - ticket.repayment)
            })
            return holds.toFixed(2);
        },
        holdsSupplierDues(){
            let holdsSupplierDues = 0
            this.returnedTickets.forEach(ticket => {
                if(!ticket.raceCanceled){
                    holdsSupplierDues += Number(ticket.supplierDues)
                }
            })
            return holdsSupplierDues.toFixed(2)
        },
        holdsDues(){
            let holdsDues = 0
            this.returnedTickets.forEach(ticket => {
                if(!ticket.raceCanceled){
                    holdsDues += Number(ticket.dues)
                }
            })
            return holdsDues.toFixed(2)
        },
        holdsTotal(){
            let total = Number(this.holdsDues) + Number(this.holdsSupplierDues) + Number(this.holds)
            return total.toFixed(2)
        },
        holdsSiteCommission(){
            let returnsDues = 0
            this.returnedTickets.forEach(ticket => {
                if(!ticket.raceCanceled){
                    returnsDues += Number(ticket.duePrice)
                }
            })
            return returnsDues.toFixed(2)
        },
        eTrafficTotal(){
            let eTrafficTotal = this.salesTotal - this.repayments + Number(this.holdsTotal)
            return eTrafficTotal.toFixed(2);
        }
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