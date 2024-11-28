<template>
    <div>
      <div class="container"><Header/></div>
    </div>
    <div class="container"> 
      <!-- <pre>
        {{ race }}
      </pre> -->
      
      <BusLoading v-if="loadingSeats"/>
    </div>
    <HeaderСrumbsVue :step="'first'" v-if="!loadingSeats && !errorMessage" :race="race" />
    <div v-if="!loadingSeats" class="container">
      <div v-if="errorMessage" style="margin-top: 20px;">        
        <div class="alert alert-danger" role="alert">
            <p>{{ errorMessage }}</p>
        </div>
        <p style="font-size: 25px;"><strong>Выберите другой рейс, время или дату.</strong></p>
      </div>
      <div v-else class="window-bus">
        {{ $route.params['route_id'] }}
        <Seat :seats="seats" :columnsAmount="columnsAmount" :race="race"/>
      </div>
    </div>
    <!-- <Footer/> -->
  </template>

<script>
import Seat from '../components/Seat.vue';
import HeaderСrumbsVue from '../components/HeaderСrumbs.vue';
import Header from '../components/Header.vue';
import BusLoading from '../components/BusLoading.vue';
import axios from 'axios';
import axiosClient from '../axios'
import router from '../router'
import Footer from '../components/Footer.vue'

export default {
  components: { Seat, HeaderСrumbsVue, Header, BusLoading, Footer },
  data(){
    return {
      race: [],
      seats: [12, 12],
      columnsAmount: 0,
      loadingSeats: true,
      errorMessage: null
    }
  },
  async mounted(){
    console.log(document.referrer)
    await axiosClient
    .get('/race?dispatchPointId='+this.$route.params['dispatch_point_id']+'&arrivalPointId='+this.$route.params['arrival_point_id']+'&date='+this.$route.params['date']+'&uid='+this.$route.params['race_id'])
    .then(response => {
      console.log('не ошибка')
        this.race = response.data
        console.log(this.race)
        this.seats = this.race.seats
        this.updateSeats()
    })
    .catch(error => {
      console.log('ошибка')
      console.log(error)
      // this.errorMessage = error.response.data.errorMessage
    });
    // if(!this.race){
    //   router.push({name: 'Main'})
    //   return
    // }

    this.loadingSeats = false
    
  },
  methods: {
    updateSeats(){
      let newSeats = []
      let i = 0
      let lastNum = 0

      this.seats.forEach((seat) => {
        if(seat.name.substring(0, 6) == 'Место '){
          seat.name = Number(seat.name.substring(6))
        }
      })

      this.seats.forEach((seat) => {
        console.log(seat.name)
        while(seat.name - lastNum != 1){
          lastNum++
          newSeats.push({code: null, name: lastNum, type: null})
        }
        newSeats.push(seat)
        lastNum++
      })

      this.seats = newSeats
      newSeats = []
      let temp = []

      this.seats.forEach((seat) => {
        temp.push(seat)        
        if(seat.name % 4 == 0){
          newSeats.push(temp)
          temp = []
        }
      })
      newSeats.push(temp)
      temp = []
      this.columnsAmount = Math.ceil( this.seats.length/4)
      this.seats = newSeats
    }
  }

}
</script>