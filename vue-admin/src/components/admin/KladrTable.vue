<template>
    <div style="margin-bottom: 35px;">
        <p style="margin: 15px 0;"><strong>Точки {{ pointTypeName }}:</strong></p>  
        <el-table
            :data="points.data"
            border
            style="width: 100%">
            <el-table-column
            v-if="pointType == 'dispatch'"
            fixed 
            prop="id"
            :label="'ID точки '+pointTypeName"
            width="85">
            </el-table-column>
            <el-table-column
            v-else
            fixed 
            prop="arrival_point_id"
            :label="'ID точки '+pointTypeName"
            width="85">
            </el-table-column>
            <el-table-column
            fixed 
            width="300">
                <template #header>
                    <p style="margin-bottom: 7px;">Точка {{ pointTypeName }}</p>
                    <el-input
                    v-model="pointNameSearch"
                    size="mini"
                    placeholder="Название точки"/>
                </template>
                <template #default="scope">
                    <PointExpression :point="scope.row"/>
                </template>
            </el-table-column>
            <el-table-column
            fixed
            v-if="pointType == 'arrival'"
            width="260">
            <template #header>
                <p style="margin-bottom: 7px;">Точка отправления</p>
                <el-select v-model="dispatchPointId" clearable filterable>
                    <el-option
                        v-for="point in dispatchPoints"
                        :key="point.id"
                        :value="point.id"
                        :label="(point.name)+(point.region || point.details ? ', ' : '' )+(point.region)+((point.details && !point.region) || !point.details? '' : ', ')+(point.details)"
                    >
                    <PointExpression :point="point"/>
                    </el-option>
                </el-select>
            </template>
            <template #default="scope">
                <PointExpression :point="scope.row.dispatch_point"/>
            </template>
            </el-table-column>
            <el-table-column
            width="300">
            <template #header>
                <p style="margin-bottom: 7px;">Точка кладр</p>
                <el-select v-model="kladrId" filterable clearable :loading="loading" remote :remote-method="kladrFilter" style="margin-left: 10px;" :disabled="noKladr">
                    <el-option
                        v-for="kladr in kladrs"
                        :key="kladr.id"
                        :value="kladr.id"
                        :label="kladr.name"
                    >
                    <KladrExpression :kladr="kladr"/>
                    </el-option>
                </el-select>
            </template>
            <template #default="scope">
                <KladrExpression v-if="scope.row.kladr" :kladr="scope.row.kladr"/>
                <!-- {{ scope.row.kladr ? (scope.row.kladr.name+', '+scope.row.kladr.socr+', '+scope.row.kladr.code) : '' }} -->
            </template>
            </el-table-column>
            <el-table-column
            width="180">
            <template #header>
                <el-checkbox label="Без связки с кладр" v-model="noKladr" />
            </template>            
            <template #default="scope">
                <div v-if="scope.row.kladr">
                    <el-button v-if="pointType == 'dispatch'" type="danger" @click="addPoint(scope.row.id, null)">Отвязать кладр</el-button>
                    <el-button v-else type="danger" @click="addPoint(scope.row.arrival_point_id, null, scope.row.dispatch_point_id)">Отвязать кладр</el-button>
                </div>
                
            </template>
            </el-table-column>
            <el-table-column
            width="400">
            <template #header>
                Связать с кладр
            </template>  
            <template #default="scope">
                <el-select placeholder="" v-model="scope.row.new_kladr_id" filterable clearable :loading="loading" remote :remote-method="kladrFilter" style="margin-left: 10px;">
                    <el-option 
                        v-for="kladr in kladrs"
                        :key="kladr.id"
                        :label="kladr.name+', '+kladr.socr+', '+kladr.code+(kladr.region ? ', '+kladr.region : '' )+(kladr.district ? ', '+kladr.district : '')+(kladr.city ? ', '+kladr.city : '')+(kladr.relevance ? ', '+kladr.relevance : '')"
                        :value="kladr.id"
                        :style="kladr.relevance != 'Актуальный объект' ? 'color: red;' : 'color: #29bf56;'"
                    >
                    </el-option>
                </el-select>
                <el-button v-if="pointType == 'dispatch'" :disabled="!scope.row.new_kladr_id" type="primary" @click="addPoint(scope.row.id, scope.row.new_kladr_id)">Привязать кладр</el-button>
                <el-button v-else :disabled="!scope.row.new_kladr_id" type="primary" @click="addPoint(scope.row.arrival_point_id, scope.row.new_kladr_id, scope.row.dispatch_point_id)">Привязать кладр</el-button>
            </template>
            </el-table-column>

            <el-table-column
            width="400">
            <template #header>
                <p style="margin-bottom: 7px;">Автовокзал</p>
                <el-select v-model="stationId" filterable clearable :loading="loading" remote :remote-method="stationFilter" style="margin-left: 10px;">
                    <el-option 
                        v-for="station in stations"
                        :key="station.id"
                        :label="station.name"
                        :value="station.id"
                    >
                    </el-option>
                </el-select>

            </template>  
            <template #default="scope">
                {{ scope.row.station ? scope.row.station.name : ''  }}
            </template>
            </el-table-column>

            <el-table-column
            width="230">
            <template #header>
                <el-checkbox label="Без связки с автовокзалом" v-model="noStation" />
            </template>            
            <template #default="scope">
                <div v-if="scope.row.station">
                    <el-button type="danger" @click="addStationIntoPoint(scope.row.id, null)">Отвязать автовокзал</el-button>
                </div>
            </template>
            </el-table-column>

            
            <el-table-column
            width="450">
            <template #header>
                Связать с автовокзалом
            </template>  
            <template #default="scope">
                <el-select v-model="scope.row.new_station_id" filterable :loading="loading" remote :remote-method="stationFilter" style="margin-left: 10px;">
                    <el-option 
                        v-for="station in stations"
                        :key="station.id"
                        :label="station.name"
                        :value="station.id"
                    >
                    </el-option>
                </el-select>
                <el-button :disabled="!scope.row.new_station_id" type="primary" @click="addStationIntoPoint(scope.row.id, scope.row.new_station_id)">Привязать автовокзал</el-button>
            </template>
            </el-table-column>

            
        </el-table>
        <Bootstrap5Pagination
            :data="points"
            @pagination-change-page="getPoints"
            :limit="1"
        />  
    </div>

</template>
<script>
import axiosAdmin from '../../axiosAdmin';
import PointExpression from '../../components/PointExpression.vue';
import KladrExpression from '../../components/KladrExpression.vue';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';


export default
{
    components: { KladrExpression, PointExpression, Bootstrap5Pagination },
    props: ['pointType', 'pointTypeName'],
    emits: [],
    data() {
        return {
            kladrId: null,
            stationId: null,
            pointNameSearch: '',

            dispatchPoints: [],
            dispatchPointId: '',

            // kladrFilterValue: '',
            points: {},
            kladrs: [],
            loading: false,
            page: 1,
            stations:[],

            noKladr: false,
            noStation: false
        }
    },
    async mounted(){
        this.getPoints()
        if(this.pointType == 'dispatch'){
            return
        }
        this.getDispatchPoints()
        const promise = axiosAdmin
    },
    methods: {
        async kladrFilter(query){
            if(query.length <= 2){
                return
            }
            this.loading = true
            const promise =  axiosAdmin
            .get('/kladrs?kladrFilter='+query)
            .then(response => {
                this.kladrs = response.data.kladrs
                // console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.loading = false
        },
        async stationFilter(query){
            if(query.length <= 2){
                return
            }
            this.loading = true
            const promise =  axiosAdmin
            .get('/stations?stationFilter='+query)
            .then(response => {
                this.stations = response.data.stations
                // console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.loading = false
        },
        async addPoint(pointId, kladrId, dispatchPointId = null){
            if(!kladrId){
                if(!confirm('Вы уверены, что хотите отвязать кладр? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
                    return
                }
            }
            console.log(pointId, kladrId, dispatchPointId)
            let data = {}
            data[this.pointType+'PointId'] = pointId
            data['kladrId'] = kladrId
            if(this.pointType == 'arrival'){
                data['dispatchPointId'] = dispatchPointId
            }
            
            console.log(data)
            const promise = axiosAdmin
            .post('/kladrs/'+this.pointType+'/add', data)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.getPoints(this.page)
        },
        async addStationIntoPoint(pointId, stationId){
            if(!stationId){
                if(!confirm('Вы уверены, что хотите отвязать автовокзал? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
                    return
                }
            }
            console.log(pointId, stationId)
            let data = {}
            data[this.pointType+'PointId'] = pointId
            data['stationId'] = stationId
            
            console.log(data)
            const promise = axiosAdmin
            .post('/station/add/to/'+this.pointType+'/point', data)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.getPoints(this.page)
        },
        async getPoints(page = 1){
            this.page = page
            let data = {}
            data['page'] = page
            data['kladrId'] = this.kladrId
            data['pointNameSearch'] = this.pointNameSearch
            data['dispatchPointId'] = this.dispatchPointId
            data['stationId'] = this.stationId
            data['noKladr'] = this.noKladr
            data['noStation'] = this.noStation
            console.log('/'+this.pointType+'/points/paginate')
            const promise = axiosAdmin
            .post('/'+this.pointType+'/points/paginate', data)
            .then(response => {
                this.points = response.data.points
                console.log(response.data)
            })
            .catch( error => {
                console.log(error)
            })
            await promise
        },
        async getDispatchPoints(){
            const promise = axiosAdmin
            .get('/dispatch_points')
            .then(response => {
                this.dispatchPoints = response.data
            })
            .catch( error => {
                console.log(error)
            })
            await promise
        }
    },
    watch: {
        kladrId(){
            this.getPoints()
        },
        noKladr(){
            this.kladrId = ''
            this.getPoints()
        },
        pointNameSearch(){
            this.getPoints()
        },
        dispatchPointId(){
            this.getPoints()
        },
        stationId(){
            this.getPoints()
        },
        noStation(){
            this.getPoints()
        },
    }
}
</script>

<style>
.el-select-dropdown .el-select-dropdown__item {
    max-height: 100px; 
    line-height: 20px;
    height: auto;
    white-space: normal; 
    /* Разрешить перенос строк внутри опции */
     word-wrap: break-word; 
     margin-bottom: 10px;
    /* Переносить длинные слова на новую строку при необходимости */
}
</style>