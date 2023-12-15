<template>
    <div class="common-layout">
        <div class="container" v-loading.fullscreen.lock="orderLoading">
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
                    </el-descriptions>
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
                            <TicketCard v-for=" ticket in order.tickets" :ticket="ticket" :ticketStatuses="ticketStatuses"/>
                        </el-space>
                    </div>
                </el-main>
            </el-container>
        </div>
    </div>
</template>
<script>
import axiosClient from '../../axios';
import Header from '../../components/admin/Header.vue'
import TicketCard from '../../components/admin/TicketCard.vue'
import ticketStatuses from '../../data/TicketStatuses';

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
            orderStatus: ''
        }
    },
    async mounted(){
        this.orderLoading = true
        const promise1 = axiosClient
        .get('/order?order_id='+this.order_id)
        .then(response => {
            this.order_outside = response.data.order
            this.order = JSON.parse(response.data.order.order_info)
            console.log(this.order)
        })
        await promise1
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

    }
}
</script>