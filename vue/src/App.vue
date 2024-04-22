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
    console.log(this.$route)
    console.log(location.hostname)
    let tempUtm = JSON.parse(localStorage.getItem('utm_data'))
    if(tempUtm && tempUtm.referrer_url){
      this.oldReferrer = tempUtm.referrer_url
    }

    this.newReferrer = !document.referrer.includes(location.hostname) ? document.referrer : this.oldReferrer
    let token = localStorage.getItem('authToken')
    let user = []
    
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
    console.log(this.$route.query.orderIdd)
    // if(!token && this.$route.query.orderId){
    //   console.log('need fixing!')
    // }
    if(this.utm_data_old){
      console.log('пустота считается не пустотой')
    }
    console.log(this.$route.query.utm_source)
    if(this.$route.query.utm_source && this.$route.query.utm_medium && this.$route.query.utm_campaign && this.$route.query.utm_content){
        this.utm_data_new = {
            utm_source: this.$route.query.utm_source,
            utm_medium: this.$route.query.utm_medium,
            utm_campaign: this.$route.query.utm_campaign,
            utm_content: this.$route.query.utm_content,
            referrer_url: this.newReferrer,
        }
        localStorage.setItem('utm_data', JSON.stringify(this.utm_data_new))
    }
    else{
      this.utm_data_old = JSON.parse(localStorage.getItem('utm_data'))
      if(this.utm_data_old){
        this.utm_data_old.referrer_url = this.newReferrer
      }
      else{
        this.utm_data_old = {
            utm_source: '',
            utm_medium: '',
            utm_campaign: '',
            utm_content: '',
            referrer_url: this.newReferrer,
        }
      }
      localStorage.setItem('utm_data', JSON.stringify(this.utm_data_old))
    }
    
    if(this.utm_data_old){
      console.log('utm_data_old exists!')
    }
    if(this.utm_data && this.utm_data.utm_source && this.utm_data.utm_medium && this.utm_data.utm_campaign && this.utm_data.utm_content){
      console.log(this.utm_data)
      console.log('OK utm')
    }
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
