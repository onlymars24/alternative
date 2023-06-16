<template>
    <div class="common-layout">
        <div class="container" v-loading.fullscreen.lock="orderLoading">  
            <Header/>
            <el-container v-if="!orderLoading">
                <el-main>
                    <el-descriptions :title="'Информация о заказе с ID: '+order.id" direction="vertical" :column="4" border>
                        <el-descriptions-item label="Сумма заказа">{{order.total}}</el-descriptions-item>
                        <el-descriptions-item label="Сумма возврата">{{order.repayment}}</el-descriptions-item>
                        <el-descriptions-item label="Статус заказа" :span="2">{{ orderStatus }}</el-descriptions-item>
                        <el-descriptions-item label="Дата создания заказа">{{order.created}}</el-descriptions-item>
                        <el-descriptions-item label="Истечение срока действия неоплаченного заказа">{{order.expired}}</el-descriptions-item>
                        <el-descriptions-item label="Завершение обработки заказа">{{order.finished}}</el-descriptions-item>
                    </el-descriptions>
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
            order: [],
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
        const promise = axiosClient
        .get('/order?order_id='+this.order_id)
        .then(response => {
            this.order = JSON.parse(response.data.order.order_info)
            console.log(this.order)
        })
        await promise
        this.orderLoading = false
        this.orderStatus = this.orderStatuses[this.order.status].label

    }
}
</script>