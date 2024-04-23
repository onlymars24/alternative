<script>
import axiosClient from './axios';
import router from './router'
import PopupWindow from './components/PopupWindow.vue';

export default
{
  components: { PopupWindow },
  data()
  {
    return{
      openInputs: false,

      utm_data_old: null,
      utm_data_new: null,
      newReferrer: '',
      oldReferrer: '',

      unfixedUser: false,
      unfixedUserData: {
        
      }
    }
  },
  async mounted(){

    let token = localStorage.getItem('authToken')
    let user = []
    
    //start unfixed data
    this.unfixedUserData = JSON.parse(localStorage.getItem('unfixedUser'))
    if(this.unfixedUserData && this.unfixedUserData.phone && this.unfixedUserData.bankOrderId){
      this.unfixedUser = true
      console.log('need fixing')
    }
    if(token){
      const promise = axiosClient
      .get('/user')
      .then(response => {
        user = response.data.user
      })
      .catch(error => {
        localStorage.removeItem('authToken')
        router.push({name: 'Main'})
      })
      await promise
    }
    //end unfixed data


  
  }
}
</script>

<template>
  
  <router-view @click.prevent="$store.commit('windowHeader', 0)"></router-view>

  <PopupWindow v-if="unfixedUser" :content="10" :unfixedUserData="unfixedUserData"/>
</template>

<style>
*{
  box-sizing: border-box;
}
:root{
  --blue: #2196F3;
}
p{
  margin: 0px;
}
.blue__link
{
  color: var(--blue);
  cursor: pointer;
}
body
{
  background-color: #f5f5f5 !important;
}
.but-go
{
    max-width: 350px;
    width: 100%;
    height: 50px;
    background-color: var(--blue);
    border-radius: 30px;
    color: white;
    font-weight: bold;
    font-size: 20px;
    margin-top: 40px;
}
h2{
  margin-bottom: 0px;
}
/* headerCrumbs */
</style>
