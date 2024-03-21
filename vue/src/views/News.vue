<template>
    <HeaderMain :isRaces="false"/>


    <div class="about" style="margin-top: 50px;">
        <div class="container">
            
            <div class="news__cards">
                <h2 class="news__cards-title">Новости</h2>
                <div v-for="newThing in news" class="news__card" @click="toNew()">
                    <div class="card">
                        <img src="../img/media-img.webp" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">{{newThing.title}}</h5>
                            <p class="card-text">{{newThing.descr}}</p>
                        </div>
                        <div class="card-footer">
                            2 days ago
                        </div>
                    </div>                     
                </div>
               
            </div>

        </div>
    </div>
    
    <hr class="bef__footer">
    <Footer/>
</template>

<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import axiosClient from '../axios';
import router from '../router'

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/scrollbar';

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
            news: [
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
                {
                    title: 'title1',
                    descr: 'descr',
                    avatar: 'media-img.webp',
                    date: '11 сен 2001'
                },
            ],
            disabledDate: (Date) => {
            return time.getTime() > Date.now()
            },
            groupID: 223652237,
            content: '',
        }
    },
    methods: {
        toNew(){
            this.$router.push({ name: 'New', params: {id: 1}})
        }
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

        // VK.Widgets.CommunityMessages("vk_community_messages", this.groupID, {
        //     tooltipButtonText: "Есть вопрос?",
        //     expanded: "0",
        //     widgetPosition: "left"
        // });

        // Подключение виджета сообщества

        // VK.init({
        //     apiId: this.groupID,
        //     onlyWidgets: true
        // });
        // VK.Widgets.Group("vk_groups", 
        //     {
        //         mode: 3, color1: "FFFFFF", color2: "000000", color3: "5181B8"
        //     }, 
        //     this.groupID
        // );
    }
    // created() {
    //   document.title = 'Главная';
    // }
}
</script>



<style scoped>
.news__cards{
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}
.news__card{
    width: 32%;
    margin-bottom: 10px;
    cursor: pointer;
}
.news__cards-title{
    width: 100%;
    margin-bottom: 20px;
}
@media (min-width: 320px) and (max-width: 768px){
    .news__card{
        width: 100%;
    }
}

</style>