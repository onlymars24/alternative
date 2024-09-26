<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import BusStationsMain from '../components/BusStationsMain.vue'
import axiosClient from '../axios';
// import Captcha from 'https://smartcaptcha.yandexcloud.net/captcha.js'

export default{
    name: "Main",
    components: {
        HeaderMain,
        Footer,
        BusStationsMain
    },
    data() {
        return {
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
        // document.title = "Автовокзалы России";
        // const descEl = document.querySelector('head meta[name="description"]');
        // descEl.setAttribute('content', 'Билеты на автобус онлайн');

        

        const promise = axiosClient
        .get('/page/main')
        .then(response => {
            console.log(response)
            this.content = response.data.pageMain
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        console.log('this.$route.query')
        
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