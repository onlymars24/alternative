<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import MainCrumbs from '../components/MainCrumbs.vue'
import axiosClient from '../axios';
import router from '../router'
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
        const promise1 = axiosClient
        .get('/kladr/station/page?url_settlement_name='+(this.$route.params['settlement_name'])
        +'&url_region_code='+this.$route.params['region_code']
        +'&pageType=k'
        )
        .then(response => {
            console.log(response)
            this.kladrPage = response.data.page
            // this.content = JSON.parse(this.station.data).content
        })
        .catch(error => {
            console.log(error)
        })
        await promise1
        
        const promise2 = axiosClient
        .get('/kladr/station/pages/station/?kladrId='+this.kladrPage.kladr_id)
        .then(response => {
            console.log(response)
            this.stationPages = response.data.pages
        })
        .catch(error => {
            console.log(error)
        })
        await promise2


        await axiosClient
        .get('/kladr/arrival/kladrs?kladrId='+this.kladrPage.kladr_id)
        .then(response => {
            console.log('/kladr/arrival/kladrs')
            console.log(response)
            this.arrivalKladrs = response.data.arrivalKladrs
            // this.stationPages = response.data.pages
        })
        .catch(error => {
            console.log(error)
        })
        
        
        if(!this.kladrPage){
            router.push({ name: 'Main'})
            return
        }
        // let coordinates = [
        //     // [55.0411, 83.0275],
        //     // [55.03573, 82.896184]
        //     [55.008149, 82.935881], 
        //     [55.041111, 83.02737],
        //     [54.971596, 82.872504]
        // ]
        let coordinates = []
        this.stationPages.forEach(async (page) => {
            const station = page.station
            console.log('page.hidden')
            console.log(page.hidden)
            if(station.latitude && station.longitude && !page.hidden){
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
            })

            
            // var myPlacemark2 = new ymaps.GeoObject({
            //     geometry: {
            //         type: "Point",
            //         coordinates: [56.46239283218983, 84.99424147798115]
            //     }
            // });
            // map.geoObjects.add(myPlacemark2);
            map.behaviors.disable('scrollZoom'); 
            if(coordinates.length > 1){
                map.setBounds(coordinates);
            }
            
        });
        let YMapsID__title = document.querySelector('#YMapsID__title');
        let YMapsID = document.querySelector('#YMapsID');
        YMapsID__title.innerHTML = this.kladrPage.name+' на карте'
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
    <HeaderMain v-if="kladrPage" :isRaces="false" :page="kladrPage"/>
    <HeaderMain v-else :isRaces="false"/>
    <div></div>
    <MainCrumbs v-if="kladrPage" :pages="[{name: kladrPage.name, href: null}]"/>

    <!-- <div class="container">
        <h3 v-if="kladrPage" style="font-weight: 400; margin: 25px 0; font-size: 20px;">{{kladrPage.name}} направления</h3>
        <div class="station__races">
            <template v-for="item in arrivalKladrs">
                <p v-for="arrivalKladr in item[1]"><a :href="'/автобус/'+item[0].name+'/'+arrivalKladr.name">{{'Автобус '+item[0].name+' — '+arrivalKladr.name}}</a></p>
            </template>
        </div>        
    </div> -->
<!-- 
    <pre>
        {{ kladrPage }}
    </pre>
    <pre>
        {{ arrivalKladrs }}
    </pre> -->
    <!-- <div class="container" v-if="kladrPage && isMap">
        <h2 style="margin: 15px 0;">{{ kladrPage.name }} на карте</h2>
        <div id="YMapsID" style="max-width:100%; height:300px"></div>
    </div> -->
    <!-- <div v-if="kladrPage && kladrPage.name" class="container">
        <h4 style="margin: 30px 0;">{{ kladrPage.name }} на карте</h4>
        <div v-html="kladrPage.map"></div>
    </div> -->
    <!-- <div class="about" style="margin-top: 50px;">
        <div class="container">
                <div class="about__inner" v-if="kladrPage" style="width: 100%; display: block;">
                    <h2 style="margin-bottom: 20px;">{{ kladrPage.name }}</h2>
                    <div v-for="stationPage in stationPages" class="card" style="width: 100%; display: block; margin-bottom: 20px;">
                        <router-link target="_blank" :to="{ name: 'StationPage', params: { region_code: stationPage.url_region_code, settlement_name: stationPage.url_settlement_name } }">
                        <div class="card-body">
                            <h3 class="card-title">{{ stationPage.name }}</h3>
                            <h4 v-if="stationPage.station.address || stationPage.contacts" class="card-title" style="font-size: 19px; font-weight: 400;">
                                Справочная информация
                            </h4>
                            <h5 v-if="stationPage.station.address">
                                <strong>Адрес {{ stationPage.name }}:</strong>
                            </h5> 
                            <p v-if="stationPage.station.address" style="margin-bottom: 13px;" class="card-text">{{ stationPage.station.address }}</p>                            
                            <h5 v-if="stationPage.contacts">
                                <strong>Телефоны {{ stationPage.name }}:</strong>
                            </h5>                            
                            <div v-if="stationPage.contacts" style="margin-bottom: 13px;" v-html="stationPage.contacts"></div>
                        </div>
                        </router-link>
                    </div>

                    <div v-if="kladrPage.content" v-html="kladrPage.content"></div>
                </div>

        </div>
    </div> -->
    
    <!-- <hr class="bef__footer"> -->
    <!-- <Footer/> -->
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