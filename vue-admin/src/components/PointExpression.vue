<template>
  <strong>{{point.name}}{{point.region || point.details ? ', ' : '' }}</strong>
  <span>{{point.region}}{{(point.details && !point.region) || !point.details? '' : ', ' }}{{point.details}}</span>
</template>
<script>
export default
{
  props: [ 'point' ],
}
</script>