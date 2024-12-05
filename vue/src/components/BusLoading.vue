<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
  <div class="loader__outside">
    <img src="../assets/bus_loading.png">
    <p>Загрузка.....</p>  
    <div class="loader"></div>
  </div>
</template>
<script scoped>


export default
{
  data(){
    return{

    }
  },
  computed: {

  },
  methods: {

  }
};
</script>

<style scoped>
.loader__outside img{
  max-width: 90%;
}

.loader__outside p{
  color: grey;
}
</style>