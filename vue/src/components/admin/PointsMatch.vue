<template>
    <div class="" style="margin-bottom: 250px;" v-loading.fullscreen.lock="loading">
        <p style=" margin-top: 30px;">Соответствия точек для обратных рейсов</p>
        <el-table :data="matches" style="width: 100%; margin-top: 10px;">
        <el-table-column label="ID точки из заказа, 
        которую меняем" prop="orderPointId" />
        <el-table-column label="Название точки из заказа, которую меняем" prop="orderPointName" />
        <el-table-column label="ID точки, на которую меняем" prop="matchPointId" />
        <el-table-column label="Название точки, на которую меняем" prop="matchPointName" />
        <el-table-column label="Тип точки" prop="pointType" />
        <el-table-column label="ID точки отправления" prop="dispatchPointId" />
        <el-table-column label="Название точки отправления" prop="dispatchPointName" />
        <el-table-column align="right">
        <template #default="scope">
            <el-button @click="deleteMatch(scope.row.id)" size="small"
            >Удалить</el-button
            >
        </template>
        </el-table-column>
        </el-table>
  <el-card class="box-card" style="width: 100%; margin-top: 30px;">
    <template #header>
      <div class="card-header">
        <span>Новое соответствие точек для обратных рейсов</span>
      </div>
    </template>
        <div class="" style="display: flex; align-items: flex-end;">
            <div class="">
                <p>Точка из заказа,<br>которую меняем</p>
                <el-select
                    v-model="newMatch.orderPointId"
                    filterable
                    placeholder=""
                    style="width: 240px; margin-right: 20px;"
                >
                    <el-option
                    v-for="point in points"
                    :key="point.name"
                    :label="point.name"
                    :value="point.id"
                    />
                </el-select>                
            </div>
            <div class="" style="margin: 0 25px 0 10px;">Соответствует =></div>
            <div class="">
                <p>Точка, на которую меняем</p>
                <el-select
                    v-model="newMatch.matchPointId"
                    filterable
                    placeholder=""
                    style="width: 240px; margin-right: 20px;"
                >
                    <el-option
                    v-for="point in points"
                    :key="point.name"
                    :label="point.name"                   
                    :value="point.id"
                    />
                </el-select>                
            </div>
            <div class="">
                <p>Точка отправления(необязательный<br> параметр для соответствия<br> с типом "Точка прибытия")</p>
                <el-select
                    v-model="newMatch.dispatchPointId"
                    filterable
                    placeholder=""
                    style="width: 240px; margin-right: 20px;"
                    :disabled="newMatch.pointType != 'Прибытие'"
                >
                    <el-option
                    v-for="point in dispatchPoints"
                    :key="point.name"
                    :label="point.name"                   
                    :value="point.id"
                    />
                </el-select>                
            </div>

            <div class="">
                <p>Тип точки</p>
                <el-select
                    v-model="newMatch.pointType"
                    filterable
                    placeholder=""
                    style="width: 240px; margin-right: 20px;"
                >
                    <el-option
                        label="Точка отправления"
                        value="Отправление"
                    />
                    <el-option
                        label="Точка прибытия"
                        value="Прибытие"
                    />
                </el-select>
            </div>
        </div>
        <el-button style="margin-top: 10px" type="primary" @click="create" :disabled="!(newMatch.orderPointId && newMatch.matchPointId && newMatch.pointType)">Создать</el-button>
  </el-card>
  </div>
</template>
<script>
import { ref } from "vue";
import router from '../../router'
import dayjs from 'dayjs'
import axiosClient from "../../axios";
import axiosAdmin from "../../axiosAdmin";

export default
{
    props: [],
    data() {
        return {
            matches: [],
            points: [],
            dispatchPoints: [],
            newMatch: {
                orderPointId: '',
                orderPointName: '',
                matchPointId: '',
                matchPointName: '',
                dispatchPointId: '',
                dispatchPointName: '',
                pointType: '',
            },
            loading: true
        }
    },
    methods: {
        async getAll(){
            const promise = axiosAdmin
            .get('/matches')
            .then(response => {
                console.log(response)
                this.matches = response.data.pointsMatches
            })
            .catch(error => {
                console.log(error)
            })
            await promise
        },
        async create(){
            this.loading = true
            this.newMatch.orderPointName = this.points.filter( point => {
                return point.id == this.newMatch.orderPointId
            })[0].name
            
            this.newMatch.matchPointName = this.points.filter( point => {
                return point.id == this.newMatch.matchPointId
            })[0].name

            if(this.newMatch.dispatchPointId){
                this.newMatch.dispatchPointName = this.dispatchPoints.filter( point => {
                    return point.id == this.newMatch.dispatchPointId
                })[0].name
            }
            
            console.log(this.newMatch)
            const promise = axiosAdmin
            .post('/match/create', this.newMatch)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.newMatch.orderPointName = this.newMatch.orderPointId = this.newMatch.matchPointId = this.newMatch.matchPointName = this.newMatch.pointType = ''
            this.getAll()
            this.loading = false
        },
        async deleteMatch(id){
            if(!confirm('Вы уверены, что хотите удалить match? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.loading = true
            const promise = axiosAdmin
            .post('/match/delete', {id})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.getAll()
            this.loading = false
        }
    },
    async mounted(){
        const promise1 = axiosAdmin
        .get('/points')
        .then(response => {
            this.points = response.data
            console.log(this.points)
        })
        .catch(error => {
            console.log(error)
        })
        await promise1

        const promise2 = axiosAdmin
        .get('/dispatch_points')
        .then(response => {
            this.dispatchPoints = response.data
            console.log(this.dispatchPoints)
        })
        .catch(error => {
            console.log(error)
        })
        await promise2

        this.getAll()
        this.loading = false
        // this.points.forEach(point => {
        //     point.label = (!point.name ? '' : point.name) + (point.region || point.details ? ', ' : '' ) + (!point.region ? '' : point.region) + ((point.details && !point.region) || !point.details? ' ' : ', ')+(!point.details ? '' : point.details)
        // })
    }
}
</script>
<style>
</style>