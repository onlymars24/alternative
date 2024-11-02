<template>
    <div>
      <div class="container"><Header/></div>
    </div>
    <div class="container"> 
      <BusLoading v-if="loadingSeats"/>
    </div>
    <HeaderСrumbsVue :step="'first'" v-if="!loadingSeats" :race="race" />
    <div v-if="!loadingSeats" class="container">
      <div class="window-bus">
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
    }
  },
  async mounted(){
    console.log(document.referrer)
    const promise = axiosClient
      .get('/race?dispatchPointId='+this.$route.params['dispatch_point_id']+'&arrivalPointId='+this.$route.params['arrival_point_id']+'&date='+this.$route.params['date']+'&uid='+this.$route.params['race_id'])
      .then(response => {
          this.race = response.data
          console.log(this.race)
      })
      .catch(error => {
        console.log(error)
      });
    await promise
    if(!this.race){
      router.push({name: 'Main'})
      return
    }
    this.seats = this.race.seats
    this.updateSeats()
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