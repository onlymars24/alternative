<template>
    <HeaderMain :isRaces="false"/>


    <div class="about" style="margin-top: 50px;">
        <div class="container">
            
            <div class="news__cards">
                <h2 class="news__cards-title">Новости</h2>
                <div v-for="event in events" class="news__card" @click="toNew(event.id)">
                    <div class="card" style="height: 250px;">
                        <!-- <img src="../img/media-img.webp" class="card-img-top" alt="..."> -->
                        <div class="card-body">
                            <h5 class="card-title">{{event.title}}</h5>
                            <p class="card-text">{{event.descr}}</p>
                        </div>
                        <div class="card-footer">
                            {{ event.date }}
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
            events: [],
        }
    },
    methods: {
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
        const promise = axiosClient
        .get('/events')
        .then(response => {
            console.log(response)
            this.events = response.data.events.reverse()
        })
        .catch(error => {
            console.log(error)
        })
        await promise
    }
}
</script>



<style scoped>
.news__cards{
    display: flex;
    flex-wrap: wrap;
    /* justify-content: space-between; */
}
.news__card{
    width: 30%;
    margin-bottom: 10px;
    cursor: pointer;
    margin-right: 15px;
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