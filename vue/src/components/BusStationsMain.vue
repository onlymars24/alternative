<template>
    <div class="bus__stations-main">
        <div v-for="(region, key) in mainPages" class="bus__stations-main__region">
            <p class="bus__stations-main__region-name">{{key}}</p>
            <ul class="bus__stations-main__region-list">
                <li v-for="(kladrPage, key) in region">
                    <div class="bus__stations-main__kladr-list">
                        <p><h3><a :href="'/автовокзал/'+kladrPage.kladr.name" :title="kladrPage.name">{{ kladrPage.name }}:</a></h3></p>
                        <p v-for="(stationPage, keay) in kladrPage.stationPages">
                            <a :href="'/автовокзал/'+stationPage.station.name" :title="stationPage.name">{{ stationPage.name }}</a>
                        </p>
                    </div>
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
                mainPages: []
            }
        },
        async mounted(){
            const promise = axiosClient
            .get('/main/pages')
            .then(response => {
                console.log(response)
                this.mainPages = response.data.pagesOnMain
            })
            .catch(error => {
                console.log(error)
            })
            await promise
        }

    }
</script>
<style>
    .bus__stations-main{
        /* display: flex;
        flex-wrap: wrap; */
    }

    .bus__stations-main__region{
        /* width: 50%; */
    }

    .bus__stations-main__region-name{
        /* border-bottom: 2px solid #0275fe; */
        margin-bottom: 8px;
        display: inline-block;
    }
    .bus__stations-main__region-list{
        display: block;
        list-style-type: none;
    }

    .bus__stations-main__region-list h3{
        font-size: 1.1em;
        margin-bottom: 4px;
        display: flex;
    }
    .bus__stations-main__region-list p{
        /* width: 25%; */
        margin-bottom: 20px;
    }

    

    .bus__stations-main__region{
        /* margin-bottom: 5px; */
    }
    
    .bus__stations-main__kladr-list{
        margin-left: 19px;
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .bus__stations-main__kladr-list p{
        list-style-type: disc;
        width: auto;
        margin-bottom: 4px;
        margin-right: 15px;
        
    }

    @media (max-width: 768px) {
        .bus__stations-main__region{
            width: 100%;
        }
        .bus__stations-main__region-list p{
            width: 100%;
        }
        /* .bus__stations-main__kladr-list li{
            width: auto;
        } */
    }
</style>