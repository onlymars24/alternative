<template>
    <div style="margin-bottom: 35px;">
        <p style="margin: 15px 0;"><strong>Точки {{ pointTypeName }}:</strong></p>  
        <el-table
            :data="points.data"
            border
            style="width: 100%">
            <el-table-column
            v-if="pointType == 'dispatch'"
            prop="id"
            :label="'ID точки '+pointTypeName"
            width="180">
            </el-table-column>
            <el-table-column
            v-else
            prop="arrival_point_id"
            :label="'ID точки '+pointTypeName"
            width="180">
            </el-table-column>
            <el-table-column
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
            v-if="pointType == 'arrival'"
            width="300">
            <template #header>
                <!-- {{ dispatchPoints }} -->
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
                    >
                    <KladrExpression :kladr="kladr"/>
                    <!-- {{ kladr.name+', '+kladr.socr+', '+kladr.code+(kladr.region ? ', '+kladr.region : '' )+(kladr.district ? ', '+kladr.district : '')+(kladr.city ? ', '+kladr.city : '') }} -->
                    </el-option>
                </el-select>
            </template>
            <template #default="scope">
                <KladrExpression v-if="scope.row.kladr" :kladr="scope.row.kladr"/>
                <!-- {{ scope.row.kladr ? (scope.row.kladr.name+', '+scope.row.kladr.socr+', '+scope.row.kladr.code) : '' }} -->
            </template>
            </el-table-column>
            <el-table-column
            width="300">
            <template #header>
                <el-checkbox label="Без связки" v-model="noKladr" />
            </template>            
            <template #default="scope">
                <div v-if="scope.row.kladr">
                    <el-button v-if="pointType == 'dispatch'" type="danger" @click="addPoint(scope.row.id, null)">Удалить связку</el-button>
                    <el-button v-else type="danger" @click="addPoint(scope.row.arrival_point_id, null, scope.row.dispatch_point_id)">Удалить связку</el-button>
                </div>
                
            </template>
            </el-table-column>
            <el-table-column
            width="400">

            <template #default="scope">
                <el-select v-model="scope.row.new_kladr_id" filterable :loading="loading" remote :remote-method="kladrFilter" style="margin-left: 10px;">
                    <el-option
                        v-for="kladr in kladrs"
                        :key="kladr.id"
                        :label="kladr.name+', '+kladr.socr+', '+kladr.code+(kladr.region ? ', '+kladr.region : '' )+(kladr.district ? ', '+kladr.district : '')+(kladr.city ? ', '+kladr.city : '')"
                        :value="kladr.id"
                    >
                    </el-option>
                </el-select>
                <el-button v-if="pointType == 'dispatch'" :disabled="!scope.row.new_kladr_id" type="primary" @click="addPoint(scope.row.id, scope.row.new_kladr_id)">Связать точки</el-button>
                <el-button v-else :disabled="!scope.row.new_kladr_id" type="primary" @click="addPoint(scope.row.arrival_point_id, scope.row.new_kladr_id, scope.row.dispatch_point_id)">Связать точки</el-button>
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
            kladrId: '',
            pointNameSearch: '',

            dispatchPoints: [],
            dispatchPointId: '',

            // kladrFilterValue: '',
            points: {},
            kladrs: [],
            loading: false,
            page: 1,

            noKladr: false
        }
    },
    async mounted(){
        this.getPoints()
        if(this.pointType == 'dispatch'){
            return
        }
        this.getDispatchPoints()
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
        async addPoint(pointId, kladrId, dispatchPointId = null){
            if(!kladrId){
                if(!confirm('Вы уверены, что удалить связку? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
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
        async getPoints(page = 1){
            this.page = page
            let data = {}
            data['page'] = page
            data['kladrId'] = this.kladrId
            data['pointNameSearch'] = this.pointNameSearch
            data['dispatchPointId'] = this.dispatchPointId
            data['noKladr'] = this.noKladr
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
    }
}
</script>