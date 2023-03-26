<!-- eslint-disable -->
<template>
  <!-- <button @click="$emit('toSeats', race.uid)">Тест</button> -->
   <div class="menu__ticket">
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
			   <p class="race__address">{{ race.dispatchStationName }}</p>
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
			   <p class="race__address">{{ race.arrivalStationName }}</p>
			   <!-- <p>Автовокзал Сочи, улица Горького; 56А</p> -->
			 </div>
		   </div>
		 </div>
	   </div>
	   <div class="ticket-up__right" v-if="true">
		 <div class="ticket-up__right-ins">
		   <div class="right-ins__left">
			 <p>{{ race.price }}</p>
			 <p>руб</p>
		   </div>
		   <div class="right-ins__right">
			 <div class="right-ins__right-button">
			   <button @click="tickets = !tickets" class="buy__but" >
				  Список билетов 
			   </button>
			 </div>
       <ul class="tickets-list" v-show="tickets">
          <li><a href="">Место 43</a></li>
          <li><a href="">Место 44</a></li>
          <li><a href="">Место 43</a></li>
          <li><a href="">Место 44</a></li>
          <li><a href="">Место 43</a></li>
         </ul>
		   </div>
		 </div>
	   </div>
	 </div>
	 <div class="menu__ticket-medium">
	   <div class="ticket-medium__ins">
		 <div class="ticket-medium__ins-left">
		   <button class="race__details__but" @click="race.details_menu = !race.details_menu">
			 {{ race.details_menu ? "Скрыть детали" : "Детали рейса" }}
		   </button>
		   <p>Перевозчик: ИП Пыльнов Игорь Анатольевич</p>
		   <p>Автобус: Мерседес_20м</p>
		 </div>
	   </div>
	 </div>
	 <div class="menu__ticket-low" v-if="race.details_menu">
	   <div class="ticket-low__ins">
		 <div class="ticket-low__ins-up">
		   <p :class="{ active: race.section == 'route' }" @click="race.section = 'route'">
			 Маршрут
		   </p>
		   <p :class="{ active: race.section == 'driver' }" @click="race.section = 'driver'">
			 Перевозчик
		   </p>
		   <p :class="{ active: race.section == 'bus' }" @click="race.section = 'bus'">Автобус</p>
		   <p :class="{ active: race.section == 'conditions' }" @click="race.section = 'conditions'">
			 Условия
		   </p>
		 </div>
		 <template v-if="race.section == 'route'">
		   <div class="ticket-low__ins-down">
			 <div class="ticket-low__ins-down-first">
			   <div class="ins-down-first__left">
				 <p>
				   Маршрут № <b>{{ race.num }} {{ race.name }} </b>, по маршруту
				   <b>{{ race.dispatchStationName }} — {{ race.arrivalStationName }}</b>
				 </p>
				 <p>Тип рейса: {{ race.type.name }}</p>
				 <p>Отправление и прибытие по местному времени</p>
			   </div>
			  
			   <div class="Departure-Arrival"><DepartureArrival /></div>
			 </div>
			 <div class="ticket-low__ins-down-second">
				<div class="ins-down-first__right">
				 <p><b>Дополнительно</b></p>
				 <p>Для посадки необходим паспорт</p>
				 <p>Для посадки необходим распечатанный билет</p>
			   </div>
				
			   <div class="ins-down-second__right">
				 <p><b>Перевозчик</b></p>
				 <p>Бренд: ИП Пыльнов Игорь Анатольевич</p>
				 <p>Автобус: Мерседес_20м</p>
				 <p>Перевозчик: ИП Пыльнов Игорь Анатольевич</p>
				 <p>Адрес: Россия, Ставропольский кр., г. Ставрополь</p>
				 <p>ОГРН: 311265129400063</p>
				 <p>Время работы: Пн-Пт 10:00-17:00 (по местному времени)</p>
			   </div>
			 </div>
		   </div>
		 </template>
 
		 <template v-if="race.section == 'driver'">
		  <TicketLow :race="race"/>
		 </template>
 
		 <template v-if="race.section == 'bus'">
		   <div class="ticket-low__ins">
			 <div class="ticket-low__ins-down">
			   <div class="ticket-low__ins-down-first">
				 <div class="ins-down-first__left">
				   <p>Модель автобуса: Ик-250_42м</p>
				   <p>{{ race.busInfo }}</p>
				 </div>
				 <div class="ins-down-first__right">
				   <p><b>Условия на рейсе</b></p>
				   <p>Для посадки необходим паспорт</p>
				   <p>Для посадки необходим распечатанный билет</p>
				 </div>
			   </div>
			 </div>
		   </div>
		 </template>
 
		 <template v-if="race.section == 'conditions'">
		   <div class="ticket-low__ins">
			 <div class="ticket-low__ins-fourth">
			   <a href="#">Условия возврата</a>
			 </div>
		   </div>
		 </template>
	   </div>
	 </div>
   </div>
 </template>
<script>
	import DepartureArrival from './DepartureArrival.vue';
	import TicketLow from './TicketLow.vue';
	import router from '../router'
	export default{
		components: { DepartureArrival, TicketLow },
		props: ['race', 'button_status'],
    emits: ['toSeats'],
		data(){
			return{
				// race:  {
				// 	"uid": "1770206:554599:20230311:650:275",
				// 	"depotId": 1770206,
				// 	"num": "568л",
				// 	"name": "Новосибирск АВ Главный - Северное",
				// 	"description": "льготный",
				// 	"dispatchDate": "2023-03-11 11:40:00",
				// 	"arrivalDate": "2023-03-11 17:15:00",
				// 	"dispatchStationName": "Новосибирск АВ-Главный",
				// 	"arrivalStationName": "Барабинск пов.",
				// 	"dispatchPointId": 1770206,
				// 	"arrivalPointId": 1770221,
				// 	"supplierPrice": 1130,
				// 	"price": 1130,
				// 	"freeSeatCount": 45,
				// 	"freeSeatEstimation": "25+",
				// 	"busInfo": "45 мест",
				// 	"carrier": "Северноеагротранс ОАО АТП",
				// 	"carrierInn": "5435100070",
				// 	"carrierPhone": null,
				// 	"principal": "Артмарк",
				// 	"principalInn": "2221122730",
				// 	"dataRequired": false,
				// 	"type": {
				// 	"id": 1,
				// 	"name": "Междугородный"
				// 	},
				// 	"clazz": {
				// 	"id": 1,
				// 	"name": "Регулярный"
				// 	},
				// 	"status": {
				// 	"id": 1,
				// 	"name": "Продажа"
				// 	},
				// 	"fromCache": true,
				// 	"section": "route",
				// 	"details_menu": true,
				// 	"dispatchDay": "11 апр.",
				// 	"arrivalDay": "11 апр.",
				// 	"dispatchTime": "11:40",
				// 	"arrivalTime": "17:15"
				// }
        tickets:false
			}
		},
    computed:{

    },
    methods: {

    },
    mounted(){
      
    }

	}
</script>
<style>

.tickets-list
{
  position: absolute;
  z-index: 10;
  background-color: white;
  padding: 10px;
  border-radius: 10px;
  top: 0px;
  right: 190px;
  width: 100px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}
@media (max-width:550px)
{
  .tickets-list
{
  right: 150px;
  top: 60px;
}
}
</style>