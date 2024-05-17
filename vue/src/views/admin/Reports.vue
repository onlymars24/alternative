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
                                        <td>Продажа пассажирских билетов</td>
                                        <td>{{ salesPassengerAmount }}</td>
                                        <td>{{ salesPassengerSupplierFares }}₽</td>
                                        <td>{{ salesPassengerSupplierDues }}₽</td>
                                        <td>{{ salesPassengerDues }}₽</td>
                                        <td>{{ salesPassengerTotal }}₽</td>
                                        <td>{{ salesPassengerSiteCommission }}₽</td>
                                    </tr>
                                    <tr>
                                        <td>Возврат пассажирских билетов</td>
                                        <td>{{ returnsPassengerAmount }}</td>
                                        <td></td>
                                        <td>{{ returnsPassengerSupplierDues }}₽</td>
                                        <td>{{ returnsPassengerDues }}₽</td>
                                        <td>{{ repaymentsPassenger }}₽</td>
                                        <td>{{ returnsPassengerSiteCommission }}₽</td>
                                    </tr>
                                    <tr>
                                        <td>Продажа багажных билетов</td>
                                        <td>{{ salesLuggageAmount }}</td>
                                        <td>{{ salesLuggageSupplierFares }}₽</td>
                                        <td>{{ salesLuggageSupplierDues }}₽</td>
                                        <td>{{ salesLuggageDues }}₽</td>
                                        <td>{{ salesLuggageTotal }}₽</td>
                                        <td>{{ salesLuggageSiteCommission }}₽</td>
                                    </tr>
                                    <tr>
                                        <td>Возврат багажных билетов</td>
                                        <td>{{ returnsLuggageAmount }}</td>
                                        <td></td>
                                        <td>{{ returnsLuggageSupplierDues }}₽</td>
                                        <td>{{ returnsLuggageDues }}₽</td>
                                        <td>{{ repaymentsLuggage }}₽</td>
                                        <td>{{ returnsLuggageSiteCommission }}₽</td>
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

                            <table class="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Количество страховок</th>
                                        <th>Стоимость страховок</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Продажа страховок</td>
                                        <td>{{salesInsurances[0]}}</td>
                                        <td>{{salesInsurances[1]}}</td>
                                    </tr>
                                    <tr>
                                        <td>Возврат</td>
                                        <td>{{returnsInsurances[0]}}</td>
                                        <td>{{returnsInsurances[1]}}</td>
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
                            <el-table :data="ticketsTable" style="width: 100%">
                                <el-table-column prop="dispatchDate" label="Дата и время отправления (местное)" width="200" />
                                <el-table-column prop="created_at" label="Дата создания заказа на росвокзалах (GMT +3)" width="190" />
                                <el-table-column prop="confirmed_at" label="Дата покупки (местное)" width="180" />
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
                                <el-table-column prop="tablePrice" label="Стоимость" width="120" />
                                <el-table-column prop="tableRepayment" label="Сумма возврата" width="150" />
                                <el-table-column prop="supplierDues" label="Сбор поставщика" width="150" />
                                <el-table-column prop="dues" label="Сбор агента" width="150" />
                                <el-table-column prop="tableDiffPrice" label="Удержание" width="150" />
                                <el-table-column prop="duePrice" label="Комиссия сайта" width="150" />
                                <el-table-column prop="bonusesPrice" label="Оплата бонусами" width="150" />
                                <el-table-column prop="insured" label="Страхование" width="150" />
                                <el-table-column prop="insurancePrice" label="Цена страховки" width="150" />
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
import axiosAdmin from '../../axiosAdmin'
import router from '../../router'
import Header from '../../components/admin/Header.vue'
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';
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
            locale: ru,
            ticketsTableVar: []
        }
    },
    async mounted(){
        // dayjs.extend(utc);
        // dayjs.extend(timezone);
        // let timeZone = 'Asia/Krasnoyarsk'
        // let currentTime = dayjs().tz(timeZone).format('YYYY-MM-DD HH:mm:ss');


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
        
        const promise = axiosAdmin
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
            ticket.insurance = ticket.insurance ? JSON.parse(ticket.insurance) : null
            // console.log(ticket.insurance)
            ticket.insurancePrice = ticket.insurance ? ticket.insurance.rate[0].value.toFixed(2) : (0).toFixed(2)
            ticket.insured = ticket.insurance ? 'Застрахован': 'Не застрахован'
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

            +'&salesPassengerAmount='+this.salesPassengerAmount
            +'&salesPassengerSupplierFares='+this.salesPassengerSupplierFares
            +'&salesPassengerSupplierDues='+this.salesPassengerSupplierDues
            +'&salesPassengerDues='+this.salesPassengerDues
            +'&salesPassengerTotal='+this.salesPassengerTotal
            +'&salesPassengerSiteCommission='+this.salesPassengerSiteCommission
            +'&returnsPassengerAmount='+this.returnsPassengerAmount
            +'&returnsPassengerSupplierDues='+this.returnsPassengerSupplierDues
            +'&returnsPassengerDues='+this.returnsPassengerDues
            +'&repaymentsPassenger='+this.repaymentsPassenger
            +'&returnsPassengerSiteCommission='+this.returnsPassengerSiteCommission

            +'&salesLuggageAmount='+this.salesLuggageAmount
            +'&salesLuggageSupplierFares='+this.salesLuggageSupplierFares
            +'&salesLuggageSupplierDues='+this.salesLuggageSupplierDues
            +'&salesLuggageDues='+this.salesLuggageDues
            +'&salesLuggageTotal='+this.salesLuggageTotal
            +'&salesLuggageSiteCommission='+this.salesLuggageSiteCommission
            +'&returnsLuggageAmount='+this.returnsLuggageAmount
            +'&returnsLuggageSupplierDues='+this.returnsLuggageSupplierDues
            +'&returnsLuggageDues='+this.returnsLuggageDues
            +'&repaymentsLuggage='+this.repaymentsLuggage
            +'&returnsLuggageSiteCommission='+this.returnsLuggageSiteCommission

            +'&holds='+this.holds
            +'&holdsSupplierDues='+this.holdsSupplierDues
            +'&holdsDues='+this.holdsDues
            +'&holdsTotal='+this.holdsTotal
            +'&holdsSiteCommission='+this.holdsSiteCommission
            +'&eTrafficTotal='+this.eTrafficTotal

            +'&salesInsurancesAmount='+this.salesInsurances[0]
            +'&salesInsurancesPrice='+this.salesInsurances[1]

            +'&returnsInsurancesAmount='+this.returnsInsurances[0]
            +'&returnsInsurancesPrice='+this.returnsInsurances[1]
        },
        downloadPDF(){
            return import.meta.env.VITE_API_BASE_URL+'/export/pdf/?comparingDate1='+dayjs(this.comparingDates[0]).format('YYYY-MM-DD HH:mm:ss')
            +'&comparingDate2='+dayjs(this.comparingDates[1]).format('YYYY-MM-DD HH:mm:ss')
            
            +'&salesPassengerSupplierFares='+this.salesPassengerSupplierFares
            +'&repaymentsPassenger='+this.repaymentsPassenger

            +'&salesLuggageSupplierFares='+this.salesLuggageSupplierFares
            +'&repaymentsLuggage='+this.repaymentsLuggage

            +'&salesSupplierDues='+this.salesSupplierDues
            +'&salesDues='+this.salesDues

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
        ticketsTable(){
            let comparingDates = [
                dayjs(this.comparingDates[0]).format('YYYY-MM-DD HH:mm:ss'),
                dayjs(this.comparingDates[1]).format('YYYY-MM-DD HH:mm:ss')
            ];
            let ticketsTable = this.ticketsArray.filter(ticket => {
                return  (
                            ticket.status != 'B' &&
                            ticket.created_at > comparingDates[0] &&
                            ticket.created_at < comparingDates[1]
                        )
                        ||
                        (
                            ticket.status == 'R' &&
                            ticket.updated_at > comparingDates[0] &&
                            ticket.updated_at < comparingDates[1]
                        )
            })
            ticketsTable.forEach(ticket => {
                ticket.tablePrice = (0).toFixed(2)
                ticket.tableRepayment = (0).toFixed(2)
                ticket.tableDiffPrice = (0).toFixed(2)
            if(ticket.status != 'B' && 
                    ticket.status != 'R' && 
                    ticket.created_at > comparingDates[0] &&
                    ticket.created_at < comparingDates[1]
                ){
                    ticket.tablePrice = ticket.price
                    ticket.tableRepayment = (0).toFixed(2)
                    ticket.tableDiffPrice = (0).toFixed(2)
                }
            else if(ticket.status == 'R' && 
                    ticket.created_at > comparingDates[0] &&
                    ticket.created_at < comparingDates[1] &&
                    ticket.updated_at > comparingDates[0] &&
                    ticket.updated_at < comparingDates[1]
                ){
                    ticket.tablePrice = ticket.price
                    ticket.tableRepayment = ticket.repayment
                    ticket.tableDiffPrice = ticket.diffPrice
                }
            else if(ticket.status == 'R' &&
                    ticket.updated_at > comparingDates[0] &&
                    ticket.updated_at < comparingDates[1]
                ){
                    ticket.tablePrice = (0).toFixed(2)
                    ticket.tableRepayment = ticket.repayment
                    ticket.tableDiffPrice = ticket.diffPrice
                }
            else if(ticket.status == 'R' &&
                    ticket.updated_at > comparingDates[1]
                ){
                    ticket.tablePrice = ticket.price
                    ticket.tableRepayment = (0).toFixed(2)
                    ticket.tableDiffPrice = (0).toFixed(2)
                }
            })
            return ticketsTable
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


        salesPassenger(){
            return this.tickets.filter(ticket => {
                return ticket.ticketType != 'Багажный'
            });
        },
        salesPassengerAmount(){
            return this.salesPassenger.length
        },
        salesPassengerSupplierFares(){
            let supplierFare = 0
            this.salesPassenger.forEach(ticket => {
                supplierFare += Number(ticket.supplierFare)
            })
            return supplierFare.toFixed(2);
        },
        salesPassengerSupplierDues(){
            let supplierDues = 0
            this.salesPassenger.forEach(ticket => {
                supplierDues += Number(ticket.supplierDues)
            })
            return supplierDues.toFixed(2)
        },
        salesPassengerDues(){
            let dues = 0
            this.salesPassenger.forEach(ticket => {
                dues += Number(ticket.dues)
            })
            return dues.toFixed(2)
        },
        salesPassengerTotal(){
            let prices = 0
            this.salesPassenger.forEach(ticket => {
                prices += Number(ticket.price)
            })
            return prices.toFixed(2)
        },
        salesPassengerSiteCommission(){
            let siteCommission = 0
            this.salesPassenger.forEach(ticket => {
                siteCommission += Number(ticket.duePrice)
            })
            return siteCommission.toFixed(2);
        },
        ////

        returnsPassenger(){
            return this.returnedTickets.filter(ticket => {
                return ticket.ticketType != 'Багажный'
            });
        },
        returnsPassengerAmount(){
            return this.returnsPassenger.length
        },
        returnsPassengerSupplierDues(){
            let returnsSupplierDues = 0
            this.returnsPassenger.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsSupplierDues += Number(ticket.supplierDues)
                }
            })
            return returnsSupplierDues.toFixed(2)
        },
        returnsPassengerDues(){
            let returnsDues = 0
            this.returnsPassenger.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsDues += Number(ticket.dues)
                }
            })
            return returnsDues.toFixed(2)
        },
        repaymentsPassenger(){
            let repayments = 0
            //this.returnedTickets.forEach(ticket => {
            //    repayments += Number(ticket.repayment)
            //})
            this.returnsPassenger.forEach(ticket => {
                repayments += Number(ticket.price)
            })
            return repayments.toFixed(2);
        },
        returnsPassengerSiteCommission(){
            let returnsSiteCommission = 0
            this.returnsPassenger.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsSiteCommission += Number(ticket.duePrice)
                }
            })
            return returnsSiteCommission.toFixed(2);
        },
        ////


        salesLuggage(){
            return this.tickets.filter(ticket => {
                return ticket.ticketType == 'Багажный'
            });
        },
        salesLuggageAmount(){
            return this.salesLuggage.length
        },
        salesLuggageSupplierFares(){
            let supplierFare = 0
            this.salesLuggage.forEach(ticket => {
                supplierFare += Number(ticket.supplierFare)
            })
            return supplierFare.toFixed(2);
        },
        salesLuggageSupplierDues(){
            let supplierDues = 0
            this.salesLuggage.forEach(ticket => {
                supplierDues += Number(ticket.supplierDues)
            })
            return supplierDues.toFixed(2)
        },
        salesLuggageDues(){
            let dues = 0
            this.salesLuggage.forEach(ticket => {
                dues += Number(ticket.dues)
            })
            return dues.toFixed(2)
        },
        salesLuggageTotal(){
            let prices = 0
            this.salesLuggage.forEach(ticket => {
                prices += Number(ticket.price)
            })
            return prices.toFixed(2)
        },
        salesLuggageSiteCommission(){
            let siteCommission = 0
            this.salesLuggage.forEach(ticket => {
                siteCommission += Number(ticket.duePrice)
            })
            return siteCommission.toFixed(2);
        },


        returnsLuggage(){
            return this.returnedTickets.filter(ticket => {
                return ticket.ticketType == 'Багажный'
            });
        },
        returnsLuggageAmount(){
            return this.returnsLuggage.length
        },
        returnsLuggageSupplierDues(){
            let returnsSupplierDues = 0
            this.returnsLuggage.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsSupplierDues += Number(ticket.supplierDues)
                }
            })
            return returnsSupplierDues.toFixed(2)
        },
        returnsLuggageDues(){
            let returnsDues = 0
            this.returnsLuggage.forEach(ticket => {
                if(ticket.raceCanceled){
                    returnsDues += Number(ticket.dues)
                }
            })
            return returnsDues.toFixed(2)
        },
        repaymentsLuggage(){
            let repayments = 0
            //this.returnedTickets.forEach(ticket => {
            //    repayments += Number(ticket.repayment)
            //})
            this.returnsLuggage.forEach(ticket => {
                repayments += Number(ticket.price)
            })
            return repayments.toFixed(2);
        },
        returnsLuggageSiteCommission(){
            let returnsSiteCommission = 0
            this.returnsLuggage.forEach(ticket => {
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
        },

        salesInsurances(){
            let amount = 0
            let price = 0
            this.tickets.forEach(ticket => {
                if(ticket.insured == 'Застрахован'){
                    amount++
                    price += Number(ticket.insurancePrice)
                }
            })
            return [amount, price.toFixed(2)]
        },
        returnsInsurances(){
            let amount = 0
            let price = 0
            this.returnedTickets.forEach(ticket => {
                if(ticket.insured == 'Застрахован'){
                    amount++
                    price += Number(ticket.insurancePrice)
                }
            })
            return [amount, price.toFixed(2)]
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