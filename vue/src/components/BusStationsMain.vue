<template>
    <!-- {{ busStationsMain }} -->
    <!-- Все автовокзалы на главной -->
    <div class="bus__stations-main">
        <h2 style="margin-bottom: 20px;">Список автовокзалов</h2>
        <div v-for="(region, key) in busStationsMain" class="bus__stations-main__region">
            <h3 class="bus__stations-main__region-name">{{key}}</h3>
            <ul class="bus__stations-main__region-list">
                <li v-for="(dispatch, key) in region">
                    <p><a :href="'/автовокзал/'+dispatch.name" target="_blank">{{ dispatch.name }}</a></p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import axiosClient from '../axios';

    export default{
        props: ['seat'],
        data(){
            return{
                busStationsMain: []
            }
        },
        async mounted(){
            const promise = axiosClient
            .get('/bus/stations/main')
            .then(response => {
                console.log(response)
                this.busStationsMain = response.data.busStationsMain
            })
            .catch(error => {

            })
            await promise
        }

    }
</script>
<style scoped>
    .bus__stations-main__region-name{
        /* border-bottom: 2px solid #0275fe; */
        display: inline-block;
    }
    .bus__stations-main__region-list{
        display: flex;
        flex-wrap: wrap;
    }
    .bus__stations-main__region-list li{
        width: 25%;
        margin-bottom: 20px;
    }
    .bus__stations-main__region{
        margin-bottom: 10px;
    }
    @media (max-width: 768px) {
        .bus__stations-main__region-list li{
            width: 50%;
        }
    }
</style>