<template>
    <div class="common-layout" v-loading.fullscreen.lock="orderLoading">
        <!-- <div class="container"> -->
            <Header/>
            <el-container v-if="!orderLoading">
                <el-main>
                    <el-descriptions :title="'Информация о заказе с ID: '+order.id" direction="vertical" :column="4" border>
                        <el-descriptions-item label="Сумма заказа">{{order.total}}₽</el-descriptions-item>
                        <el-descriptions-item label="Сумма возврата">{{order.repayment}}₽</el-descriptions-item>
                        <el-descriptions-item label="Статус заказа" :span="2">{{ orderStatus }}</el-descriptions-item>
                        <el-descriptions-item label="Дата создания заказа">{{order.created}}</el-descriptions-item>
                        <el-descriptions-item label="Истечение срока действия неоплаченного заказа">{{order.expired}}</el-descriptions-item>
                        <el-descriptions-item label="Завершение обработки заказа">{{order.finished}}</el-descriptions-item>
                        <el-descriptions-item label="IP плательщика">{{order_outside.ip}}</el-descriptions-item>
                        <el-descriptions-item label="Номер карты">{{order_outside.pan}}</el-descriptions-item>
                        <el-descriptions-item label="Сервисный сбор">{{order_outside.duePercent}}%</el-descriptions-item>
                        <el-descriptions-item label="Сумма сервисного сбора">{{order_outside.duePrice}}₽</el-descriptions-item>

                        <el-descriptions-item label="utm_source">{{order_outside.utm_source}}</el-descriptions-item>
                        <el-descriptions-item label="utm_medium">{{order_outside.utm_medium}}</el-descriptions-item>
                        <el-descriptions-item label="utm_campaign">{{order_outside.utm_campaign}}</el-descriptions-item>
                        <el-descriptions-item label="utm_content">{{order_outside.utm_content}}</el-descriptions-item>
                        <el-descriptions-item label="referrer_url">{{order_outside.referrer_url}}</el-descriptions-item>
                    </el-descriptions>
                    <el-button v-if="order_outside.dispatchPointId && order_outside.arrivalPointId" type="primary" style="margin-top: 20px;" @click="toReturnRace">Обратный рейс</el-button>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <span>История операций</span>
                                </div>
                            </template>
                            <template v-for="transaction in transactions">

                                <p v-if="transaction.StatusCode != 2">Чек {{transaction.type == 'Income' ? ' платежа' : ' возврата'}} ещё не готов</p>
                                <p v-if="transaction.StatusCode == 2"><a :href="transaction.OfdReceiptUrl" target="_blank">Чек</a>{{transaction.type == 'Income' ? ' платежа' : ' возврата'}}</p>
                            </template>
                        </el-card>
                    </div>
                    <div style="margin-top: 20px;">
                        <el-space :fill="false" wrap :size="17">
                            <TicketCard v-for=" ticket in tickets" :ticket="ticket" :ticketStatuses="ticketStatuses" :isOrderPage="true"/>
                        </el-space>
                    </div>
                </el-main>
            </el-container>
        <!-- </div> -->
    </div>
</template>
<script>
import axios from 'axios';
import axiosClient from '../../axios';
import Header from '../../components/admin/Header.vue'
import TicketCard from '../../components/admin/TicketCard.vue'
import ticketStatuses from '../../data/TicketStatuses';
import router from '../../router'

export default
{
    components: {Header, TicketCard},
    data() {
        return {
            order_id: this.$route.params['order_id'],
            transactions: [],
            order: [],
            order_outside: [],
            orderLoading: false,
            ticketStatuses: ticketStatuses,
            orderStatuses: {
                B: {
                    label: 'Забронирован',
                    value: 'B'
                },
                P: {
                    label: 'Частично возвращён',
                    value: 'P'
                },
                R: {
                    label: 'Возвращён',
                    value: 'R'
                },
                S: {
                    label: 'Оплачен',
                    value: 'S'
                }
            },
            orderStatus: '',
            tickets: []
        }
    },
    async mounted(){
        this.orderLoading = true
        const promise1 = axiosClient
        .get('/order?order_id='+this.order_id)
        .then(response => {
            this.order_outside = response.data.order
            this.order = JSON.parse(response.data.order.order_info)
            this.tickets = response.data.tickets
            console.log(this.order)
        })
        await promise1
        this.tickets.forEach(ticket => {
            ticket.insurance = ticket.insurance ? JSON.parse(ticket.insurance) : null
        })
        const promise2 = axiosClient
        .post('/order/transactions', {orderId: this.order_id})
        .then(response => {
            this.transactions = response.data.transactions
        })
        .catch(error => {
            console.log(error)
        })
        await promise2
        this.orderLoading = false
        this.orderStatus = this.orderStatuses[this.order.status].label

    },
    methods: {
        toReturnRace(){
			router.push({name: 'ReturnRace', params: {dispatch_point_id: this.order_outside.dispatchPointId, arrival_point_id: this.order_outside.arrivalPointId, 
				dispatch_station_name: this.order.tickets[0].dispatchStation, arrival_station_name: this.order.tickets[0].arrivalStation }})
		},
    }
}
</script>