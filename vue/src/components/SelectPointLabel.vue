<script>
export default
{
  props: [ 'sourceId', 'name', 'region', 'district' ],
  data(){
    return {
        
    }
  },
  methods: {

  }
}
</script>

<template>
        <div style="display: flex; flex-wrap: wrap; align-items: flex-end; padding: 5px 0;">
          <span style="font-size: 15px;" 
          :style="sourceId.includes('kladrs') || sourceId.includes('stations') ? 'font-size: 19px;' : ''"
          >{{ name}}{{ region ? ',&nbsp;' : '' }}</span>
          <span style="font-size: 15px; color: #777676;">{{(region ? region : '')}}{{ district ? ',&nbsp;' : '' }}</span>
          <span style="font-size: 15px; color: #777676;" >{{(district ? district : '') }}</span>
        </div> 
</template>