<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import MainCrumbs from '../components/MainCrumbs.vue'
import axiosClient from '../axios';
import router from '../router'
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
            busStationsDispatchPoints: [],
            kladrPage: null,
            stationPages: [],
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
            testStr: 'sa d / f',
            arrivalKladrs: []
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
        await axiosClient
        .get('/kladr/station/pages/station/?kladrId='+store.state.kladrPage.kladr_id)
        .then(response => {
            console.log(response)
            this.stationPages = response.data.pages
        })
        .catch(error => {
            console.log(error)
        })


        // await axiosClient
        // .get('/kladr/arrival/kladrs?kladrId='+this.kladrPage.kladr_id)
        // .then(response => {
        //     console.log('/kladr/arrival/kladrs')
        //     console.log(response)
        //     this.arrivalKladrs = response.data.arrivalKladrs
        //     // this.stationPages = response.data.pages
        // })
        // .catch(error => {
        //     console.log(error)
        // })
        
        
        // let coordinates = [
        //     // [55.0411, 83.0275],
        //     // [55.03573, 82.896184]
        //     [55.008149, 82.935881], 
        //     [55.041111, 83.02737],
        //     [54.971596, 82.872504]
        // ]
        let coordinates =  [] 
        let bounds = []
        // coordinates.forEach(coordinate => {

        // })
        this.stationPages.forEach(async (page) => {
            const station = page.station
            console.log('page.hidden')
            console.log(page.hidden)
            if(station.latitude && station.longitude && page.hidden != 1){
                coordinates.push([parseFloat(station.latitude), parseFloat(station.longitude)])
            }
        })
        
        console.log('coordinates')
        console.log(coordinates)
        // console.log('coordinates1')
        // console.log(coordinates1)
        if(coordinates.length == 0){
            return
        }
        console.log(coordinates)
        ymaps.ready(function () {
            const map = new ymaps.Map('YMapsID', {
                center: [parseFloat(coordinates[0][0]), parseFloat(coordinates[0][1])],
                controls: ['zoomControl'],
                zoom: 20,
                type: 'yandex#map',
            });

            coordinates.forEach(coordinate => {
                var myPlacemark1 = new ymaps.GeoObject({
                    geometry: {
                        type: "Point",
                        coordinates: coordinate
                    }
                });
                map.geoObjects.add(myPlacemark1);
                bounds.push(myPlacemark1.geometry.getBounds());

            })
            map.behaviors.disable('scrollZoom'); 
            if(coordinates.length > 1){
                var mapBounds = bounds.reduce(function (result, currentBounds) {
                    result[0][0] = Math.min(result[0][0], currentBounds[0][0]);
                    result[0][1] = Math.min(result[0][1], currentBounds[0][1]);
                    result[1][0] = Math.max(result[1][0], currentBounds[1][0]);
                    result[1][1] = Math.max(result[1][1], currentBounds[1][1]);
                    return result;
                }, [[180, 90], [-180, -90]]); // Начальные значения (полные границы карты)

                // Устанавливаем границы с небольшой поправкой, чтобы не растягивалась карта слишком сильно
                map.setBounds(mapBounds, {
                    checkZoomRange: true, // Проверка диапазона масштаба
                    zoomMargin: 50 // Отступ для предотвращения слишком сильного увеличения масштаба
                });
            }
        });
        let YMapsID__title = document.querySelector('#YMapsID__title');
        let YMapsID = document.querySelector('#YMapsID');
        YMapsID__title.innerHTML = store.state.kladrPage.name+' на карте'
        YMapsID.style.height = '300px'
        // this.isMap = true
        // document.title = this.station.name;
        
        // const descEl = document.querySelector('head meta[name="description"]');
        // descEl.setAttribute('content',this.station.description);

        // const linkCan = document.querySelector('head link[rel="canonical"]');
        // linkCan.setAttribute('href', 'https://росвокзалы.рф/автовокзал/'+this.station.title);

        // const promise3 = axiosClient
        // .get('/station/events?id='+this.station.id)
        // .then(response => {
        //     console.log(response)
        //     this.events = response.data.events
        //     console.log()
        // })
        // .catch(error => {
        //     console.log(error)
        // })
        // await promise3
    },
}
</script>

<template>
    <HeaderMain/>
    <div></div>
    <MainCrumbs :pages="[{name: $store.state.kladrPage.name, href: null}]"/>
    <!-- <div class="container"> -->
        <!-- <div class="card" style="width: 100%; margin-bottom: 5px;">
        <div class="card-body">
            <span style="font-size: 0.8em;">Маршрут №7459</span>
            <p class="" style="color: #0275fe; padding-bottom: 5px;">Томск АВ — Моряковский Затон ч/з Зоркальцево с. 134 , Пригородное</p>
            <table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th style="font-size: 0.8em; width: 140px; color: grey; font-weight: 300;" scope="col">Регулярность</th>
                    <th style="font-size: 0.8em; color: grey; font-weight: 300;" scope="col">Время отправления</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="vertical-align: top; padding-bottom: 10px; font-size: 0.8em;">Пн, Вт, Сб, Вс</td>
                    <td style="vertical-align: top; padding-bottom: 10px; font-size: 0.8em;">06:00,&nbsp; 06:40,&nbsp; 15:00,&nbsp; 16:30,&nbsp; 17:15,&nbsp; 18:10, 19:00, 20:50</td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td style="vertical-align: top; font-size: 0.8em;">Пн, Вт, Чт, Пт, Сб, Вс</td>
                    <td style="vertical-align: top; font-size: 0.8em;">06:00,&nbsp; 06:40,&nbsp; 07:10,&nbsp; 07:50,&nbsp; 09:10,&nbsp; 13:30,&nbsp; 15:00,&nbsp; 16:30,&nbsp; 17:15,&nbsp; 18:10, 19:00, 20:50</td>
                </tr>
            </tbody>
        </table>  
        </div>
        </div>     -->
        <!-- <div class="card schedule__card" style="width: 100%;">
        <div class="card-body "> -->
            
<!-- .schedule__card-num{font-size: 0.8em; }
.schedule__card-name{ color: #0275fe; padding-bottom: 5px; }
.schedule__card-table{ width: 100%; }
.schedule__card-th{ font-size: 0.8em; color: grey; font-weight: 300; }
.schedule__card-td{ vertical-align: top; padding-bottom: 10px; font-size: 0.8em; } -->
            <!-- <span class="schedule__card-num">Маршрут №7459</span>
            <p class="schedule__card-name">Томск АВ — Моряковский Затон ч/з Зоркальцево с. 134 , Пригородное</p> -->
            <!-- <div>
                <div style="display: flex; justify-content: s;">
                    <div>Регулярность</div>
                    <div>Время отправления</div>
                </div>
            </div> -->
            <!-- <table class="schedule__card-table" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th class="schedule__card-th" style="width: 140px;" scope="col">Регулярность</th>
                        <th class="schedule__card-th" scope="col">Время отправления</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="schedule__card-td">Пн, Вт, Сб, Вс</td>
                        <td class="schedule__card-td">06:00,&nbsp; 06:40,&nbsp; 15:00,&nbsp; 16:30,&nbsp; 17:15,&nbsp; 18:10, 19:00, 20:50</td>
                    </tr>
                </tbody>
            </table>   -->
        
            
            <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a> -->
        <!-- </div>
        </div>     
    </div>       -->


</template>

<style scoped>
/* .schedule__card{ margin-bottom: 5px; }
.schedule__card-num{font-size: 0.8em; }
.schedule__card-name{ color: #0275fe; padding-bottom: 5px; }
.schedule__card-table{ width: 100%; }
.schedule__card-th{ font-size: 0.8em; color: grey; font-weight: 300; }
.schedule__card-td{ vertical-align: top; padding-bottom: 10px; font-size: 0.8em; } */

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
    @media(max-width: 790px){
        .station__slides-button{
            display: none;
        }
    }
</style>