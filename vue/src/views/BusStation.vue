<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
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
            station: null,
            navigation: {
                nextEl: '.station__slides-button-next',
                prevEl: '.station__slides-button-prev',
            },
            loading: false
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
        console.log(this.$route.params['title'])
        const promise1 = axiosClient
        .get('/bus/station?title='+this.$route.params['title'].replace(/\s/g, '_'))
        .then(response => {
            console.log(response)
            this.station = response.data.station
            this.content = JSON.parse(this.station.data).content
        })
        .catch(error => {
            console.log(error)
        })
        await promise1
        
        if(!this.station){
            router.push({ name: 'Main'})
            return
        }
        document.title = this.station.name;
        
        const descEl = document.querySelector('head meta[name="description"]');
        descEl.setAttribute('content',this.station.description);

        const linkCan = document.querySelector('head link[rel="canonical"]');
        linkCan.setAttribute('href', 'https://росвокзалы.рф/автовокзал/'+this.station.title);

        const promise2 = axiosClient
        .get('/station/events?id='+this.station.id)
        .then(response => {
            console.log(response)
            this.events = response.data.events.reverse()
            console.log()
        })
        .catch(error => {
            console.log(error)
        })
        await promise2
    },
}
</script>

<template>
    <HeaderMain v-if="station" :isRaces="false" :busStationDispatchPointId="station.dispatch_point_id" :station="station"/>
    <HeaderMain v-else :isRaces="false"/>


    <div class="about" style="margin-top: 50px;">
        <div class="container">
            <div v-if="events.length > 0" class="station__slides">
            <h2 class="station__slides-title">Новости</h2>
            <swiper
            :modules="modules"
            :allowTouchMove="false"
            :slides-per-view="3"
            :space-between="20"
            :navigation="navigation"
            :oSwipingSelector="'station__slides-button-disabled'"
            navigation
            @swiper="onSwiper"
            @slideChange="onSlideChange"
            >
                <swiper-slide v-for="event in events">
                    <div class="card station__slide" style="height: 250px;" @click="toNew(event.id)">
                        <!-- <img src="../img/media-img.webp" class="card-img-top" alt="..."> -->
                        <div class="card-body">
                            <h5 class="card-title">{{event.title}}</h5>
                            <p class="card-text">{{event.descr}}</p>
                        </div>
                        <div class="card-footer">
                            {{event.date}}
                        </div>
                    </div>      
                </swiper-slide>
            </swiper>
            <div class="station__slides-buttons">
                <div class="station__slides-button station__slides-button-prev">&#9668;</div>
                <div class="station__slides-button station__slides-button-next">&#9658;</div>
            </div>                
            </div>
        
            <div class="about__inner">
                <div v-html="content"></div>

            <!-- <div class="about__info">
                <div class="about__info-main">
                    <a href="">Внутренние</a>
                </div>
                <div id='vk_community_messages'></div>
                <div class="about__info-text">
                    <div id="vk_groups"></div> 
                </div>
            </div> -->
            </div>

        </div>
    </div>
    
    <hr class="bef__footer">
    <Footer/>
</template>

<style scoped>
    .station__slides{
        margin-bottom: 50px;
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
    .station__slides-buttons .station__slides-button{
        margin-right: 15px;
        padding: 1px 5px;
        border: 2px solid #0275fe;
        border-radius: 50%;
        color: #0275fe;
    }
    .swiper-button-disabled{
        border: 2px solid grey !important;
        color: grey !important; 
    }
</style>