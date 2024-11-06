<template>
    <div class="line-head">
        <div class="container">
            <div class="race-progress breadcrumb" style="flex-wrap: wrap;">
                <div class="page-link not-passed"><a href="/">Билеты на автобус</a></div>
                <template v-for="page in pages">
                    <div><span class="arrow"></span></div>
                    <div class="page-link not-passed"><a v-if="page.href" :href="page.href">{{ page.name }}</a><span v-else>{{ page.name }}</span></div>                    
                </template>
            </div>
            <div class="race__info-head"></div>
        </div>
    </div>
</template>

<script>
    export default{
        props: ['pages'],
        data(){
            return {

            }
        }
    }
</script>

<style scoped>
    .race-progress {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }
    .not-passed {
        color: rgb(145, 145, 145);
        padding: 10px;
        font-size: 14px;
        /* position: relative; */
        text-align: center;
    }
    .passed {
        padding: 10px;
        /* padding-left: 0px; */
        font-size: 14px;
        /* position: relative; */
        color: black;
        /* font-weight: 700; */
        /* padding-bottom: 12px; */
        
        border-bottom: 3px solid #2196f3;
    }
    .arrow {
        display: block;
        background-image: url("/public/img/arrow-open-right-svgrepo-com.svg");
        width: 10px;
        height: 10px;
        background-position: center center;
        background-size: contain;
        background-repeat: no-repeat;
        /* right: -8px; */
        /* top: 17px; */
        /* position: absolute; */
    }
    .page-link{
        display: flex;
        align-items: center;
        padding: 0;
    }
    .page-link a{
        color: rgb(145, 145, 145);
    }
    .page-link a:hover{
        color: #2196f3;
    }
</style>