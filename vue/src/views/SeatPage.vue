<template>
    <HeaderСrumbsVue />
    <div class="container">
      <pre>{{ race }}</pre>
      <div class="window-bus">
        {{ $route.params['route_id'] }}
        <Seat v-if="!loadingSeats" :seats="seats" :columnsAmount="columnsAmount" :race="race"/>
      </div>
    </div>
  </template>

<script>
import Seat from '../components/Seat.vue';
import HeaderСrumbsVue from '../components/HeaderСrumbs.vue';
import axios from 'axios';

export default {
  components: { Seat, HeaderСrumbsVue },
  data(){
    return {
      race: [],
      seats: [12, 12],
      columnsAmount: 0,
      loadingSeats: true
    }
  },
  async mounted(){
    const promise = axios
      .get('http://localhost:8000/api/race/'+this.$route.params['race_id'])
      .then(response => (
          this.race = response.data
      ));
    await promise
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
        seat.name = Number(seat.name.substring(6))
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
      
      console.log(this.columnsAmount)
    }
  }

}
</script>