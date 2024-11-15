<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import BusStationsMain from '../components/BusStationsMain.vue'
import MainCrumbs from '../components/MainCrumbs.vue'
import axiosClient from '../axios';
// import ymaps3 from '/api'
// import Captcha from 'https://smartcaptcha.yandexcloud.net/captcha.js'

export default{
    name: "Main",
    components: {
        HeaderMain,
        Footer,
        BusStationsMain,
        MainCrumbs
    },
    data() {
        return {
            map: '',
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
            disabledDate: (Date) => {
            return time.getTime() > Date.now()
            },
            groupID: 223652237,
            content: ''
            // innerDrawer: false
        }
    },
    methods: {

    },
    watch: {
        
    },
    computed: {

    },
    async mounted() {
        // console.log(window.location.href)
        // ymaps3.import('@yandex/ymaps3-vuefy')
        // await ymaps3.ready;

        // const {YMap, YMapDefaultSchemeLayer} = ymaps3;

        // // Иницилиазируем карту
        // const map = new YMap(
        //     // Передаём ссылку на HTMLElement контейнера
        //     document.getElementById('map'),

        //     // Передаём параметры инициализации карты
        //     {
        //         location: {
        //             // Координаты центра карты
        //             center: [37.588144, 55.733842],

        //             // Уровень масштабирования
        //             zoom: 10
        //         }
        //     }
        // );

        // // Добавляем слой для отображения схематической карты
        // map.addChild(new YMapDefaultSchemeLayer());

        // document.title = "Автовокзалы России";
        // const descEl = document.querySelector('head meta[name="description"]');
        // descEl.setAttribute('content', 'Билеты на автобус онлайн');

        

        // const promise = axiosClient
        // .get('/page/main')
        // .then(response => {
        //     console.log(response)
        //     this.content = response.data.pageMain
        // })
        // .catch(error => {
        //     console.log(error)
        // })
        // await promise
        // console.log('this.$route.query')

        // await axiosClient
        // .get('/map')
        // .then(response => {
        //     console.log(response)
        //     this.map = URL.createObjectURL(response.data.map)
        // })
        // .catch(error => {
        //     console.log(error)
        // })
        // VK.Widgets.CommunityMessages("vk_community_messages", this.groupID, {
        //     tooltipButtonText: "Есть вопрос?",
        //     expanded: "0",
        //     widgetPosition: "left"
        // });
    }
}
</script>

<template>
    <HeaderMain/>
    <div></div>
    <!-- <MainCrumbs :pages="[]"/> -->
    
    <!-- {{ map }} -->

</template>

<!-- <script>
import axiosClient from '../axios';

export default {
  data(){
    return {
      race: [],
      content: '',
      loading: true,
      content: ''
    }
  },
  async mounted(){
    const promise = axiosClient
    .get('/page/main')
    .then(response => {
        console.log(response)
    })
    .catch(error => {
        console.log(error)
    })
    await promise
  },
  methods: {

  }

}
</script> -->

<style>
    /* @import url('https://fonts.gstatic.com'); */
    /* @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet'); */ 
    
</style>