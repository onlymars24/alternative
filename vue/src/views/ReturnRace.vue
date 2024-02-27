<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
  <div class="loader__outside">
    <img src="../assets/bus_loading.png" style="max-width: 90%;">
    <p style="color: grey;">Загрузка.....</p>  
    <div class="loader"></div>
  </div>
</template>
<script scoped>
import router from '../router';
import axiosClient from '../axios'

export default
{
  data(){
    return{
      pointsExisting: false,
      arrivalEl: {
          id: null,
          name: null
      },
      dispatchEl: {
          id: null,
          name: null
      },
      race: []
    }
  },
  computed: {

  },
  methods: {

  },
  async mounted() {
    this.dispatchEl.id = this.$route.params['arrival_point_id']
    this.arrivalEl.id = this.$route.params['dispatch_point_id']
    console.log(this.race, this.dispatchEl.id, this.arrivalEl.id)

    let dispatchPoints =[]
    const promise1 = axiosClient
      .get('/dispatch_points')
      .then(response => {
          dispatchPoints = response.data
      });
    await promise1
    let dispatchPoint = dispatchPoints.filter(point => {
        return point.id == this.dispatchEl.id
    })[0]
    let arrivalPoints
    let arrivalPoint
    if(dispatchPoint){
        const promise2 = axiosClient
        .get('/arrival_points/'+dispatchPoint.id)
        .then(response => {
            arrivalPoints = JSON.parse(response.data.arrival_points)
        });
        await promise2
        arrivalPoint = arrivalPoints.filter(point => {
            return point.id == this.arrivalEl.id
        })[0]
    }
    console.log(dispatchPoint, arrivalPoint)
    if(dispatchPoint && arrivalPoint){
      console.log('da')
      this.dispatchEl.name = arrivalPoint.name
      this.arrivalEl.name = dispatchPoint.name
      this.pointsExisting = true
    }
    else{
      console.log('net')
      this.dispatchEl.name = this.$route.params['arrival_station_name']
      this.arrivalEl.name = this.$route.params['dispatch_station_name']
    }
    
    router.push({ name: 'Races', params: { dispatch_name: this.dispatchEl.name, arrival_name: this.arrivalEl.name } })
  },
};
</script>
<style>

</style>
