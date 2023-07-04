<!-- eslint-disable -->
<template>
  <li @mousedown="$emit('fillArrival', $event)" :data-id="id" :data-name="name">
    <strong :data-id="id" :data-name="name">{{name}}{{region || details ? ', ' : '' }}</strong>
    <span :data-id="id" :data-name="name">{{region}}{{(details && !region) || !details? '' : ', ' }}{{details}}</span>
  </li>
 </template>
 <style>
 
 </style>

<script>

	export default{
		components: {},
		props: ['id', 'name', 'region', 'details'],
    emits: ['fillArrival'],
		data(){
			return{

			}
		},
    computed:{

    },
    methods: {

    },
    mounted(){
      
    }

	}
</script>