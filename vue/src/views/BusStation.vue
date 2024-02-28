<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import axiosClient from '../axios';
import router from '../router'
// import Captcha from 'https://smartcaptcha.yandexcloud.net/captcha.js'

export default{
    name: "Main",
    components: {
        HeaderMain,
        Footer,
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
            content: '',
            station: null
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
        

        console.log(this.$route.params['title'])
        const promise = axiosClient
        .get('/bus/station?title='+this.$route.params['title'].replace(/\s/g, '_'))
        .then(response => {
            console.log(response)
            this.station = response.data.station
            this.content = JSON.parse(this.station.data).content
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        
        if(!this.station){
            router.push({ name: 'Main'})
            return
        }
        document.title = this.station.name;
        const descEl = document.querySelector('head meta[name="description"]');
        descEl.setAttribute('content',this.station.description);

        VK.Widgets.CommunityMessages("vk_community_messages", this.groupID, {
            tooltipButtonText: "Есть вопрос?",
            expanded: "0",
            widgetPosition: "left"
        });

        // Подключение виджета сообщества

        VK.init({
            apiId: this.groupID,
            onlyWidgets: true
        });
        VK.Widgets.Group("vk_groups", 
            {
                mode: 3, color1: "FFFFFF", color2: "000000", color3: "5181B8"
            }, 
            this.groupID
        );
    }
    // created() {
    //   document.title = 'Главная';
    // }
}
</script>

<template>
    <HeaderMain v-if="station" :isRaces="false" :busStationDispatchPointId="station.dispatch_point_id" :station="station"/>
    <HeaderMain v-else :isRaces="false"/>

    <div class="about">
        <div class="container">
            
            <div class="about__inner">
                <div v-html="content"></div>

            <div class="about__info">
                <div class="about__info-main">
                    <!-- <a href="">Внутренние</a> -->
                </div>
                <div id='vk_community_messages'></div>
                <div class="about__info-text">
                    <div id="vk_groups"></div> 
                </div>
            </div>
            </div>

        </div>
    </div>
    
    <hr class="bef__footer">
    <Footer/>
</template>

<style>

</style>