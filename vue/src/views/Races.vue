<template>
<HeaderMain @changeRaces="changeRaces0" :arrivalEl0="arrivalEl" :dispatchEl0="dispatchEl" :date0="date"/>
<div>
    <!-- RACES <pre>{{ races }}</pre> -->
    <div class="menu" style="margin-top: 50px;">
		<div class="container">
            <div v-if="loadingRaces" class="loader__outside">
                <img src="../assets/bus_loading.png">
                <p style="color: grey;">Загрузка.....</p>  
                <div class="loader"></div>
            </div>
            <div v-else-if="!races.length" class="not__found">
                <div class="not__found-img">
                    <img src="../assets/free-icon-sad-3350122.png">
                </div>
                <div class="not__found-text">
                    <p class="not__found-title">
                        Билеты не найдены
                    </p>
                    <p class="not__found-descr">
                        К сожалению, такого маршрута у нас нет. Мы делаем всё возможное, чтобы подключать как можно больше маршрутов. Возможно, он скоро появится.
                    </p>
                </div>
            </div>
            <div v-else class="menu__intro">
				<p>Отправление и прибытие по местному времени</p>
				<h4>Расписание автобусов {{dispatchEl.name}} — {{arrivalEl.name}}</h4>
				<div class="menu__inro-sort">
				<div class="inro-sort__button">
                	<button @click="sort($event, 'dispatchDate')" :class="{active: sortingParams.param == 'dispatchDate'}">
                		Время отправления <img v-if="sortingParams.param == 'dispatchDate'" :src="sortingParams.arrowUp ?  '/src/assets/arrow_up.svg' : '/src/assets/arrow_down.svg'" alt="">
                	</button>
            	</div>
            	<div class="inro-sort__button">
                	<button @click="sort($event, 'freeSeatCount')" :class="{active: sortingParams.param == 'freeSeatCount'}">
                		Количество билетов <img v-if="sortingParams.param == 'freeSeatCount'" :src="sortingParams.arrowUp ?  '/src/assets/arrow_up.svg' : '/src/assets/arrow_down.svg'" alt="">
                	</button>
            	</div>
            	<div class="inro-sort__button">
                	<button @click="sort($event, 'arrivalDate')" :class="{active: sortingParams.param == 'arrivalDate'}">
                		Время прибытия <img v-if="sortingParams.param == 'arrivalDate'" :src="sortingParams.arrowUp ?  '/src/assets/arrow_up.svg' : '/src/assets/arrow_down.svg'" alt="">
                	</button>
            	</div>
            	<div class="inro-sort__button">
                	<button @click="sort($event, 'price')" :class="{active: sortingParams.param == 'price'}">
                		Стоимость <img v-if="sortingParams.param == 'price'" :src="sortingParams.arrowUp ?  '/src/assets/arrow_up.svg' : '/src/assets/arrow_down.svg'" alt="">
                	</button>
            	</div>
				</div>
			</div>
            
            <template v-for="race in sortedRaces">
                <RaceCard @toSeats="toSeats" :race="race"/> 
            </template>
            <!-- <RaceCard/>  -->
		</div>
	</div>


</div>
<Footer/>
</template>

<style>
.not__found{
    box-shadow: rgb(0 0 0 / 15%) 0px 2px 17px;
}
.loader__outside{
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #F8F8F8;
    padding: 30px 0;
    box-shadow: rgb(0 0 0 / 15%) 0px 2px 17px;
    border-radius: 6px;
}
.loader__outside p {
    margin-left: 8px;
    color: grey;
}
.loader__outside img {
    transform-origin: top center;
    animation-name: sale;
    animation-duration: 1s;
    animation-fill-mode: both;
}

.loader {
    border: 5px solid #C8C8C8; /* Light grey */
    border-top: 5px solid grey; /* Blue */
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 2s linear infinite;
    text-align: center;
    margin-top: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.not__found{
    display: flex;
    align-items: center;
    padding: 30px;
}
.not__found img{
    width: 200px;
}
.not__found-text{
    margin-left: 60px;
}
.not__found-title{
    font-size: 38px;
    margin-bottom: 20px;
}
.not__found-descr{
    font-size: 18px;
}

.inro-sort__button img{
    width: 14px;
    margin-left: 2px;
    margin-top: 0px;
}

.inro-sort__button{
    text-align: center;
}

.inro-sort__button button.active {
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
</style>

<script>
import HeaderMain from '../components/HeaderMain.vue';
import Footer from '../components/Footer.vue';
import RaceCard from '../components/RaceCard.vue';
import router from '../router';
import axios from 'axios';
import * as dayjs from 'dayjs'

export default {
    components: {
        HeaderMain,
        Footer,
        RaceCard
    },
    data(){
        return{
            arrivalEl: {
                id: this.$route.params['arrival_id'],
                name: this.$route.params['arrival_name']
            },
            dispatchEl: {
                id: this.$route.params['dispatch_id'],
                name: this.$route.params['dispatch_name']
            },
            date: this.$route.params['date'],
            races: [],
            loadingRaces: true,
            months: [
                '', 'янв.', 'февр.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сент.', 'окт.', 'ноябр.', 'дек.', 
            ],
            sortingParams: {
                arrowUp: true,
                param: 'price'
            }
        }
    },
    mounted(){
        this.changeRaces0(this.date, this.dispatchEl.id, this.arrivalEl.id);
    },
    computed: {
        sortedRaces(){
            let direction = [];
            if(!this.sortingParams.arrowUp){
                direction[0] = 1
                direction[1] = -1
            }
            else{
                direction[0] = -1
                direction[1] = 1
            }
            let key = this.sortingParams.param
            return this.races.sort(function (a, b) {
            if (a[key] > b[key]) {
                return direction[0];
            }
            if (a[key] < b[key]) {
                return direction[1];
            }
            return 0;
            });
        }
    },
    methods: {
        async changeRaces0(date, dispatch_id, arrival_id){
            this.loadingRaces = true
            this.races = []
            const promise = axios
                .get('http://localhost:8000/api/races/'+date+'/?dispatch_point_id='+dispatch_id+'&arrival_point_id='+arrival_id)
                .then(response => (
                    this.races = response.data
                ));
            await promise
            
            // console.log(this.races)
            this.races.forEach(race => {
                race.section = 'route'
                race.details_menu = false
                // console.log(dayjs(race.dispatchDate))
                // race.dispatchDateObj = dayjs(race.dispatchDate)
                // race.arrivalObj = dayjs(race.arrivalDate)
                race.dispatchDay = dayjs(race.dispatchDate).format('D')+' '+this.months[dayjs(race.dispatchDate).format('M')]
                race.arrivalDay = dayjs(race.arrivalDate).format('D')+' '+this.months[dayjs(race.arrivalDate).format('M')]
                
                race.dispatchTime = dayjs(race.dispatchDate).format('HH:mm')
                race.arrivalTime = dayjs(race.arrivalDate).format('HH:mm')
                console.log()
                // this.get_driving_time(race.dispatchDate, race.arrivalDate)
                // .then(response => (
                //     race.driving_time = response
                // ))


                // difference = dayjs(race.arrivalDate).diff(dayjs(race.dispatchDate))/1000
                
                // race.difference = ''
                // if(~~(difference / 3600) != 0){
                //     race.difference = difference -(difference / 3600)+'ч.'
                // }
                
            });
            this.loadingRaces = false
        },
        async get_driving_time(dispatchDate, arrivalDate){
            let driving_time = ''
            const promise1 = axios
                .get('http://localhost:8000/api/date/format?start='+dispatchDate+'&finish='+arrivalDate)
                .then(response => (
                    driving_time = response.data
                ));
                await promise1
                console.log(driving_time)
            return driving_time;
        },
        sort(event, param){
            this.sortingParams.param = param;
            this.sortingParams.arrowUp = !this.sortingParams.arrowUp
        },
        addZero(num) {
            if (num >= 0 && num <= 9) {
                return '0' + num;
            } else {
                return num;
            }
        },
        // tommorow(){
        //     let date = new Date(this.$route.params['date']);
        //     return date.getFullYear() + '-' + this.addZero(date.getMonth() + 1) + '-' + this.addZero(date.getDate()+1);
        // },
        // afterTommorow(){
        //     let date = new Date(this.$route.params['date']);
        //     return date.getFullYear() + '-' + this.addZero(date.getMonth() + 1) + '-' + this.addZero(date.getDate()+2);
        // },
        toSeats(raceId){
            router.push({name: 'SeatPage', params: {race_id: raceId}})
        }
    }
    
}
</script>