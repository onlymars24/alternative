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
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col"><strong>№</strong></th>
                    <th scope="col"><strong>Рейс</strong></th>
                    <th scope="col"><strong>Время следования</strong></th>
                    <th scope="col"><strong>Дни недели</strong></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th rowspan="3" scope="row">543</th>
                    <td style="width: 30%;" rowspan="3">Томск — А/П Толмачево ч/з Новосибирск ЖД Вокзал-Главный 3757 , Межобластное</td>
                    <td>18:00</td>
                    <td>Каждый день</td>
                </tr>
                <tr>
                <td>18:00</td>
                <td>Каждый день</td>
                </tr>
                <tr>
                    <td>18:00</td>
                    <td>Каждый день</td>
                </tr>
            </tbody>
        </table>        
    </div>


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
    @media(max-width: 790px){
        .station__slides-button{
            display: none;
        }
    }
</style>