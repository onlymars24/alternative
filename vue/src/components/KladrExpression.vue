<template>
    <span>{{ 
    kladr.name+', '+kladr.socr+', '+kladr.code+
    (kladr.region ? ', '+kladr.region : '' )+
    (kladr.district ? ', '+kladr.district : '')+
    (kladr.city ? ', '+kladr.city : '') 
    }}</span>
  </template>
  <script>
  export default
  {
    props: [ 'kladr' ],
  }
  </script>