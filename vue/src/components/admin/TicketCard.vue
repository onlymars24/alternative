<template>
    <el-card class="box-card" style="width: 370px;">
        <template #header>
        <div class="card-header" style="display: flex; justify-content: space-between;">
            <span>Билет c ID: {{ticket.id}}</span>
            <el-link v-if="ticket.order_id" :href="'order/'+ticket.order_id">
                Весь заказ<i class="el-icon-top-right el-icon--right"></i>
            </el-link>
        </div>
        </template>
        <div class="text item" v-if="ticket.order_id"><strong>Заказ с ID:</strong> {{ticket.order_id}}</div>
        <div class="text item"><strong>Статус:</strong> {{ ticketStatuses[ticket.status].label }}</div>
        <div class="text item"><strong style="margin-right: 5px;">Билет:</strong>
            <el-link v-if="ticket.status == 'S' || ticket.status == 'R'" type="primary" :href="baseUrl+'/tickets/'+ticket.hash+'.pdf'" target="_blank" alt="">
                PDF<i class="el-icon-tickets el-icon--right"></i>
            </el-link>
        </div>
        <div class="text item"><strong style="margin-right: 5px;">Квитанция о возврате:</strong>
            <el-link type="primary" v-if="ticket.status == 'R'" :href="baseUrl+'/tickets/'+ticket.hash+'_r.pdf'" target="_blank" alt="">
                PDF<i class="el-icon-tickets el-icon--right"></i>
            </el-link>
        </div>
        <div class="text item"><strong>Пункт отправления:</strong> {{ ticket.dispatchStation }}</div>
        <div class="text item"><strong>Пункт прибытия:</strong> {{ ticket.arrivalStation }}</div>
        <div class="text item"><strong>Дата отправления:</strong> {{ ticket.dispatchDate }}</div>
        <div class="text item"><strong>Дата рождения:</strong> {{ ticket.birthday }}</div>
        <div class="text item"><strong>Серия билета:</strong> {{ ticket.ticketSeries }}</div>
        <div class="text item"><strong>Номер билета:</strong> {{ ticket.ticketNum }}</div>
        <div class="text item"><strong>Серия документа:</strong> {{ ticket.docSeries }}</div>
        <div class="text item"><strong>Номер документа:</strong> {{ ticket.docNum }}</div>
        

        <div class="text item"><strong>Код валюты поставщика(автовокзала):</strong> {{ ticket.supplierCurrencyCode }}</div>
        <div class="text item"><strong>Тариф поставщика(автовокзала):</strong> {{ ticket.supplierFare }}</div>
        <div class="text item"><strong>Сборы поставщика(автовокзала):</strong> {{ ticket.supplierDues }}</div>
        <div class="text item"><strong>Цена поставщика(автовокзала):</strong> {{ ticket.supplierPrice }}</div>
        <div class="text item"><strong>Сумма возврата на автовокзале:</strong> {{ ticket.supplierRepayment }}</div>
        <div class="text item"><strong>Код валюты агента:</strong> {{ ticket.currencyCode }}</div>
        <div class="text item"><strong>Сбор агента:</strong> {{ ticket.dues }}</div>

        <div class="text item"><strong>Конечная цена билета:</strong> {{ ticket.price }}</div>
        <div class="text item"><strong>НДС:</strong> {{ ticket.vat }}</div>
        <div class="text item"><strong>Сумма, подлежащая возврату покупателю:</strong> {{ ticket.repayment }}</div>

        <div class="text item"><strong>Тип билета:</strong> {{ ticket.ticketType }}</div>
        <div class="text item"><strong>ФИО:</strong> {{ ticket.lastName }} {{ ticket.firstName }} {{ ticket.middleName }}</div>
        <div class="text item"><strong>Телефон:</strong> {{ ticket.phone }}</div>
    </el-card>
</template>
<script>
import { ref } from "vue";
import router from '../../router'
import dayjs from 'dayjs'

export default
{
    props: ['ticket', 'ticketStatuses'],
    data() {
        return {
            baseUrl: ''
        }
    },
    methods: {

    },
    mounted(){
        this.baseUrl = import.meta.env.VITE_API_BASE_URL
        this.ticket.dispatchDate = dayjs(this.ticket.dispatchDate).format('YYYY-MM-DD hh:mm')
        this.ticket.birthday = dayjs(this.ticket.birthday).format('YYYY-MM-DD')
    }
}
</script>
<style>

</style>