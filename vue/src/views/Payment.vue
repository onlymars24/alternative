<template>
    <button @click="pay" style="width: 400px; height: 200px; background-color: grey;">Списать деньги за заказ</button>
</template>
<script>
import axiosClient from '../axios';
import router from '../router'
export default
{
    methods: {
        async pay(){
            const promise = axiosClient
            .post('/order/confirm', {order_id: this.$route.params['order_id']})
            .then(response => {
            })
            await promise
            router.push({ name: 'Account'})            
        }
    },
    mounted(){

    }
};
</script>
<style>

</style>