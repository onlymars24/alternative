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
      arrivalPoints: [],

      newDispatchPoints: [],
      newArrivalPoints: [],
    }
  },
  computed: {

  },
  methods: {

  },
  async mounted() {
    return

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
    console.log(dispatchPoint.hasOwnProperty('kladr') && dispatchPoint.kladr.hasOwnProperty('arrival_points'))

    let arrivalPoint
    if(dispatchPoint){
        const promise2 = axiosClient
        .get('/arrival_points/'+dispatchPoint.id)
        .then(response => {
          console.log(response)
            this.arrivalPoints = response.data
        })
        .catch(error => {
          console.log(error)
        })
        await promise2
        arrivalPoint = this.arrivalPoints.filter(point => {
            return point.arrival_point_id == this.$route.params['arrival_point_id']
        })[0]
    }
    console.log(arrivalPoint)
    // console.log(arrivalPoint.hasOwnProperty('kladr') && arrivalPoint.kladr.hasOwnProperty('dispatch_points'))
    // return
    let newKladrDispatch = null
    let newKladrArrival = null
    if(dispatchPoint.hasOwnProperty('kladr') && dispatchPoint.kladr.hasOwnProperty('arrival_points') && dispatchPoint.kladr.arrival_points.length
      && arrivalPoint.hasOwnProperty('kladr') && arrivalPoint.kladr.hasOwnProperty('dispatch_points') && arrivalPoint.kladr.dispatch_points.length
    ){
      newKladrDispatch= arrivalPoint.kladr
      newKladrArrival = dispatchPoint.kladr
      const promise1 = axiosClient
      .get('/arrival/points?pointType=k&pointId='+newKladrDispatch.id)
      .then(response => {
          this.newArrivalPoints = response.data.arrivalPoints
      })
      .catch(error => {
        // console.log(error)
      })
      await promise1
      // console.log(newKladrDispatch, newKladrArrival)
      let tempNewKladrArrival = this.newArrivalPoints.filter(point => {
        return point.id == newKladrArrival.id && !point.hasOwnProperty('details')
      })[0]
      newKladrArrival = tempNewKladrArrival
    }
    // console.log(this.newArrivalPoints)
    // console.log(newKladrDispatch, newKladrArrival)
    // return
    if(!newKladrDispatch || !newKladrArrival){
      // console.log('неудачно')
      const promise3 = axiosClient
      .post('/send/race/existing', {
        status: 'Неудачная',
        points: this.$route.params['arrival_station_name']+' - '+this.$route.params['dispatch_station_name'],
        orderId: this.$route.params['order_id'],
        from_admin: this.$route.query.from_admin ? true : false
      })
      .then(response => {
        // console.log(response)
      })
      .catch(error => {
        // console.log(error)
      })
      await promise3
      //send mail
      router.push({ name: 'Races', params: { dispatch_name: this.$route.params['arrival_station_name'], arrival_name: this.$route.params['dispatch_station_name'] } })
      return
    }
    // console.log('удачно')

      // const promise4 = axiosClient
      // .post('/match/replacement', {
      //   dispatchPointName: arrivalPoint.name,
      //   arrivalPointName: dispatchPoint.name,
      // })
      // .then(response => {
      //     this.newPoints = response.data
      //     console.log(this.newPoints)
      // })
      // .catch(error => {
      //     console.log(error)
      //   })
      //   await promise4
      

    // this.dispatchEl.name = this.newPoints.newDispatchPointName ? this.newPoints.newDispatchPointName : arrivalPoint.name
    // this.arrivalEl.name = this.newPoints.newArrivalPointName ? this.newPoints.newArrivalPointName : dispatchPoint.name


    // console.log(this.race, this.dispatchEl.name, this.arrivalEl.name)

    // dispatchPoint = this.dispatchPoints.filter(point => {
    //     return point.name == this.dispatchEl.name
    // })[0]

    // if(dispatchPoint){
    //     const promise5 = axiosClient
    //     .get('/arrival_points/'+dispatchPoint.id)
    //     .then(response => {
    //       this.arrivalPoints = JSON.parse(response.data.arrival_points)
    //     })
    //     .catch(error => {
    //       console.log(error)
    //     })
    //     await promise5
    //     arrivalPoint = this.arrivalPoints.filter(point => {
    //         return point.name == this.arrivalEl.name
    //     })[0]
    // }
    // console.log(dispatchPoint, arrivalPoint)
  
    // if(dispatchPoint && arrivalPoint){
    //   console.log('da')
    //   this.dispatchEl.name = dispatchPoint.name
    //   this.arrivalEl.name = arrivalPoint.name
    //   this.pointsExisting = true
    //   this.status = 'Успешная'
    // }
    // else{
    //   console.log('net')
    //   this.status = 'Неудачная'
    // }
    const promise6 = axiosClient
    .post('/send/race/existing', {
      status: 'Успешная',
      points: newKladrDispatch.name+' - '+newKladrArrival.name,
      orderId: this.$route.params['order_id'],
      from_admin: this.$route.query.from_admin ? true : false
    })
    .then(response => {
      // console.log(response)
    })
    .catch(error => {
      // console.log(error)
    })
    await promise6
    //send mail
    router.push({ name: 'Races', params: { dispatch_name: newKladrDispatch.slug, arrival_name: newKladrArrival.slug } })
  },
};
</script>
<style>

</style>