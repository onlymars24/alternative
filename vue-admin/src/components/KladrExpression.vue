<template>
  <span :style="kladr.relevance != 'Актуальный объект' ? 'color: red;' : 'color: #29bf56;'">
    {{ 
    kladr.name+
    (kladr.socr ? ', '+kladr.socr : '' )+
    (kladr.code ? ', '+kladr.code : '' )+
    (kladr.region ? ', '+kladr.region : '' )+
    (kladr.district ? ', '+kladr.district : '')+
    (kladr.city ? ', '+kladr.city : '')+
    (kladr.relevance ? ', '+kladr.relevance : '')
    }}
  </span>
</template>
<script>
export default
{
  props: [ 'kladr', 'select' ],
}
</script>