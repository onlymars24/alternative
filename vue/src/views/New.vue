<template>
  <div class="container">
    <Header/>
  </div>
    <div class="container">
    <h1 style="text-align: center; margin: 30px 0;">{{event.title}}</h1>
    <div class="new">
    <div class="new-wrapper">
      <div v-html="this.event.content"></div>
    </div>
    </div>
  </div>
  <hr class="bef__footer">
    <Footer/>
</template>
<script>
  import Header from '../components/Header.vue';
  import Footer from '../components/Footer.vue'
  import axiosClient from "../axios"

  export default
  {
    components:{Header, Footer},
    data(){
      return{
        event: {},
        centent: ''
      }
    },
    async mounted(){
      const promise = axiosClient
      .get('/event?id='+this.$route.params['id'])
      .then(response => {
          this.event = response.data.event
          console.log(this.event)
          // this.content = JSON.parse(this.event.content)
      })
      .catch(error => {
          console.log(error)
      })
      await promise

      document.title = this.event.title;
      const descEl = document.querySelector('head meta[name="description"]');
      descEl.setAttribute('content', this.event.descr);

      const linkCan = document.querySelector('head link[rel="canonical"]');
      linkCan.setAttribute('href', 'https://росвокзалы.рф/автобус/'+this.dispatchEl.name+'/'+this.arrivalEl.name);


    }
  }
</script>
<style scoped>

/* Accordion */
.input-accord {
    position: absolute;
    opacity: 0;
    z-index: -1;
}
.new-wrapper {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
    margin:0 auto;
    background-color: white;
    padding: 40px;
}

@media (min-width: 320px) and (max-width: 768px){
  .faq__menu-and-accord{
    flex-direction: column;
  } 
  .accordion-wrapper{
    max-width: 100%;
    padding: 20px;
    margin-top: 15px;
  }
}
  
</style>