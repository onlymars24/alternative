<script>
import Header from '../components/Header.vue'
import axiosClient from '../axios'
import * as dayjs from 'dayjs'
import router from '../router'

export default{
    name: "Avtobus",
    components: {
        Header
    },
    data() {
        return {
            dispatchEl: {
                id: null,
                name: this.$route.params['dispatch_name']
            },
            arrivalEl: {
                id: null,
                name: this.$route.params['arrival_name']
            },
            today: null,
            dispatchData: [],
            arrivalData: [],
        }
    },
    methods: {

    },
    async mounted() {
        const promise1 = axiosClient
        .get("/dispatch_points")
        .then(response => (this.dispatchData = response.data));
        await promise1
        let tempDispatchEl =  this.dispatchData.filter(el => {
            return el.name && el.name.toUpperCase().indexOf(this.dispatchEl.name.toUpperCase()) !== -1
        });
        this.dispatchEl.id = tempDispatchEl[0].id
        const promise2 = axiosClient
            .get('/arrival_points/'+this.dispatchEl.id)
            .then(response => (this.arrivalData = JSON.parse(response.data)));
        await promise2
        let tempArrivalEl =  this.arrivalData.filter(el => {
            return el.name && el.name.toUpperCase().indexOf(this.arrivalEl.name.toUpperCase()) !== -1
        });
        this.arrivalEl.id = tempArrivalEl[0].id
        this.today = dayjs().format('YYYY-MM-DD')
        router.push({ name: 'Races', params: { dispatch_id: this.dispatchEl.id, dispatch_name: this.dispatchEl.name, arrival_id: this.arrivalEl.id, arrival_name: this.arrivalEl.name, date: this.today } })

    },
    computed: {

    }
}
</script>

<template>
    <div class="container">
        <Header/>
        <div style="margin-top: 30px;" class="loader__outside">
            <img src="../assets/bus_loading.png">
            <p style="color: grey;">Загрузка.....</p>  
            <div class="loader"></div>
        </div>
    </div>
    
   
</template>
<style>

</style>