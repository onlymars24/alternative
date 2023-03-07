<template>
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
									<p>{{ race.dispatchStationName }}</p>
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
									<p>{{ race.arrivalStationName }}</p>
									<!-- <p>Автовокзал Сочи, улица Горького; 56А</p> -->
								</div>
							</div>
						</div>
					</div>
					<div class="ticket-up__right">
						<div class="ticket-up__right-ins">
							<div class="right-ins__left">
								<p>{{race.price}}</p>
								<p>руб</p>
							</div>
							<div class="right-ins__right">
								<div class="right-ins__right-button">
                				<button>
                					Выбрать
                				</button>
            				</div>
            				<div class="right-ins__right-text">
            					Количество мест: {{ race.freeSeatCount }}
            				</div>
							</div>
						</div>
					</div>
				</div>
				<div class="menu__ticket-medium">
					<div class="ticket-medium__ins">
						<div class="ticket-medium__ins-left">
							<span @click="race.details_menu = !race.details_menu">Детали</span>
							<p>Перевозчик: ИП Пыльнов Игорь Анатольевич</p>
							<p>Автобус: Мерседес_20м</p>
						</div>
					</div>
				</div>
				<div class="menu__ticket-low" v-if="race.details_menu">
					<div class="ticket-low__ins">
						<div class="ticket-low__ins-up">
							<p :class="{active: race.section == 'route'}" @click="race.section = 'route'">Маршрут</p>
							<p :class="{active: race.section == 'driver'}" @click="race.section = 'driver'">Перевозчик</p>
							<p :class="{active: race.section == 'bus'}" @click="race.section = 'bus'">Автобус</p>
							<p :class="{active: race.section == 'conditions'}" @click="race.section = 'conditions'">Условия</p>
						</div>
						<template v-if="race.section == 'route'">
							<div class="ticket-low__ins-down">
								<div class="ticket-low__ins-down-first">
									<div class="ins-down-first__left">
										<p>Маршрут № <b>{{ race.num }} {{ race.name }} </b>, по маршруту <b>{{race.dispatchStationName}} — {{race.arrivalStationName}}</b></p>
										<p>Тип рейса: {{ race.type.name }}</p>
										<p>Отправление и прибытие по местному времени</p>
									</div>
									<div class="ins-down-first__right">
										<p><b>Дополнительно</b></p>
										<p>Для посадки необходим паспорт</p>
										<p>Для посадки необходим распечатанный билет</p>
									</div>
								</div>
								<div class="ticket-low__ins-down-second">
									<div class="ins-down-second__left">
										<div class="ins-down-second__left-up">
											<div class="ins-down-second__left-up-time">
												<p>{{ race.dispatchTime }}</p>
												<p>{{ race.dispatchDay }}</p>
											</div>
											<div class="ins-down-second__left-up-text">
												<p><b>{{ race.dispatchStationName }}</b></p>
												<!-- <p>Автовокзал Краснодар, площадь Привокзальная; дом 5</p> -->
											</div>
										</div>
										<div class="ins-down-second__left-up">
											<div class="ins-down-second__left-up-time">
												<p>{{ race.arrivalTime }}</p>
												<p>{{ race.arrivalDay }}</p>
											</div>
											<div class="ins-down-second__left-up-text">
												<p><b>{{ race.arrivalStationName }}</b></p>
												<!-- <p>Автовокзал Сочи, улица Горького; 56А</p> -->
											</div>
										</div>
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
							<div class="menu__ticket-low">
								<div class="ticket-low__ins">
									<!-- <div class="ticket-low__ins-up">
										<a href="">Маршрут</a>
										<a href="">Перевозчик</a>
										<a href="">Автобус</a>
										<a href="">Условия</a>
									</div> -->
									<div class="ins-down-second__ticket-second">
										<!-- <p>Бренд: ИП Пыльнов Игорь Анатольевич</p> -->
										<!-- <p>Перевозчик: ИП Пыльнов Игорь Анатольевич</p> -->
										<!-- <p>Адрес: Россия, Ставропольский кр., г. Ставрополь</p> -->
										<!-- <p>ОГРН: 311265129400063</p> -->
										<!-- <p>Время работы: Пн-Пт 10:00-17:00 (по местному времени)</p> -->
										<p>Организация перевозчика: {{ race.carrier }}</p>
										<p>{{ race.carrierInn }}</p>
										<p>{{ race.carrierPhone }}</p>
									</div>
								</div>
							</div>
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
<style>
    /* @import url('https://fonts.gstatic.com'); */
    /* @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet'); */
/* * {
	margin: 0;
	padding: 0;
	font-family: "Roboto";
	box-sizing: border-box;
	
} */
.menu{
    font-weight: 700;
}
a, a:visited {
	text-decoration: none;
	/* color: #000; */
}
ul, li {
	margin: 0;
	padding: 0;
	list-style-type: none;
}

button{
	outline: none;
	text-decoration: none;
   cursor: pointer;
   border: 1px solid transparent;
}

.container {
	width: 100%;
	max-width: 1044px;
	padding-right: 32px;
	padding-left: 32px;
	margin-right: auto;
	margin-left: auto;
}

	/* Menu */

.menu__intro p {
	padding-left: 1px;
   margin-top: 16px;
   margin-bottom: -4px;
}

.menu__intro h4 {
	margin-top: 10px;
	margin-bottom: 10px;
   font-size: 24px;
}

.menu__inro-sort {
	display: flex;
}

.inro-sort__button button{
	font-size: 14px;
	display: flex;
   -webkit-box-align: center;
   align-items: center;
   height: 38px;
   padding-right: 14px;
   padding-left: 14px;
   margin: 0px 5px;
   background-color: rgb(255, 255, 255);
   border-radius: 4px;
   outline: none;
}

.inro-sort__button button:focus {
	font-size: 14px;
	display: flex;
   -webkit-box-align: center;
   align-items: center;
   height: 38px;
   padding-right: 14px;
   padding-left: 14px;
   background-color: rgb(255, 255, 255);
   border-radius: 4px;
   color: #0275fe;
   border: 1px solid #0275fe;
}

 /* Верхний блок */

.menu__ticket {
	margin-top: 15px;
	background-color: #fff;
	position: relative;
   touch-action: manipulation;
   background-color: white;
   border-radius: 6px;
   box-shadow: rgb(0 0 0 / 15%) 0px 2px 4px;
}

.menu__ticket-up {
	display: flex;
	justify-content: space-between;
   position: relative;
}

.ticket-up__left-ins {
	display: flex;
   padding: 12px 16px;
}

.left-ins__left {
	padding-right: 20px;
	width: 360px;
}

.left-ins__left-up{
	display: flex;
   -webkit-box-align: center;
   align-items: center;
   -webkit-box-pack: justify;
   justify-content: space-between;
}

.left-up-first__time {
	display: flex;
   margin-bottom: 3px;
   font-size: 28px;
   line-height: 1;
}

.left-up-first__time-date {
	display: block;
   padding: 2px 5px 4px;
   margin-top: 2px;
   margin-left: 8px;
   font-size: 12px;
   white-space: nowrap;
   background-color: rgb(245, 245, 245);
   border-radius: 6px;
}

.left-ins__left-up-second {
	margin-right: 20px;
   font-size: 12px;
   color: rgb(83, 83, 83);
   white-space: nowrap;
}

.ticket-up__right {
	display: flex;
   flex-basis: 260px;
   -webkit-box-align: center;
   align-items: center;
   width: 260px;
   min-width: 260px;
   max-width: 260px;
   -webkit-box-pack: end;
   justify-content: flex-end;
}

.ticket-up__right-ins {
	display: flex;
   -webkit-box-align: center;
   align-items: center;
   padding: 16px;
}

.right-ins__left {
	display: flex;
   flex-direction: column;
}

.right-ins__left p:first-child {
	font-size: 28px;
}

.right-ins__left p:last-child {
	font-size: 14px;
	margin-left: 5px;
}

.right-ins__right {
	display: flex;
   position: relative;
   margin-left: 16px;
   font-size: 14px;
}

.right-ins__right-button button{
	display: inline-block;
   width: auto;
   font-weight: normal;
   text-align: center;
   text-decoration: none;
   white-space: nowrap;
   vertical-align: middle;
   touch-action: manipulation;
   cursor: pointer;
   user-select: none;
   background-image: none;
   border: 0px;
   outline: 0px;
   position: relative;
   color: rgb(255, 255, 255);
   background-color: #0275fe;
   padding: 13px 30px;
   font-size: 16px;
   border-radius: 4px;
   line-height: 20px;
   transition: all 0.28s ease-in-out 0s;
   will-change: transform;
}

.right-ins__right-text {
	position: absolute;
   bottom: -20px;
   left: 50%;
   font-size: 12px;
   line-height: 14px;
   color: #0275fe;
   white-space: nowrap;
   transform: translateX(-50%);
}

	/* ПодБлок */

.menu__ticket-medium {
	display: flex;
	align-items: center;
	justify-content: space-between;
	position: relative;
   min-height: 35px;
   font-size: 12px;
   background-color: rgb(246, 245, 245);
   border-bottom-right-radius: 6px;
   border-bottom-left-radius: 6px;
}

.ticket-medium__ins {
	-webkit-box-align: center;
   padding-left: 15px;
}

.ticket-medium__ins-left {
	display: flex;
	max-width: 630px;
}

.ticket-medium__ins-left span {
	width: 109px;
   color: #0275fe;
   white-space: nowrap;
   cursor: pointer;
}

.ticket-medium__ins-left p {
	margin-left: 30px;
}

	/* Нижний Блок */

.ticket-low__ins {
	padding: 0px 20px 20px;
   background-color: rgb(255, 255, 255);
   border-top: 2px solid rgb(245, 245, 245);
   border-bottom-right-radius: 6px;
   border-bottom-left-radius: 6px;
}

.ticket-low__ins-up {
	display: flex;
   padding: 0px;
   margin: -2px 0px 0px;
   list-style-type: none;
}

.ticket-low__ins-up p {
	padding: 13px 12px 7px;
   cursor: pointer;
   background-color: transparent;
   border-top: 2px solid transparent;
   border-bottom-right-radius: 6px;
   border-bottom-left-radius: 6px;
   outline: none;
   color: #0275fe;
   font-size: 12px;
}

.ticket-low__ins-up p:hover {
   font-weight: 500;
   color: rgb(39, 36, 36);
   background-color: rgb(255, 255, 255);
   border-color: #0275fe;
}

.ticket-low__ins-up .active{
   font-weight: 500 !important;
   color: rgb(39, 36, 36) !important;
   background-color: rgb(255, 255, 255) !important;
   border-color: #0275fe !important;
}

.ticket-low__ins-down-first {
	display: flex;
}

.ticket-low__ins-down {
	padding: 22px 12px 12px;
}

.ins-down-first__left {
	max-width: 430px;
	margin-bottom: 24px;
   font-size: 14px;
}

.ins-down-first__left p {
	margin-bottom: 8px;
   line-height: 1.29;
}

.ins-down-first__right {
	margin-left: 80px;
	margin-bottom: 24px;
   font-size: 14px;
}

.ins-down-first__right p {
	margin-bottom: 8px;
   line-height: 1.29;
}

.ticket-low__ins-down-second {
	display: flex;
	font-size: 14px;
}

.ins-down-second__left {
	max-width: 430px;
}

.ins-down-second__left-up {
	position: relative;
   display: flex;
   margin-bottom: 16px;
}

.ins-down-second__left-up-time {
	width: 50px;
   min-width: 50px;
   margin-right: 35px;
}

.ins-down-second__left-up-time p:first-child {
	font-size: 14px;
   font-weight: 500;
   line-height: 1.29;
}

.ins-down-second__left-up-time p:last-child {
	margin-top: 5px;
   font-size: 12px;
   line-height: 1.17;
}

.ins-down-second__left-up-text p:first-child {
	position: relative;
   margin-bottom: 3px;
   font-size: 14px;
   font-weight: 500;
   line-height: 1.29;
   word-break: break-all;
}

.ins-down-second__left-up-text p:last-child {
	line-height: 1.29;
}

.ins-down-second__right {
	max-width: 430px;
	margin-left: 80px;
}

.ins-down-second__right p {
	margin-top: 10px;
	margin-bottom: 10px;
}

.ins-down-second__ticket-second {
	padding: 22px 12px 12px;
	font-size: 14px;
}

.ins-down-second__ticket-second p {
	margin-top: 10px;
}

.ticket-low__ins-fourth {
   padding-top: 20px;
}

.ticket-low__ins-fourth a {
	color: rgb(249, 37, 63);
   cursor: pointer;
}

	/* ******* ВТОРОЙ СЕКТОР ******** */

.head__menu-choose {
	display: flex;
	padding: 0px 32px;
}

.head__menu-choose p:nth-of-type(1) {
	display: inline-block;
   height: 100%;
   padding: 14px 0px;
   font-size: 16px;
   color: rgb(39, 36, 36);
   vertical-align: middle;
}

.head__menu-choose p:nth-of-type(2) {
	margin-left: 40px;
	display: inline-block;
   height: 100%;
   padding: 14px 0px;
   font-size: 16px;
   color: rgb(39, 36, 36);
   vertical-align: middle;
}

.head__menu-choose p:nth-of-type(3) {
	margin-left: 40px;
	display: inline-block;
   height: 100%;
   padding: 14px 0px;
   font-size: 16px;
   color: rgb(39, 36, 36);
   vertical-align: middle;
}

.ins-down-second__right a {
   color: #0275fe;
   white-space: nowrap;
   cursor: pointer;
}

.ticket-low__ins-one {
	display: flex;
	justify-content: flex-end;
}

.ticket-low__ins-one a {
	color: #0275fe;
   white-space: nowrap;
   cursor: pointer;
   font-size: 14px;
}

.info__ins {
	background-color: #fff;
   padding: 32px 40px 44px;
   margin-top: 20px;
   border-radius: 6px;
   -webkit-box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
   box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}

.info__text {
	text-align: center;
}

.info__text p:nth-of-type(1) {
	font-size: 20px;
}

.info__text p:nth-of-type(2) {
	font-size: 14px;
	margin-top: 10px;
}

.info__text span {
	color: #818682;
}

.info__text p:nth-of-type(3) {
	margin-top: 30px;
	margin-bottom: 30px;
	font-size: 26px;
}

.info__button {
	display: flex;
	justify-content: center;
}

.info__button button {
   width: auto;
   font-weight: normal;
   text-align: center;
   text-decoration: none;
   white-space: nowrap;
   vertical-align: middle;
   touch-action: manipulation;
   cursor: pointer;
   user-select: none;
   background-image: none;
   border: 0px;
   outline: 0px;
   position: relative;
   color: rgb(255, 255, 255);
   background-color: #0275fe;
   padding: 13px 30px;
   font-size: 14px;
   border-radius: 4px;
   line-height: 20px;
   transition: all 0.28s ease-in-out 0s;
   will-change: transform;
}

	/* ******  ФОРМА  ******** */

.ins-down-first__left-menu {
	font-size: 16px;
   line-height: 20px;
   margin-bottom: 2px;
}

.ins-down-first__left-menu p:last-child {
	color: #a7a7a7;
    line-height: 18px;
    text-transform: lowercase;
    font-size: 14px;
    margin-top: 5px;
}

.ticket-low__ins-down-first-menu {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.ins-down-first__right-menu a {
	color: #0275fe;
	font-size: 14px;
}

.ticket__form {
	margin-top: 50px;
}

.ticket__form-main {
	margin-bottom: 16px;
   padding: 32px;
   background-color: #fff;
   border-radius: 6px;
   -webkit-box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
   box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}

.ticket__form-text p:first-child {
	font-size: 26px;
	font-weight: 400;
}

.ticket__form-text p:first-child {
	margin-top: 5px;
	margin-bottom: 20px;
}

	/* ******  Подтверждение  ******* */



.agree__block {
	max-width: 570px;
	position: relative;
   padding: 32px;
   background-color: rgb(255, 255, 255);
   background-clip: padding-box;
   border: 1px solid rgb(167, 167, 167);
   border-radius: 12px;
}

.agree__block-text {
	display: flex;
   flex-flow: column;
   -webkit-box-pack: center;
   justify-content: center;
   font-size: 18px;
}

.agree__block-text p:nth-of-type(1) {
	font-size: 14px;
}

.agree__block-text p:nth-of-type(2) {
	text-align: center;
	margin-top: 20px;
}

.agree__block-text p:nth-of-type(3) {
	text-align: center;
	margin-top: 20px;
}

.agree__block-text a {
	text-align: center;
	font-size: 14px;
	color: #0275fe;
	margin-top: 20px;
}

.agree__block-input {
	display: flex;
	justify-content: center;
	margin-top: 40px;
}

.agree__block-input input {
	width: 164px;
   height: 56px;
   padding: 10px 25px;
   font-size: 27px;
   letter-spacing: 19px;
   border: 1px solid rgb(204, 204, 204);
   border-radius: 4px;
   outline: none;
}

.agree__block-text a:last-child {
	text-align: center;
	font-size: 14px;
	color: #0275fe;
	margin-top: 60px;
}

	/* **** Места в автобусе **** */

.bus__main {
	display: flex;
	flex-direction: column;
	align-items: center;
	margin-top: 15px;
	background-color: #fff;
	position: relative;
   touch-action: manipulation;
   background-color: white;
   border-radius: 6px;
   box-shadow: rgb(0 0 0 / 15%) 0px 2px 4px;
}

.bus__text p:first-child {
	font-size: 20px;
	text-align: center;
}

.bus__text p:last-child {
	margin-top: 20px;
	font-size: 14px;
	text-align: center;
}

.bus__text span {
	color: #818682;
}

.bus__main-sit {
   display: flex;

   align-items: center;
   justify-content: center;
   margin-bottom: 15px;
   margin-top: 12px;
}

.box_free {
	width: 18px;
   height: 18px;
   border: 1px solid transparent;
   display: inline-block;
   vertical-align: middle;
   border-radius: 4px;
   border-color: #32c453;
   margin-right: 5px;
}

.box_busy {
	width: 18px;
   height: 18px;
   border: 1px solid transparent;
   display: inline-block;
   vertical-align: middle;
   border-radius: 4px;
   border-color: #ececec;
   background-color: #ececec;
   margin-right: 5px;
   margin-left: 40px;
}

.bus__block {
	margin-top: 40px;
	max-width: 500px;
   display: flex;
   -webkit-box-orient: vertical;
   -webkit-box-direction: normal;
   padding: 20px 16px 20px 16px;
   border: 1px solid #ccc;
   border-radius: 30px;
}

.bus__block-button button {
	width: 40px;
   height: 40px;
   border: 1px solid transparent;
   display: flex;
   align-items: center;
   justify-content: center;
   border-radius: 4px;
   border-color: #32c453;
   margin-right: 10px;
   background-color: #fff;
   font-size: 16px;
}

.bus__block-button button:hover {
	width: 40px;
   height: 40px;
   border: 1px solid transparent;
   display: flex;
   align-items: center;
   justify-content: center;
   border-radius: 4px;
   border-color: #32c453;
   margin-right: 10px;
   color: #fff;
   background-color: #32c453;
   font-size: 16px;
}

@media screen and (min-width: 768px) and (max-width: 992px){
	.left-ins__left-down {
		font-size: 12px;
	}

	.left-ins__left {
		width: 330px;
	}

	.ticket-up__right-ins {
		display: block;
		text-align: center;
	}

	.ticket-up__right {
		display: block;
		text-align: center;
		height: 150px;
		flex-basis: 200px;
      width: 200px;
      min-width: 200px;
      max-width: 200px;
	}

	.ins-down-first__left {
    	max-width: 310px;
   }
}

@media screen and (min-width: 320px) and (max-width: 767px){
	.ticket-low__ins-down-first {
		display: block;
	}

	.ins-down-first__right {
		margin-left: 0px;
	}

	.ticket-low__ins-down-second {
		display: block;
	}

	.ins-down-second__right {
		margin-left: 0px;
	}

	.info__text p:nth-of-type(3) {
		font-size: 20px;
	}
}
</style>

<script>
	export default{
		props: ['race'],
		data(){
			return{

			}
		}
	}
</script>