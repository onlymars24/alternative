<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import axiosClient from '../axios';
import router from '../router'
import MainCrumbs from '../components/MainCrumbs.vue'
import store from '../store'
// import Captcha from 'https://smartcaptcha.yandexcloud.net/captcha.js'

import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Scrollbar, A11y } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';



export default{
    name: "Main",
    components: {
        HeaderMain,
        Footer,
        Swiper, 
        MainCrumbs,
        SwiperSlide
    },
    data() {
        return {
            modules: [ 
                Navigation, 
                // Pagination, 
                // Scrollbar, 
                A11y 
            ],
            drawer: false,
            size: 'default',
            value1: '',
            shortcuts: [
                {
                    text: 'Today',
                    value: new Date(),
                },
                {
                    text: 'Yesterday',
                    value: () => {
                    const date = new Date()
                    date.setTime(date.getTime() - 3600 * 1000 * 24)
                    return date
                    },
                },
                {
                    text: 'A week ago',
                    value: () => {
                    const date = new Date()
                    date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
                    return date
                    },
                },
            ],
            events: [],
            disabledDate: (Date) => {
            return time.getTime() > Date.now()
            },
            groupID: 223652237,
            content: '',
            stationPage: store.state.stationPage,
            navigation: {
                nextEl: '.station__slides-button-next',
                prevEl: '.station__slides-button-prev',
            },
            breakpoints: {
                320: {       
                    slidesPerView: 1,
                    allowTouchMove: true
                    // spaceBetween: 10     
                }, 
                500: {       
                    slidesPerView: 2,
                    allowTouchMove: true
                    // spaceBetween: 10     
                }, 
                790: {       
                    slidesPerView: 3,
                    // spaceBetween: 10     
                }, 
            },
            loading: false,
            races: [],
            arrivalKladrs: [],
            // isMap: false
        }
    },
    methods: {
        onSlideChange(){

        },
        onSwiper(){

        },
        toNew(id){
            this.$router.push({ name: 'New', params: {id: id}})
        }
    },
    watch: {
        
    },
    computed: {

    },
    async mounted() {
    //     const promise1 = axiosClient
    //     .get('/kladr/station/page?url_settlement_name='+(this.$route.params['settlement_name'])
    //     +'&url_region_code='+this.$route.params['region_code']
    //     +'&pageType=s'
    // )
    //     .then(response => {
    //         console.log(response)
    //         this.stationPage = response.data.page
    //         // this.content = JSON.parse(this.station.data).content
    //     })
    //     .catch(error => {
    //         console.log(error)
    //     })
    //     await promise1
    //     // return
    //     if(!this.stationPage){
    //         router.push({ name: 'Main'})
    //         return
    //     }
        const station = this.stationPage.station
        let coordinate
        if(station.latitude && station.longitude){
            coordinate = [station.latitude, station.longitude]
        }
        else{
            return
        }
        ymaps.ready(function () {
            const map = new ymaps.Map('YMapsID', {
                center: [parseFloat(coordinate[0]), parseFloat(coordinate[1])],
                controls: ['zoomControl'],
                zoom: 17,
                type: 'yandex#map',
            });
            var myPlacemark1 = new ymaps.GeoObject({
                geometry: {
                    type: "Point",
                    coordinates: [parseFloat(coordinate[0]), parseFloat(coordinate[1])]
                }
            });
            map.geoObjects.add(myPlacemark1);
            map.behaviors.disable('scrollZoom'); 
        });
        let YMapsID__title = document.querySelector('#YMapsID__title');
        let YMapsID = document.querySelector('#YMapsID');
        YMapsID__title.innerHTML = this.stationPage.name+' на карте'
        YMapsID.style.height = '300px'
    },
}
</script>

<template>
    <HeaderMain v-if="stationPage" :isRaces="false" :page="stationPage"/>
    <HeaderMain v-else :isRaces="false"/>
    <div></div>
    <MainCrumbs v-if="stationPage" :pages="[{name: 'Расписание '+stationPage.station.kladr.name, href: '/расписание/'+stationPage.url_region_code+'/'+stationPage.station.kladr.kladr_station_page.url_settlement_name}, 
    {name: stationPage.name, href: null}]"/>
</template>

<style scoped>
    .station__slides-wrapper{
        width: 94%;
    }
    .station__slides{
        margin-bottom: 50px;
        position: relative;
    }
    .station__slides-title{
        margin-bottom: 30px;
    }
    .station__slide{
        cursor: pointer;
    }
    .station__slide img{
        width: 100%; 
        height: 150px;
        object-fit: cover;
    }
    .station__slide-title{
        margin-top: 10px;
    }
    .station__slide span{
        color: grey;
        font-size: 12px;
    }
    .station__slide-subtitle{
        font-size: 14px;
    }
    .station__slides-buttons{
        display: flex;
        margin-top: 15px;
    }
    .station__slides-button{
        /* margin-right: 15px; */
        padding: 1px 5px;
        border: 2px solid #0275fe;
        border-radius: 50%;
        color: #0275fe;
        z-index: 20;
        position: absolute;
        top: 50%;
        /* width: 3%; */
        /* width: 30px;
        height: 30px;
        margin-top: -15px; */
        /* background-color: rgba(0, 0, 0, 0.5);
        color: #fff;
        border-radius: 50%;
        cursor: pointer; */
    }
    .station__slides-button-next{
        right: 0px;
    }
    .station__slides-button-prev{
        left: 0px;
    }
    .swiper-button-disabled{
        border: 2px solid grey !important;
        color: grey !important; 
    }
    .station__slides-link{
        margin-top: 15px;
    }
    /* .station__races{
        display: flex;
        flex-wrap: wrap;
    }
    .station__races p{
        width: 50%;
    }
    @media(max-width: 790px){
        .station__slides-button{
            display: none;
        }
        .station__races{
            display: block;
        }
        .station__races p{
            width: 100%;
        }
    } */
</style>