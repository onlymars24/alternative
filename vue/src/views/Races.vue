<template>
<HeaderMain :isRaces="true"/>
<MainCrumbs :pages="crumbPages"/>
<div>
    <div class="menu menu__top">
        <div class="container">
            <div class="menu__intro">
                <h2 class="menu__intro-title">Расписание автобусов {{$store.state.dispatchNameConst}} — {{$store.state.arrivalNameConst}} 
                    на {{ cacheDate == dates.today ? 'сегодня' : '' }} 
                            {{ cacheDate == dates.tomorrow ? 'завтра' : '' }} 
                            {{ cacheDate == dates.afterTomorrow ? 'послезавтра' : '' }}{{ formatedCacheRaces.length && !this.$route.query.on ? cacheDate : '' }}
                </h2>
                <div class="menu__inro-sort">
                </div>              
            </div>             
        </div>
    </div>
   
    <div class="menu">
		<div class="container">
            <div v-if="loadingRaces">
                <div v-if="formatedCacheRaces.length">
                    <div class="loader__outside">
                        <img src="../assets/bus_loading.png" style="max-width: 20%;">
                        <p style="color: grey; font-size: 27px;">Обновляем список доступных рейсов.....</p>
                        <div class="loader"></div>
                    </div>
                    <template v-for="race in formatedCacheRaces">
                        <RaceCard @toSeats="toSeats" :race="race" :button_status="'Обновляем...'"/>
                    </template>
                </div>
                <BusLoading v-else/>
            </div>
            
            <div v-else>
                <div v-if="!races.length && isServerError && formatedCacheRaces.length">
                    <template v-for="race in formatedCacheRaces">
                        <RaceCard @toSeats="toSeats" :race="race" :button_status="'stationError'"/>
                    </template>        
                </div>
                <div v-else-if="!races.length && isServerError">
                    <div class="not__found-text" style="margin-left: 0;">
                        <p class="not__found-title">
                            Нет связи с автовокзалом. Сервер автовокзала скоро восстановится, и затем можно будет купить билет. Повторите поиск через несколько минут.
                        </p>
                    </div>          
                </div>
                <div v-else-if="!races.length && !this.$route.query.on">
                    <div class="not__found">
                        <div class="not__found-text">
                            <p class="not__found-title">
                                Выберите дату отправления и нажмите "Найти билет"
                            </p>
                        </div>
                    </div>
                        <div v-if="formatedCacheRaces.length > 0">
                            <template v-for="race in formatedCacheRaces">
                                <RaceCard @toSeats="toSeats" :race="race" :button_status="'Обновить'" @findRacesWithDate="findRacesWithDate"/> 
                            </template>
                        </div>                       
                </div>
                <div v-else-if="!races.length" class="not__found">
                    <div class="not__found-text">
                        <p v-if="this.existingRaces.step == 1" style="margin-bottom: 7px; line-height: 30px;" class="not__found-title">
                            {{$store.state.dispatchNameConst}} — {{$store.state.arrivalNameConst}}
                            <strong>на  {{ dateConst == dates.today ? 'сегодня' : '' }} 
                            {{ dateConst == dates.tomorrow ? 'завтра' : '' }} 
                            {{ dateConst == dates.afterTomorrow ? 'послезавтра' : '' }} {{ dateConst }}
                            билеты не найдены.</strong>
                        </p>
                        <div v-if="this.existingRaces.step == 1">
                            <!-- <p style="font-size: 24px;  line-height: 30px;">Выберите другую дату или точки отправления и прибытия.</p> -->
                            <p style="font-size: 24px; margin-top: 10px; line-height: 30px;">Найти ближайшие рейсы на другую дату?</p>
                            <div class="rejection__buttons" style="display: flex; justify-content: space-between; margin-top: 7px;">
                                <button class="btn btn-primary btn-code" @click="findOtherDates"><div style="font-size: 20px;">Найти</div></button>    
                            </div>
                        </div>
                        <div v-if="this.existingRaces.step == 2">
                            <div>
                                <p style="font-size: 24px;">{{$store.state.dispatchNameConst}} — {{$store.state.arrivalNameConst}}
                                    на ближайшие 7 дней рейсы не найдены.</p>
                                <p style="font-size: 30px; margin-top: 10px;">Выберите другую дату или точки отправления и прибытия.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="menu__intro">
                    <div class="menu__inro-sort">
                        <div class="inro-sort__button">
                            <button @click="sort($event, 'dispatchDate')" :class="{active: sortingParams.param == 'dispatchDate'}">
                                Время отправления <img v-if="sortingParams.param == 'dispatchDate'" :src="sortingParams.arrowUp ?  '/img/arrow_up.svg' : '/img/arrow_down.svg'" alt="">
                            </button>
                        </div>
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
                        <RaceCard @toSeats="toSeats" :race="race" :button_status="'Выбрать'"/> 
                    </template>                
                </div>

                <!-- <div style="margin-top: 20px; list-style-type: unset;" v-if="busRoute" v-html="busRoute.content"></div> -->
            </div>

            

            <!-- <RaceCard/>  -->
		</div>
	</div>


</div>
<!-- <Footer/> -->
</template>

<style scoped>

.menu__intro-title{
    margin: 10px 0;
}

.menu__top{
    margin-top: 50px;
}

.not__found{
    box-shadow: rgb(0 0 0 / 15%) 0px 2px 17px;
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
import route from '../router';
import axios from 'axios';
import axiosClient from '../axios'
import dayjs from 'dayjs'
import PopupWindow from '../components/PopupWindow.vue';
import MainCrumbs from '../components/MainCrumbs.vue';
import store from '../store'

export default {
    components: {
        HeaderMain,
        Footer,
        RaceCard,
        BusLoading,
        MainCrumbs,
        PopupWindow
    },
    data(){
        return{
            date: this.$route.query.on,
            dateConst: this.$route.query.on,
            races: [],
            isServerError: false,
            racesInfo: null,
            loadingRaces: true,
            months: [
                '', 'янв.', 'февр.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сент.', 'окт.', 'ноябр.', 'дек.', 
            ],
            sortingParams: {
                arrowUp: false,
                param: 'dispatchDate'
            },
            paramKey: 0,
            busRoute: null,
            existingRaces: {
                loading: false,
                step: 1,
                date: null
            },
            dates: {
                today: '',
                tomorrow: '',
                afterTomorrow: ''
            },
            cacheRaces: [],
            formatedCacheRaces: [],
            // dispatchPoints: [],
            // arrivalPoints: [],
            crumbPages: [
            ],
            dispatchItem: store.state.dispatchItem,
            arrivalItem: store.state.arrivalItem,
            cacheDate: null
        }
    },
    // beforeMount(){
    //     window.scrollTo(0, 600);
    //     console.log('должен был съехать mounted')
    // },
    async mounted(){
        if(this.$route.query.orderId){
            await axiosClient
            .post('/return/race/send', {
                status: 'Успешная',
                dispatchName: store.state.dispatchItem.name,
                arrivalName: store.state.arrivalItem.name,
                orderId: this.$route.query.orderId
            })
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
        }

        if(this.dispatchItem.sourceId.includes('kladrs') && this.dispatchItem.kladr_station_page){
            this.crumbPages.push({name: 'Расписание '+this.dispatchItem.name, href: '/расписание/'+this.dispatchItem.kladr_station_page.url_region_code+'/'+this.dispatchItem.kladr_station_page.url_settlement_name })
        }
        else if(this.dispatchItem.sourceId.includes('stations') && this.dispatchItem.kladr && this.dispatchItem.kladr.kladr_station_page){
            this.crumbPages.push({name: 'Расписание '+this.dispatchItem.kladr.name, href: '/расписание/'+this.dispatchItem.kladr.kladr_station_page.url_region_code+'/'+this.dispatchItem.kladr.kladr_station_page.url_settlement_name })
        }

        // if(!dispatchItem.hasOwnProperty('details') && dispatchPoint.kladr_station_page){
        //     let kladrPage = dispatchPoint.kladr_station_page
        //     this.crumbPages.push({name: 'Расписание '+dispatchPoint.name, href: '/расписание/'+kladrPage.url_region_code+'/'+kladrPage.url_settlement_name })
        // }
        // else if(dispatchPoint.station && dispatchPoint.station.kladr_station_page && dispatchPoint.kladr && dispatchPoint.kladr.kladr_station_page){
        //     let kladr = dispatchPoint.kladr
        //     let kladrPage = kladr.kladr_station_page
        //     this.crumbPages.push({name: 'Расписание '+kladr.name, href: '/расписание/'+kladrPage.url_region_code+'/'+kladrPage.url_settlement_name })
        // }
        this.crumbPages.push(
            {name: 'Автобус '+this.dispatchItem.name+' - '+this.arrivalItem.name, href: null},
        )



        window.scrollTo(0, 600);
        this.dates.today = dayjs().format('DD.MM.YYYY')
        this.dates.tomorrow = dayjs().add(1, 'day').format('DD.MM.YYYY')
        this.dates.afterTomorrow = dayjs().add(2, 'day').format('DD.MM.YYYY')
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if(!regex.test(this.date)){
            this.date = dayjs().format('YYYY-MM-DD')
        }
        this.dateConst = dayjs(this.date).format('DD.MM.YYYY')

        await axiosClient
        .get('/cache/races?dispatchSlug='+store.state.dispatchItem.slug+'&arrivalSlug='+store.state.arrivalItem.slug+'&date='+this.date)
        .then(response => {
            let cacheRacesData = response.data.cacheRaces
            this.cacheRaces = Object.values(JSON.parse(cacheRacesData.list))
            this.cacheDate = dayjs(cacheRacesData.date).format('DD.MM.YYYY')
            // console.log('this.cacheRaces')
            // console.log(this.cacheRaces)
        })
        .catch(error => {
            // console.log(error)
        })
        if(this.cacheRaces.length > 0){
            this.cacheRaces.forEach(race => {
                race.section = 'route'
                race.details_menu = false
                race.dispatchDay = race.dispatchDate ? dayjs(race.dispatchDate).format('D')+' '+this.months[dayjs(race.dispatchDate).format('M')] : ''
                race.arrivalDay = race.arrivalDate ? dayjs(race.arrivalDate).format('D')+' '+this.months[dayjs(race.arrivalDate).format('M')] : ''
                race.dispatchTime = race.dispatchDate ? dayjs(race.dispatchDate).format('HH:mm') : ''
                race.arrivalTime = race.arrivalDate ? dayjs(race.arrivalDate).format('HH:mm') : ''
            });
            this.formatedCacheRaces = this.cacheRaces
        }
        if(!this.$route.query.on){
            this.loadingRaces = false
            return
        }
        let dispatchPoints = []


        // генерация точек етрафика(отправления и прибытия) для асинхронного поиска рейсов
        if(this.dispatchItem.sourceId.includes('stations')){
            dispatchPoints = this.dispatchItem.dispatch_points
        }
        if(this.dispatchItem.sourceId.includes('kladrs')){
            this.dispatchItem.stations.forEach(station => {
                station.dispatch_points.forEach(dispatchPoint => {
                    dispatchPoints.push(dispatchPoint)
                })
            })
        }
        let arrivalPoints = []
        if(this.arrivalItem.sourceId.includes('cache_arrival_points')){
            arrivalPoints = [this.arrivalItem]
        }
        if(this.arrivalItem.sourceId.includes('stations')){
            arrivalPoints = this.arrivalItem.arrival_points
        }
        if(this.arrivalItem.sourceId.includes('kladrs')){
            this.arrivalItem.stations.forEach(station => {
                station.arrival_points.forEach(arrivalPoint => {
                    arrivalPoints.push(arrivalPoint)
                })
            })
        }
        
        // dispatchPoints.forEach(async(dispatchPoint) => {
        let serverErrorsCount = 0
        let tempTotalRaces = []
            for(const dispatchPoint of dispatchPoints){
                for(const arrivalPoint of arrivalPoints){
                    if(arrivalPoint.dispatch_point_id == dispatchPoint.id){
                        let tempRaces = []
                        await axiosClient
                        .get('/races/simple?dispatchPointId='+dispatchPoint.id+'&arrivalPointId='+arrivalPoint.arrival_point_id+
                        '&date='+this.date+'&url='+decodeURIComponent(this.$route.path))
                        .then(response => {
                            if(response.data.isServerError){
                                serverErrorsCount++
                            }
                            tempRaces = response.data.races
                            tempRaces.forEach(race => {
                                race.section = 'route'
                                race.details_menu = false
                                race.dispatchDay = race.dispatchDate ? dayjs(race.dispatchDate).format('D')+' '+this.months[dayjs(race.dispatchDate).format('M')] : ''
                                race.arrivalDay = race.arrivalDate ? dayjs(race.arrivalDate).format('D')+' '+this.months[dayjs(race.arrivalDate).format('M')] : ''
                                race.dispatchTime = race.dispatchDate ? dayjs(race.dispatchDate).format('HH:mm') : ''
                                race.arrivalTime = race.arrivalDate ? dayjs(race.arrivalDate).format('HH:mm') : ''
                                tempTotalRaces[race.uid] = race
                            });
                            this.races = Object.values(tempTotalRaces)
                            if(this.races.length > 0){
                                this.loadingRaces = false
                            }
                        })
                        .catch(error => {
                        })
                    }
                }
            }
            console.log(this.races.length, serverErrorsCount)
            if(this.races.length == 0 && serverErrorsCount > 0){
                this.isServerError = true
                this.loadingRaces = false
                return
            }
            this.loadingRaces = false
            await axiosClient
            .get('/races?dispatchSourceId='+store.state.dispatchItem.sourceId+'&arrivalSourceId='+store.state.arrivalItem.sourceId+'&date='+this.date)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })


        //     await arrivalPoints.forEach(async(arrivalPoint) => {
        //         if(arrivalPoint.dispatch_point_id == dispatchPoint.id){
                    


        //             console.log(this.races)
        //         }
        //     })
        // })
        
        return
        
        await axiosClient
        .get('/races?dispatchSourceId='+store.state.dispatchItem.sourceId+'&arrivalSourceId='+store.state.arrivalItem.sourceId+'&date='+this.date)
        .then(response => {
            console.log(response)
            this.races = Object.values(response.data.racesInfo.races)
            console.log(this.races)
            this.loadingRaces = false
        })
        .catch(error => {
            console.log(error)
        })
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
        return
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
        findRacesWithDate(){
            // console.log(window.location.origin+'/автобус/'+store.state.dispatchItem.slug+'/'+store.state.arrivalItem.slug+'?on='+this.date)
            // return
            window.location.replace(window.location.origin+'/автобус/'+store.state.dispatchItem.slug+'/'+store.state.arrivalItem.slug+'?on='+this.date);
        },
        async findOtherDates(){
            this.loadingRaces = true
            window.scrollTo(0, 600);
            this.existingRaces.step = 2

            const promise1 = axiosClient
            .get('/seven/days/races?date='+this.$route.query.on+'&dispatchSourceId='+store.state.dispatchItem.sourceId+'&arrivalSourceId='+store.state.arrivalItem.sourceId)
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
            window.location.replace(window.location.origin+'/автобус/'+store.state.dispatchItem.slug+'/'+store.state.arrivalItem.slug+'?on='+this.existingRaces.date);
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
        toSeats(raceId, dispatch_point_id, arrival_point_id){
            router.push({name: 'SeatPage', params: {dispatch_point_id, arrival_point_id, date: this.$route.query.on, race_id: raceId}})
        }
    },
}
</script>