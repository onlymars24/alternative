<template>
<HeaderMain :key="paramKey" @changeRaces="changeRaces0" :arrivalEl0="arrivalEl" :dispatchEl0="dispatchEl" :date0="date" :isRaces="true"/>
<div>
    <!-- RACES <pre>{{ races }}</pre> -->
    <div class="menu" style="margin-top: 50px;">
        <div class="container">
            <div class="menu__intro">
                <h2 style="font-size: 24px; margin: 10px 0;">Расписание автобусов {{errorNames.dispatch}} — {{errorNames.arrival}}</h2>
                <div class="menu__inro-sort">
                </div>              
            </div>             
        </div>
    </div>
   
    <div class="menu">
		<div class="container">
            <!-- {{ dates }} -->
            <BusLoading v-if="loadingRaces"/>
            <div v-else>
                <div v-if="!races.length && notExistingRace" class="not__found">
                    <!-- <div class="not__found-img">
                        <img src="../assets/free-icon-sad-3350122.png">
                    </div> -->
                    <div class="not__found-text">
                        <p class="not__found-title">
                            Маршрута {{errorNames.dispatch}} — {{errorNames.arrival}}
                            не существует
                        </p>
                        <p class="not__found-descr">
                            Выберите другие точки отправления и прибытия.
                        </p>
                    </div>
                </div>
                <div v-else-if="!races.length && !notExistingRace" class="not__found">
                    <!-- <div class="not__found-img">
                        <img src="../assets/free-icon-sad-3350122.png">
                    </div> -->
                    <div class="not__found-text">
                        <p v-if="this.existingRaces.step == 1" style="margin-bottom: 7px; line-height: 30px;" class="not__found-title">
                            {{errorNames.dispatch}} — {{errorNames.arrival}}
                            <strong>на  {{ dateForError == dates.today ? 'сегодня' : '' }} 
                            {{ dateForError == dates.tomorrow ? 'завтра' : '' }} 
                            {{ dateForError == dates.afterTomorrow ? 'послезавтра' : '' }} {{ dateForError }}
                            билеты не найдены.</strong>
                        </p>
                        <!-- <p class="not__found-descr">
                            Выберите другую дату или точки отправления и прибытия.
                        </p> -->
                        <div v-if="this.existingRaces.step == 1">
                            <!-- <p style="font-size: 24px;  line-height: 30px;">Выберите другую дату или точки отправления и прибытия.</p> -->
                            <p style="font-size: 24px; margin-top: 10px; line-height: 30px;">Найти ближайшие рейсы на другую дату?</p>
                            <div class="rejection__buttons" style="display: flex; justify-content: space-between; margin-top: 7px;">
                            <button class="btn btn-primary btn-code" @click="findOtherDates"><div style="font-size: 20px;">Найти</div></button>    
                            <!-- <button class="btn btn-outline-secondary btn-code" @click="$emit('CloseWindow')"><div style="font-size: 20px;">Нет</div></button> -->
                            </div> 
                        </div>
                        <div v-if="this.existingRaces.step == 2">
                            <div>
                                <p style="font-size: 24px;">{{errorNames.dispatch}} — {{errorNames.arrival}}
                                    на ближайшие 7 дней рейсы не найдены.</p>
                                <p style="font-size: 30px; margin-top: 10px;">Выберите другую дату или точки отправления и прибытия.</p>
                            </div>

                        </div>
                    </div>
                </div>
                <div v-else class="menu__intro">
                    <!-- <p>Отправление и прибытие по местному времени</p>
                    <h4>Расписание автобусов {{dispatchEl.name}} — {{arrivalEl.name}}</h4> -->
                    <div class="menu__inro-sort">
                    <div class="inro-sort__button">
                        <button @click="sort($event, 'dispatchDate')" :class="{active: sortingParams.param == 'dispatchDate'}">
                            Время отправления <img v-if="sortingParams.param == 'dispatchDate'" :src="sortingParams.arrowUp ?  '/img/arrow_up.svg' : '/img/arrow_down.svg'" alt="">
                        </button>
                    </div>
                    <!-- <div class="inro-sort__button">
                        <button @click="sort($event, 'freeSeatCount')" :class="{active: sortingParams.param == 'freeSeatCount'}">
                            Количество билетов <img v-if="sortingParams.param == 'freeSeatCount'" :src="sortingParams.arrowUp ?  '/img/arrow_up.svg' : '/img/arrow_down.svg'" alt="">
                        </button>
                    </div> -->
                    <div class="inro-sort__button">
                        <button @click="sort($event, 'arrivalDate')" :class="{active: sortingParams.param == 'arrivalDate'}">
                            Время прибытия <img v-if="sortingParams.param == 'arrivalDate'" :src="sortingParams.arrowUp ?  '/img/arrow_up.svg' : '/img/arrow_down.svg'" alt="">
                        </button>
                    </div>
                    <div class="inro-sort__button">
                        <button @click="sort($event, 'price')" :class="{active: sortingParams.param == 'price'}">
                            Стоимость <img v-if="sortingParams.param == 'price'" :src="sortingParams.arrowUp ?  '/img/arrow_up.svg' : '/img/arrow_down.svg'" alt="">
                        </button>
                    </div>
                    </div>
                    <template v-for="race in sortedRaces">
                        <!-- <pre>{{ race }}</pre> -->
                        <RaceCard @toSeats="toSeats" :race="race"/> 
                    </template>                
                </div>
                <div style="margin-top: 20px; list-style-type: unset;" v-if="busRoute" v-html="busRoute.content"></div>
            </div>

            

            <!-- <RaceCard/>  -->
		</div>
	</div>

    <!-- <PopupWindow v-if="openWindow" @findOtherDates="findOtherDates" :existingRaces="existingRaces" @CloseWindow="openWindow = false, Scroll()" :content="11"/> -->


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
    font-size: 24px;
    margin-bottom: 20px;
}
.not__found-descr{
    font-size: 30px;
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

 @media (max-width: 425px)
{
    .not__found{
        flex-direction: column;
    }
    .not__found-text {
        margin-left: 0px;
    }
    .not__found-title {
        margin-top: 10px;
        line-height: 35px;
    }
}
</style>

<script>
import HeaderMain from '../components/HeaderMain.vue';
import Footer from '../components/Footer.vue';
import RaceCard from '../components/RaceCard.vue';
import BusLoading from '../components/BusLoading.vue';
import router from '../router';
import axios from 'axios';
import axiosClient from '../axios'
import dayjs from 'dayjs'
import PopupWindow from '../components/PopupWindow.vue';

export default {
    components: {
        HeaderMain,
        Footer,
        RaceCard,
        BusLoading,
        PopupWindow
    },
    data(){
        return{
            arrivalEl: {
                id: this.$route.query.to_id,
                name: this.$route.params['arrival_name']
            },
            dispatchEl: {
                id: this.$route.query.from_id,
                name: this.$route.params['dispatch_name']
            },
            date: this.$route.query.on,
            errorNames: {
                dispatch: this.$route.params['dispatch_name'],
                arrival: this.$route.params['arrival_name']
            },
            dateForError: this.$route.query.on,
            races: [],
            loadingRaces: true,
            notExistingRace: false,
            months: [
                '', 'янв.', 'февр.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сент.', 'окт.', 'ноябр.', 'дек.', 
            ],
            sortingParams: {
                arrowUp: false,
                param: 'dispatchDate'
            },
            paramKey: 0,
            busRoute: null,
            openWindow: false,
            existingRaces: {
                loading: false,
                step: 1,
                date: null
            },
            dates: {
                today: '',
                tomorrow: '',
                afterTomorrow: ''
            }
        }
    },
    async mounted(){
        this.dates.today = dayjs().format('DD.MM.YYYY')
        this.dates.tomorrow = dayjs().add(1, 'day').format('DD.MM.YYYY')
        this.dates.afterTomorrow = dayjs().add(2, 'day').format('DD.MM.YYYY')
        // console.log(document.referrer)
        // document.title = 'Автобус '+this.$route.params['dispatch_name']+' - '+this.$route.params['arrival_name'];
        // const descEl = document.querySelector('head meta[name="description"]');
        // descEl.setAttribute('content', 'Автобус '+this.dispatchEl.name+' — '+this.arrivalEl.name+': расписание, отправление и прибытие по местному времени, цена билетов, маршрут.');

        // const linkCan = document.querySelector('head link[rel="canonical"]');
        // linkCan.setAttribute('href', 'https://росвокзалы.рф/автобус/'+this.dispatchEl.name+'/'+this.arrivalEl.name);


        // const twitterTitle = document.querySelector('head meta[name="twitter:title"]');
        // twitterTitle.setAttribute('content', 'Автобус '+this.$route.params['dispatch_name']+' - '+this.$route.params['arrival_name']);

        // const twitterDescr = document.querySelector('head meta[name="twitter:description"]');
        // twitterDescr.setAttribute('content', 'Автобус '+this.dispatchEl.name+' — '+this.arrivalEl.name+': расписание, отправление и прибытие по местному времени, цена билетов, маршрут.');

        // const ogTitle = document.querySelector('head meta[name="og:title"]');
        // ogTitle.setAttribute('content', 'Автобус '+this.$route.params['dispatch_name']+' - '+this.$route.params['arrival_name']);

        // const ogDescr = document.querySelector('head meta[name="og:description"]');
        // ogDescr.setAttribute('content', 'Автобус '+this.dispatchEl.name+' — '+this.arrivalEl.name+': расписание, отправление и прибытие по местному времени, цена билетов, маршрут.');



        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if(!regex.test(this.date)){
            console.log('нет')
            this.date = dayjs().format('YYYY-MM-DD') 
        }
        let dispatchPoints =[]
        const promise1 = axiosClient
        .get('/dispatch_points')
        .then(response => {
            dispatchPoints = response.data
        });
        await promise1
        let dispatchPoint = dispatchPoints.filter(point => {
            return point.name == this.dispatchEl.name
        })[0]
        // if(!dispatchPoint){
        //     this.$router.push({ name: 'Main' })
        // }
        
        let arrivalPoints = [];
        let arrivalPoint = null
        if(dispatchPoint){
            const promise2 = axiosClient
            .get('/arrival_points/'+dispatchPoint.id)
            .then(response => {
                arrivalPoints = JSON.parse(response.data.arrival_points)
            });
            await promise2
            arrivalPoint = arrivalPoints.filter(point => {
                return point.name == this.arrivalEl.name
            })[0]
        }


        // let arrivalPoints = [];
        // const promise2 = axiosClient
        // .get('/arrival_points/'+dispatchPoint.id)
        // .then(response => {
        //     arrivalPoints = JSON.parse(response.data.arrival_points)
        // });
        // await promise2
        // let arrivalPoint = arrivalPoints.filter(point => {
        //     return point.name == this.arrivalEl.name
        // })[0]
        if(!arrivalPoint || !dispatchPoint){
            // this.$router.push({ name: 'Main' })
            this.races = []
            this.loadingRaces = false
            this.notExistingRace = true
            return
        }
        // if(this.$route.query.utm_source && this.$route.query.utm_medium && this.$route.query.utm_campaign && this.$route.query.utm_content){
        //     console.log('update')
        //     localStorage.setItem('utm_data', JSON.stringify({
        //         utm_source: this.$route.query.utm_source,
        //         utm_medium: this.$route.query.utm_medium,
        //         utm_campaign: this.$route.query.utm_campaign,
        //         utm_content: this.$route.query.utm_content,
        //         referrer_url: document.referrer,
        //     }))
        // }
        if(this.$route.query.from_id != dispatchPoint.id || this.$route.query.to_id != arrivalPoint.id){
            this.dispatchEl.id = dispatchPoint.id
            this.arrivalEl.id = arrivalPoint.id
            this.$router.push({ name: 'Races', query: { from_id: this.dispatchEl.id, to_id: this.arrivalEl.id, on: this.date } })
            this.paramKey ++
        }

        // const promise3 = axiosClient
        // .post('/races/xml/create', {dispatchName: dispatchPoint.name, arrivalName: arrivalPoint.name})
        // .then(response => {
        //     console.log(response)
        // })
        // .catch(error => {
        //     console.log(error)
        // })
        // await promise3

        

        this.changeRaces0(this.date, this.dispatchEl.id, this.arrivalEl.id, dispatchPoint.name, arrivalPoint.name);
        this.dateForError = dayjs(this.date).format('DD.MM.YYYY')
        // console.log(window.location.origin + this.$route.fullPath)
        // console.log(this.$route)
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
        async findOtherDates(){
            this.loadingRaces = true
            this.existingRaces.step = 2
            const promise1 = axiosClient
                .get('/seven/days/races?dispatchPointId='+this.$route.query.from_id+'&arrivalPointId='+this.$route.query.to_id+'&date='+this.$route.query.on)
                .then(response => {
                    console.log(response)
                    this.existingRaces.date = response.data.date
                })
                .catch(error => {
                    console.log(error)
                });
            await promise1
            this.loadingRaces = false
            if(!this.existingRaces.date){
                return
            }
            // this.openWindow = false
            // router.push({name: 'Main'})
            console.log(this.existingRaces.date)
            
            router.push({ name: 'Races', query: { from_id: this.$route.query.from_id, to_id: this.$route.query.to_id, on: this.existingRaces.date}, params: { dispatch_name: this.$route.params['dispatch_name'], arrival_name: this.$route.params['arrival_name'] }, replace: true })
            this.date = this.existingRaces.date
            this.paramKey ++
            // this.$forceUpdate(); 

            // router.go(1)
            console.log(this.$route)
            console.log(window.location.origin + this.$route.fullPath)
            // router.replace(this.$route.path)
            // window.location.replace(window.location.origin + this.$route.fullPath);
            this.changeRaces0(this.existingRaces.date, this.$route.query.from_id, this.$route.query.to_id, this.$route.params['dispatch_name'], this.$route.params['arrival_name'])
        },
        async changeRaces0(date, dispatch_id, arrival_id, dispatch_name, arrival_name){
            // window.scrollTo(0, 600);
            this.loadingRaces = true


            this.races = []
            this.errorNames.dispatch = dispatch_name
            this.errorNames.arrival = arrival_name

            const promise1 = axiosClient
            .post('/races/xml/create', {dispatchName: dispatch_name, arrivalName: arrival_name})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise1
            const promise2 = axiosClient
                .get('/races/'+date+'/?dispatch_point_id='+dispatch_id+'&arrival_point_id='+arrival_id)
                .then(response => (
                    this.races = response.data
                ));
            await promise2
            window.scrollTo(0, 600);
            if(this.races.length > 0){
                this.races.forEach(race => {
                    race.section = 'route'
                    race.details_menu = false
                    race.dispatchDay = race.dispatchDate ? dayjs(race.dispatchDate).format('D')+' '+this.months[dayjs(race.dispatchDate).format('M')] : ''
                    race.arrivalDay = race.arrivalDate ? dayjs(race.arrivalDate).format('D')+' '+this.months[dayjs(race.arrivalDate).format('M')] : ''
                    race.dispatchTime = race.dispatchDate ? dayjs(race.dispatchDate).format('HH:mm') : ''
                    race.arrivalTime = race.arrivalDate ? dayjs(race.arrivalDate).format('HH:mm') : ''
                });
            }
            else{
                // this.openWindow = true
                // var calendarInput = document.querySelector('#calendar');
                // calendarInput.focus();
            }
            const promise3 = axiosClient
            .get('/bus/route?dispatchPointName='+dispatch_name+'&arrivalPointName='+arrival_name)
            .then(response => {
                this.busRoute = response.data.busRoute
            })
            .catch(error => {
                console.log(error)
            })
            this.loadingRaces = false
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
        toSeats(raceId){
            router.push({name: 'SeatPage', params: {dispatch_point_id: this.dispatchEl.id, arrival_point_id: this.arrivalEl.id, date: this.$route.query.on, race_id: raceId}})
        }
    },
    // created() {
    //   document.title = 'Результаты поиска';
    // }
    
}
</script>