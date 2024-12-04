<template>
<div class="main__header" :style="('background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('+(page && page.header_img ? (baseUrl+'/'+page.header_img) : '/img/avtobus_avtovokzal_rosvokzaly.jpg' )+')')">
    <div class="container">
        <Header :blackText="false"/>
        <div class="main">
            <h1 style="text-shadow: 1px 1px 5px black" v-if="this.$route.name == 'Races'" class="main__main">
                Автобус {{$store.state.dispatchNameConst}} - {{$store.state.arrivalNameConst}}
            </h1>
            <h1 style="text-shadow: 1px 1px 5px black" v-else-if="this.$route.name == 'KladrPage'" class="main__main">
                {{ $store.state.kladrPage.name }}
            </h1>
            <h1 style="text-shadow: 1px 1px 5px black" v-else-if="this.$route.name == 'StationPage'" class="main__main">
                {{ $store.state.stationPage.name }}
            </h1>
            <h1 style="text-shadow: 1px 1px 5px black" v-else class="main__main">
                Автовокзалы России
            </h1>
            <p style="text-shadow: 1px 1px 5px black" v-if="this.$route.name == 'Races'" class="main__main-p">
                Билеты на автобус онлайн
            </p>
        </div>
        <div class="main__table">
            <SelectPoint :type-en="'dispatch'" :type-ru="'Откуда'"/>
            <SelectPoint :type-en="'arrival'" :type-ru="'Куда'"/>
            <div class="main__table-table">
                <p class="" style="margin: 0; padding-left: 12px; padding-top: 12px; padding-bottom: 5px; font-size: 12px;">Дата поездки</p>
                <div class="block">
                    <div class="date__picker-desktop">
                        <el-config-provider :locale="locale">
                            <el-date-picker
                            format="YYYY-MM-DD"
                            value-format="YYYY-MM-DD"
                            v-model="date"
                            :disabled-date="disabledDate"
                            type="date"
                            style="max-width: 100%;"
                            :clearable="false"
                            @keydown.prevent
                            >
                            </el-date-picker>
                        </el-config-provider>
                    </div>

                    <input id="calendar" class="main__table-date date__picker-mobile" type="date" style="width: 100%;" :min="dateNew" :max="toMonth" v-model="date" placeholder="Дата поездки">
                </div>
                <!-- <input id="calendar" class="main__table-date" type="date" style="width: 100%;" :min="dateNew" :max="toMonth" v-model="date" placeholder="Дата поездки"> -->
            </div>
            <div class="main__table-button">
                <button v-if="disabledSearch" disabled @click="findRaces" type="button" class="main__button">
                    Найти билет
                </button>
                <div class="main__button-link" v-else>
                    <a style="width:100%;" :href="'/автобус/'+$store.state.dispatchItem.slug+'/'+$store.state.arrivalItem.slug+(date ? '?on='+date : '')" type="button" class="main__button" disabled>
                        <div style="display: flex; align-items: center; justify-content: center;">
                            <p>Найти билет</p>
                        </div>
                    </a>
                </div>


            </div>
        </div>
        <div v-if="!disabledSearch" class="main__another__date">
            <a :href="raceUrl+'?on='+dates.today" :class="{'main__another__date-fix__active': dates.today !=  $route.query.on, 'strong': dates.today ==  $route.query.on}" class="main__another__date-fix">Сегодня</a>
            <a :href="raceUrl+'?on='+dates.tomorrow" :class="{'main__another__date-fix__active': dates.tomorrow !=  $route.query.on, 'strong': dates.tomorrow ==  $route.query.on}" class="main__another__date-fix">Завтра</a>
            <a :href="raceUrl+'?on='+dates.afterTomorrow" :class="{'main__another__date-fix__active': dates.afterTomorrow !=  $route.query.on, 'strong': dates.afterTomorrow ==  $route.query.on}" class="main__another__date-fix">Послезавтра</a>
        </div>
        <div v-else class="main__another__date">
            <span :class="{'main__another__date-fix__active': dates.today !=  $route.query.on, 'strong': dates.today ==  $route.query.on}" class="main__another__date-fix">Сегодня</span>
            <span style="margin: 0 5px;" :class="{'main__another__date-fix__active': dates.tomorrow !=  $route.query.on, 'strong': dates.tomorrow ==  $route.query.on}" class="main__another__date-fix">Завтра</span>
            <span :class="{'main__another__date-fix__active': dates.afterTomorrow !=  $route.query.on, 'strong': dates.afterTomorrow ==  $route.query.on}" class="main__another__date-fix">Послезавтра</span>
        </div>
    </div>
</div>
</template>

<script>
import router from '../router'
import axios, {isCancel, AxiosError} from 'axios';
import axiosClient from '../axios'
import Header from '../components/Header.vue'
import PopularPoint from '../components/PopularPoint.vue'
import SelectPoint from '../components/SelectPoint.vue'
import dayjs from 'dayjs'
import { spread } from 'lodash';
import store from '../store'
import ru from 'element-plus/dist/locale/ru.mjs'
export default{
        name: "HeaderMain",
        components: {
            Header,
            PopularPoint,
            SelectPoint
        },
        props: {
            page: {},
        },
        emits: ['changeRaces'],
        data() {
            return {
            locale: ru,
            auth: false,
            date: this.$route.query.on,

            dates: {
                today: '0',
                tomorrow: '0',
                afterTomorrow: '0'
            },
            dateNew: '',
            toMonth: '',
            // popularPoints: [],
            baseUrl: import.meta.env.VITE_API_BASE_URL,
        }
    },
    methods: {
        disabledDate(date) {
            const today = new Date();
            const threeMonthsLater = new Date(today);
            threeMonthsLater.setMonth(today.getMonth() + 3); // Устанавливаем дату через 3 месяца
            return date < today || date > threeMonthsLater;
        },
    },
    watch: {
        dispatchItem(dispatchPoint){
            store.commit('arrivalItemDelete')
        },
    },
    computed: {
        dispatchItem() {
            return store.state.dispatchItem
        },
        raceUrl(){
            return '/автобус/'+store.state.dispatchItem.slug+'/'+store.state.arrivalItem.slug
        },
        disabledSearch(){
            return !store.state.dispatchItem || !store.state.arrivalItem || !this.date
        },
    },
    async mounted(){
        let dash = 'G'
        this.dates.today = dayjs().format('YYYY-MM-DD')

        this.dates.tomorrow = dayjs().add(1, 'day').format('YYYY-MM-DD')
        this.dates.afterTomorrow = dayjs().add(2, 'day').format('YYYY-MM-DD')
        if(!this.date){
            this.date = this.dates.today
        }
        if(localStorage.getItem('authToken')){
            this.auth = true
        }
        var dateNewGet = new Date();
        this.dateNew = dateNewGet.getFullYear()+ "-" + (dateNewGet.getMonth() + 1 > 9? dateNewGet.getMonth() + 1 : "0" + (dateNewGet.getMonth()+ 1)) + "-" + dateNewGet.getDate();
        this.toMonth = dateNewGet.getFullYear()+ "-" + (dateNewGet.getMonth() + 2 == 12? 1 : (dateNewGet.getMonth() + 2 > 9 ? dateNewGet.getMonth() + 2 : "0"+(dateNewGet.getMonth() + 2))) + "-" + dateNewGet.getDate();
        // if(this.page){
        //     let tempDispatch = null
        //     console.log(this.page)

        //     if(this.page.kladr){
        //         tempDispatch = this.dispatchData.filter(point => {
        //             return point.slug == this.page.kladr.slug
        //         })[0] 
        //     }
        //     else if(this.page.station && this.page.station.dispatch_point){
        //         tempDispatch = this.dispatchData.filter(point => {
        //             return point.slug == this.page.station.dispatch_point.slug
        //         })[0]            
        //     }

        //     if(tempDispatch){
        //         this.dispatchEl.id = tempDispatch.keyId
        //         this.dispatchEl.name = tempDispatch.name
        //         this.dispatchText = this.dispatchEl.name
        //         this.arrivalEl.id = null
        //         this.arrivalEl.name = null
        //         this.arrivalText = ''
        //         this.arrivalBlur()
        //         this.getArrivalData()
        //     }

        // }
    }
}
</script>
<style scoped>
.main__button{
    text-align: center;
}
.main__another__date{
  text-align: center;
  margin-top: 15px;
  margin-left: 325px;
}
.main__another__date .strong{
  font-weight: 500;
}
.main__another__date a{
  margin-right: 4px;
  color: white
}
.main__another__date-fix{
    text-decoration: dotted;
    color: white;
}
.main__another__date-fix__active{
    border-bottom: 1px white dashed;
}
.big__font-size{
    font-size: 20px;
}
.date__picker-desktop{
    display: block;
}
.date__picker-mobile{
    display: none;
}
@media (max-width: 992px)
{
    .main__another__date{
        text-align: center;
        margin-top: 15px;
        margin-left: 0px;
    }
}

@media (max-width: 768px)
{
    .main__header .main{
        margin-top: 70px;
    }
    .date__picker-desktop{
        display: none !important;
    }
    .date__picker-mobile{
        display: block;
    }
}


.hint{
        z-index: 2;
        list-style-type: none;
        max-height: 242px;
        /* width: 400px; */
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #fff;
        position: absolute;
        top: 115%;
        -webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
     -moz-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
      -ms-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
       -o-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
      box-shadow: 0px 0px 10px rgba(0,0,0,.8);
    }
    /* 
Firefox */
    .hint {
        scrollbar-width: thin;
        scrollbar-color: rgb(223, 223, 223) rgb(255, 255, 255);
        padding: 0px;
    }


    .hint::-webkit-scrollbar {
        height: 12px;
        width: 6px;

    }
    .hint::-webkit-scrollbar-track {
        background: rgb(255, 255, 255);
    }
    .hint::-webkit-scrollbar-thumb {
        background-color: rgb(223, 223, 223) ;
        border-radius: 5px;
        border: 3px solid rgb(223, 223, 223);
    }

    li, ul{
        padding: 0;
        margin: 0;
    }
    .hint li{
        padding: 5px;
        font-size: 17px;
        cursor: pointer;
    }
    .hint li:hover{
        background-color: #DCDCDC;
    }
    .hint__title:hover{
        background-color: #fff;
    }
    .hint li span{
        font-size: 14px;
        color: grey;
    }
    .main__table{
        position: relative;
    }
    @media (max-width: 993px){
        .hint{
            top: 150px;
            /* min-width: 100%; */
            max-width: 100%;
        }
    }


</style>