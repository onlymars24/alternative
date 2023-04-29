<script>
import axiosClient from './axios';
import router from './router'

export default
{
  data()
  {
    return{
      openInputs: false,
    }
  },
  async mounted(){
    let token = localStorage.getItem('authToken')
    let user = []
    if(token){
      console.log('there is token')
      const promise = axiosClient
      .get('/user')
      .then(response => {
        user = response.data.user
      })
      .catch(error => {
        console.log('there is user')
        localStorage.removeItem('authToken')
        router.push({name: 'Login'})        
      })

    }
  }
}
</script>

<template>
  
  <router-view @click.prevent="$store.commit('windowHeader', 0)"></router-view>

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
