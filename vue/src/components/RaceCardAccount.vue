<!-- eslint-disable -->
<template>
  <!-- <button @click="$emit('toSeats', race.uid)">Тест</button> -->
  <!-- <pre>{{ this.race.order }}</pre> -->
   <div class="menu__ticket" style="box-shadow: rgb(0 0 0 / 15%) 0px 2px 8px;">
	
	 <div class="menu__ticket-medium" style="border-top-right-radius: 6px; border-top-left-radius: 6px;">
	   <div class="ticket-medium__ins" style="width: 100%;">
		 <div class="ticket-medium__ins-left ticket-medium__ins-left__up" style="display: block; max-width: none;">
		   	<div style="display: flex; justify-content: space-between; width: 100%;">
				<div><strong>ID: </strong> {{ race.order.id }}</div>
				<div style="margin: 0; margin-right: 10px;">
					<p v-if="race.order.status == 'S'" style="color: green; font-size: 12px;"><strong>Заказ оплачен</strong></p>
					<p style="color: #dc3545;" v-if="race.order.status == 'B' && !expired">Заказ забронирован, но не оплачен!</p>
					<p v-if="race.order.status == 'R'">Заказ возвращён</p>
					<p v-if="race.order.status == 'P'">Заказ частично возвращён</p>
					<p style="color: #dc3545;" v-if="race.order.status == 'B' && expired">Время ожидания оплаты истекло!</p>			
				</div>				
			</div>
			<div class="ticket-medium__ins-left__up-date" v-if="race.order.status == 'S'" style="margin: 0;">
				<strong>Заказ подтверждён: </strong> {{ race.order.finished }} (по местному времени)
		   	</div>
		 </div>
	   </div>
	 </div>


	 <div class="menu__ticket-up">
	   <div class="ticket-up__left">
		 <div class="ticket-up__left-ins">
			
		   <div class="left-ins__left">
			 <div class="left-ins__left-up">
			   <div class="left-ins__left-up-first">
				 <div class="left-up-first__time">
				   <p>{{ race.dispatchTime }}</p>
				   <div class="left-up-first__time-date">
					 <p>{{ race.dispatchDay }}</p>
				   </div>
				 </div>
			   </div>
			   <div class="left-ins__left-up-second">
				 <!-- <p>7 ч. 3 мин. в пути</p> -->
			   </div>
			 </div>
			 <div class="left-ins__left-down">
			   <p class="race__address">{{ race.order.tickets[0].dispatchStation }}</p>
			   <!-- <p>Автовокзал Краснодар, площадь Привокзальная; дом 5</p> -->
			 </div>
		   </div>
		   <div class="left-ins__right">
			 <div class="left-ins__left-up">
			   <div class="left-ins__left-up-first">
				 <div class="left-up-first__time">
				   <p>{{ race.arrivalTime }}</p>
				   <div class="left-up-first__time-date">
					 <p>{{ race.arrivalDay }}</p>
				   </div>
				 </div>
			   </div>
			 </div>
			 <div class="left-ins__left-down">
			   <p class="race__address">{{ race.order.tickets[0].arrivalStation }}</p>
			   <!-- <p>Автовокзал Сочи, улица Горького; 56А</p> -->
			 </div>
		   </div>
		 </div>
		 
	   </div>
	   <div class="ticket-up__right">
		<!-- <p>Заказ оформлен: 341234</p> -->
		 <div class="ticket-up__right-ins">
			
		   <div class="right-ins__left" style="min-width: 113px;">
			 <p>{{ race.order.total + duePrice + order.insurancePrice - order.bonusesPrice}}</p>
			 <p>руб</p>
		   </div>
		   <div class="right-ins__right" v-if="!expired">
			 <div class="right-ins__right-button">
			   <button v-if="(race.order.status == 'S' || race.order.status == 'P' || race.order.status == 'R') && order.dispatchPointId && order.arrivalPointId" @click="toReturnRace" class="buy__but" style="background-color: #7CAE5E;">
				Обратный билет
			   </button>
			   <button v-if="race.order.status == 'B'" @click="toPayment" class="buy__but" style="background: #dc3545;">
				  Оплатить 
			   </button>
			 </div>
			<!-- <ul class="tickets-list" v-show="tickets">
				<li v-for="ticket in race.order.tickets"><a :href="ticket.status == 'R' ? baseUrl+'/tickets/'+ticket.hash+'_r.pdf' : baseUrl+'/tickets/'+ticket.hash+'.pdf'" target="_blank">{{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}}</a></li>
			</ul> -->
		   </div>
		 </div>
	   </div>
	 </div>
	 <div class="menu__ticket-medium" style="justify-content: flex-start;">
	   <div class="ticket-medium__ins" style="padding-left: 10px;">
		 <div class="ticket-medium__ins-left">
			<div v-if="race.order.status == 'S' || race.order.status == 'P' || race.order.status == 'R'" class="">
				<a @click.prevent="tickets = !tickets" href="">Список билетов</a>
			</div>		 
			<ul class="tickets-list" v-show="tickets">
				<li v-for="ticket in race.order.tickets"><a :href="ticket.status == 'R' ? baseUrl+'/tickets/'+ticket.hash+'_r.pdf' : baseUrl+'/tickets/'+ticket.hash+'.pdf'" target="_blank">{{ticket.lastName}} {{ticket.firstName}} {{ticket.middleName}} Место {{ticket.seat}}</a></li>
			</ul>  
		   <div v-if="race.order.status == 'S' || race.order.status == 'P' || race.order.status == 'R'" class=""><a href="" @click.prevent="popupTransactionsOpen = true, getTransactions()">Чеки</a></div>
		   <div v-if="order.insured && (race.order.status == 'S' || race.order.status == 'P' || race.order.status == 'R')"><a href="" @click.prevent="popupInsurancesOpen = true, getInsurances()" class="">Страховки</a></div>

   
		 </div>

	   </div>

		<div v-if="!wentOut && (race.order.status == 'S' || race.order.status == 'P')"><a href="" @click.prevent="popupOpen = true" class="">Вернуть</a></div>
	 </div>
   </div>
   	<template v-if="popupOpen">
        <PopupWindow @CloseWindow="popupOpen = false; returnInfo.step = 1;" :content="4" :order="race.order" @returnTicket="returnTicket" @returnOrder="returnOrder" :returnInfo="returnInfo"/>
	</template>
	<template v-if="popupTransactionsOpen">
        <PopupWindow @CloseWindow="popupTransactionsOpen = false;" :content="5" :order="race.order" :returnTransactionsInfo="returnTransactionsInfo"/>
	</template>
	<template v-if="popupInsurancesOpen">
        <PopupWindow @CloseWindow="popupInsurancesOpen = false;" :content="6" :order="race.order" :insurancesInfo="insurancesInfo"/>
	</template>
 </template>
<script>
	import DepartureArrival from './DepartureArrival.vue';
	import TicketLow from './TicketLow.vue';
	import router from '../router'
	import PopupWindow from '../components/PopupWindow.vue';
	import dayjs from 'dayjs';
	import utc from 'dayjs/plugin/utc';
	import timezone from 'dayjs/plugin/timezone';
	import axiosClient from '../axios';
	export default{
		components: { DepartureArrival, TicketLow, PopupWindow },
		props: ['button_status', 'order'],
    	emits: ['toSeats', 'updateOrders'],
		data(){
			return{
				race:  {
					"uid": "1770206:554599:20230311:650:275",
					"depotId": 1770206,
					"num": "568л",
					"name": "Новосибирск АВ Главный - Северное",
					"description": "льготный",
					"dispatchDate": "2023-03-11 11:40:00",
					"arrivalDate": "2023-03-11 17:15:00",
					"dispatchStationName": "Новосибирск АВ-Главный",
					"arrivalStationName": "Барабинск пов.",
					"dispatchPointId": 1770206,
					"arrivalPointId": 1770221,
					"supplierPrice": 1130,
					"price": 1130,
					"freeSeatCount": 45,
					"freeSeatEstimation": "25+",
					"busInfo": "45 мест",
					"carrier": "Северноеагротранс ОАО АТП",
					"carrierInn": "5435100070",
					"carrierPhone": null,
					"principal": "Артмарк",
					"principalInn": "2221122730",
					"dataRequired": false,
					"type": {
					"id": 1,
					"name": "Междугородный"
					},
					"clazz": {
					"id": 1,
					"name": "Регулярный"
					},
					"status": {
					"id": 1,
					"name": "Продажа"
					},
					"fromCache": true,
					"section": "route",
					"details_menu": false,
					"dispatchDay": "11 апр.",
					"arrivalDay": "11 апр.",
					"dispatchTime": "11:40",
					"arrivalTime": "17:15",
					order: JSON.parse(this.order.order_info),
					timezone: this.order.timezone
				}, 
				windowOpen: 0,
				popupOpen: false,
				popupTransactionsOpen: false,
				popupInsurancesOpen: false,
				months: [
					'', 'янв.', 'февр.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сент.', 'окт.', 'ноябр.', 'дек.', 
				],
        		tickets: false,
				returnInfo: {
					step: 1,
					status: null,
					loading: false,
					response: [  ]
				},
				returnTransactionsInfo: {
					loading: false,
					response: [  ]
				},
				insurancesInfo: {
					loading: false,
					response: [  ]
				},
				expired: false,
				wentOut: false,
				baseUrl: '',
				duePrice: this.order.duePrice
			}
		},
    computed:{

    },
    methods: {
		toPayment(){
			// router.push({name: 'Payment', params: {order_id: this.order.id}})
			window.open(this.order.formUrl, '_self');
		},
		toReturnRace(){
			router.push({name: 'ReturnRace', params: {dispatch_point_id: this.order.dispatchPointId, arrival_point_id: this.order.arrivalPointId, 
				dispatch_station_name: this.race.order.tickets[0].dispatchStation, arrival_station_name: this.race.order.tickets[0].arrivalStation, order_id: this.order.id }})
		},
		async returnOrder(orderId){
			if(!confirm('Вы уверены, что хотите вернуть билет? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
			this.returnInfo.loading = true
			this.returnInfo.step = 2
			const promise = axiosClient
			.post('/order/return', {orderId: orderId})
			.then(response => {
				console.log(response)
			})
			.catch(error => {
				this.returnInfo.step = 3
				console.log(error)
			})
			await promise
			this.returnInfo.loading = false
		},
		async returnTicket(ticketId, orderId){
			if(!confirm('Вы уверены, что хотите вернуть билет? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
			this.returnInfo.loading = true
			this.returnInfo.step = 2
			const promise = axiosClient
			.post('/ticket/return', {ticketId: ticketId, orderId: orderId})
			.then(response => {
				console.log(response)
			})
			.catch(error => {
				this.returnInfo.step = 3
				console.log(error)
			})
			await promise
			this.returnInfo.loading = false
		},
		async getTransactions(){
			this.returnTransactionsInfo.loading = true
			const promise = axiosClient
			.post('/order/transactions', {orderId: this.race.order.id})
			.then(response => {
				console.log(response.data.transactions)
				this.returnTransactionsInfo.response = response.data.transactions
			})
			.catch(error => {
				console.log(error)
			})
			await promise
			this.returnTransactionsInfo.loading = false
		},
		async getInsurances(){
			this.insurancesInfo.loading = true
			const promise = axiosClient
			.get('/order/tickets?orderId='+this.race.order.id)
			.then(response => {
				console.log(response.data.tickets)
				this.insurancesInfo.response = response.data.tickets
				this.insurancesInfo.response.forEach(ticket => {
					ticket.insurance = JSON.parse(ticket.insurance)
				})
				console.log(this.insurancesInfo.response)
			})
			.catch(error =>{
				console.log(error)
			})
			await promise
			this.insurancesInfo.loading = false
		}
    },
    mounted(){
		this.baseUrl = import.meta.env.VITE_API_BASE_URL
		// console.log(this.race.order.tickets[0].dispatchDate)
		this.race.dispatchDay = this.race.order.tickets[0].dispatchDate ? dayjs(this.race.order.tickets[0].dispatchDate).format('D')+' '+this.months[dayjs(this.race.order.tickets[0].dispatchDate).format('M')] : ''
		this.race.arrivalDay = this.race.order.tickets[0].arrivalDate ? dayjs(this.race.order.tickets[0].arrivalDate).format('D')+' '+this.months[dayjs(this.race.order.tickets[0].arrivalDate).format('M')] : ''
		this.race.dispatchTime = this.race.order.tickets[0].dispatchDate ? dayjs(this.race.order.tickets[0].dispatchDate).format('HH:mm') : ''
		this.race.arrivalTime = this.race.order.tickets[0].arrivalDate ? dayjs(this.race.order.tickets[0].arrivalDate).format('HH:mm') : ''
		let bookTime = dayjs(dayjs(this.order.created_at).format('YYYY-MM-DDTHH:mm:ss'))
		let nowTime = dayjs(dayjs().format('YYYY-MM-DDTHH:mm:ss'))
		let nowTimeForDispatch = dayjs().format('YYYY-MM-DD HH:mm:ss')
		let differenceForBook = nowTime.diff(bookTime) / 60000

        dayjs.extend(utc);
        dayjs.extend(timezone);
        let timeZone = this.race.timezone
        let currentTimeForRaceTimezone = dayjs(dayjs().tz(timeZone).format('YYYY-MM-DD HH:mm:ss'));
		let dispatchTime = dayjs(this.race.order.tickets[0].dispatchDate)
		let differenceForDispatch = currentTimeForRaceTimezone.diff(dispatchTime) / 60000


		if(differenceForBook > 20 && this.race.order.status == 'B'){
			this.expired = true
		}
		if(differenceForDispatch > 3 * 60 || (differenceForDispatch < 30 && differenceForDispatch > -15)){
			this.wentOut = true
		}
		console.log(this.wentOut)
    }
	}
</script>
<style scoped>

strong{
	margin: 0;
}

.tickets-list
{
  position: absolute;
  z-index: 5;
  background-color: white;
  padding: 10px;
  border-radius: 10px;
  top: 35px;
  left: 0px;
  width: 280px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}

.ticket-up__left-ins{
	padding: 12px 25px;
	justify-content: space-between;
}
.ticket-medium__ins-left__up div{
	margin: 0;
	margin-right: 15px;
	margin-bottom: 5px;
}
@media (max-width:550px)
{
.tickets-list
{
  left: 0px;
  top: 40px;
}
.ticket-up__left-ins{
	padding: 12px 16px;
	justify-content: space-around;
}

.ticket-medium__ins-left__up{
	display: flex;
	flex-direction: column;
}
.ticket-medium__ins-left__up-date{
	display: flex;
	flex-direction: column;
}


}
</style>