<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import * as dayjs from 'dayjs'
import router from '../router'

export default{
    name: "Main",
    components: {
        HeaderMain,
        Footer,
    },
    data() {
        return {
            today: null
        }
    },
    methods: {
        
    },
    mounted() {
        // this.$route.params['date']
        this.today = dayjs().format('YYYY-MM-DD')
        router.push({ name: "Races", params: { dispatch_id: this.$route.params['dispatch_id'], dispatch_name: this.$route.params['dispatch_name'], arrival_id: this.$route.params['arrival_id'], arrival_name: this.$route.params['arrival_name'], date: this.today } });
    },
    computed: {

    }
}
</script>

<template>
    
</template>

<style>

</style>