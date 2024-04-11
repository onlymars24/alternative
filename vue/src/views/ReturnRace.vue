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
      race: [],
      status: '',
      newPoints: [],
      dispatchPoints: [],
      arrivalPoints: []
    }
  },
  computed: {

  },
  methods: {

  },
  async mounted() {


    const promise1 = axiosClient
      .get('/dispatch_points')
      .then(response => {
          this.dispatchPoints = response.data
      })
      .catch(error => {
        console.log(error)
      })
    await promise1
    let dispatchPoint = this.dispatchPoints.filter(point => {
        return point.id == this.$route.params['dispatch_point_id']
    })[0]
    console.log(dispatchPoint)

    let arrivalPoint
    if(dispatchPoint){
        const promise2 = axiosClient
        .get('/arrival_points/'+dispatchPoint.id)
        .then(response => {
            this.arrivalPoints = JSON.parse(response.data.arrival_points)
        })
        .catch(error => {
          console.log(error)
        })
        await promise2
        arrivalPoint = this.arrivalPoints.filter(point => {
            return point.id == this.$route.params['arrival_point_id']
        })[0]
    }
    console.log(arrivalPoint)

    if(!dispatchPoint || !arrivalPoint){
      console.log('неудачно')
      const promise3 = axiosClient
      .post('/send/race/existing', {
        status: 'Неудачная',
        points: this.$route.params['arrival_station_name']+' - '+this.$route.params['dispatch_station_name'],
        orderId: this.$route.params['order_id'],
        from_admin: this.$route.query.from_admin ? true : false
      })
      .then(response => {
        console.log(response)
      })
      .catch(error => {
        console.log(error)
      })
      await promise3
      //send mail
      router.push({ name: 'Races', params: { dispatch_name: this.$route.params['arrival_station_name'], arrival_name: this.$route.params['dispatch_station_name'] } })
      return
    }
    console.log('удачно')

      const promise4 = axiosClient
      .post('/match/replacement', {
        dispatchPointName: arrivalPoint.name,
        arrivalPointName: dispatchPoint.name,
      })
      .then(response => {
          this.newPoints = response.data
          console.log(this.newPoints)
      })
      .catch(error => {
          console.log(error)
        })
        await promise4
      

    this.dispatchEl.name = this.newPoints.newDispatchPointName ? this.newPoints.newDispatchPointName : arrivalPoint.name
    this.arrivalEl.name = this.newPoints.newArrivalPointName ? this.newPoints.newArrivalPointName : dispatchPoint.name


    console.log(this.race, this.dispatchEl.name, this.arrivalEl.name)

    dispatchPoint = this.dispatchPoints.filter(point => {
        return point.name == this.dispatchEl.name
    })[0]

    if(dispatchPoint){
        const promise5 = axiosClient
        .get('/arrival_points/'+dispatchPoint.id)
        .then(response => {
          this.arrivalPoints = JSON.parse(response.data.arrival_points)
        })
        .catch(error => {
          console.log(error)
        })
        await promise5
        arrivalPoint = this.arrivalPoints.filter(point => {
            return point.name == this.arrivalEl.name
        })[0]
    }
    console.log(dispatchPoint, arrivalPoint)
  
    if(dispatchPoint && arrivalPoint){
      console.log('da')
      this.dispatchEl.name = dispatchPoint.name
      this.arrivalEl.name = arrivalPoint.name        
      this.pointsExisting = true
      this.status = 'Успешная'
    }
    else{
      console.log('net')
      this.status = 'Неудачная'
    }
    const promise6 = axiosClient
    .post('/send/race/existing', {
      status: this.status,
      points: this.dispatchEl.name+' - '+this.arrivalEl.name,
      orderId: this.$route.params['order_id'],
      from_admin: this.$route.query.from_admin ? true : false
    })
    .then(response => {
      console.log(response)
    })
    .catch(error => {
      console.log(error)
    })
    await promise6
    //send mail
    router.push({ name: 'Races', params: { dispatch_name: this.dispatchEl.name, arrival_name: this.arrivalEl.name } })
  },
};
</script>
<style>

</style>
