<template>
    
    <Header/>
    <el-container v-loading.fullscreen.lock="loading">
        <el-main>
            <div style="margin: 20px 0;">
                <div>Выберите точку отправления:</div>   
                <el-select v-model="dispatchPointId"  @change="changeDispatchPoint">
                    <el-option
                        v-for="dispatchPoint in dispatchPoints"
                        :key="dispatchPoint.id"
                        :label="dispatchPoint.name"
                        :value="dispatchPoint.id"
                    >
                    </el-option>
                </el-select>
                <el-transfer v-model="popularPoints"
                :props="{
                    key: 'id',
                    label: 'label',
                }"
                filterable
                :titles="['Все точки прибытия', 'Популярные']"
                style="margin-top: 20px;"
                :data="arrivalPoints"
                />
            </div>
            <el-button type="primary" :loading-icon="Eleme" @click="savePopularPoints" :loading="loading">Сохранить</el-button>
        </el-main>
    </el-container>
</template>

<script>
import ru from 'element-plus/dist/locale/ru.mjs'
import axiosAdmin from '../../axiosAdmin'
import axiosClient from "../../axios";
import Header from '../../components/admin/Header.vue'
import BusLoading from '../../components/BusLoading.vue'


export default
{
    components: { Header, BusLoading },
    data() {
        return {
            data: this.generateData(),
            popularPoints: [],
            cacheArrivalPoints: {},
            arrivalPoints: [],
            dispatchPoints: [],
            dispatchPointId: '',
            loading: false
        }
    },
    async mounted(){
        this.loading = true
        const promise1 = axiosClient
        .get('/dispatch_points/')
        .then(response => {
            this.dispatchPoints = response.data
            this.dispatchPointId = this.dispatchPoints[0].id
            console.log(this.dispatchPoints)
        })
        .catch(error => {

        })
        await promise1

        this.changeDispatchPoint()
        this.loading = false
    },
    methods: {
        generateData() {
            const data = [];
            for (let i = 1; i <= 15; i++) {
            data.push({
                key: i,
                label: `Option ${ i }`,
                // disabled: i % 4 === 0
            });
            }
            return data;
        },
        async changeDispatchPoint(){
            console.log('changeDispatchPoint')
            this.loading = true
            const promise2 = axiosClient
            .get('/arrival_points/'+this.dispatchPointId)
            .then(response => {
                this.cacheArrivalPoints = response.data
                this.arrivalPoints = JSON.parse(response.data.arrival_points)
                this.popularPoints = JSON.parse(response.data.popular_arrival_points)
            })
            .catch(error => {

            })
            await promise2
            this.arrivalPoints.forEach(point => {
                point.label = (!point.name ? '' : point.name) + (point.region || point.details ? ', ' : '' ) + (!point.region ? '' : point.region) + ((point.details && !point.region) || !point.details? ' ' : ', ')+(!point.details ? '' : point.details)
            })
            this.loading = false
        },
        async savePopularPoints(){
            this.loading = true
            const promise = axiosClient
            .post('/popular/points/edit/', {id: this.dispatchPointId, popular_arrival_points: this.popularPoints})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.loading = false
        }
    },
    watch: {
        value(value){
            console.log(this.value)
            console.log(this.data)
        }
    }
}
</script>
<style>
    .el-transfer-panel{
        width: 45% !important;
    }
</style>