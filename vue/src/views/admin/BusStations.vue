<template>

    <Header/>
    <el-container v-loading.fullscreen.lock="loading">
        <el-main style="min-height: 500px;" v-if="!loading">
            <!-- {{ newBusStation }} -->
            <div>
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for=""><strong>Новый автовокзал</strong></label><br>
                    <label for="">Название автовокзала</label><br>
                    <el-input v-model="newBusStation.name"></el-input>
                </div> 
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Описание (description)</label><br>
                    <el-input type="textarea" v-model="newBusStation.description"></el-input>
                </div>
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">URL</label><br>
                    <el-input v-model="newBusStation.title"></el-input>
                </div>



                <div style="margin-bottom: 10px;">
                    <label for="">Точка отправления(населённый пункт)</label><br>
                    <el-select style="width: 25%;" v-model="newBusStation.kladrId" filterable>
                        <template v-for="el in dispatchData" :key="el.id">
                            <el-option
                                v-if="!el.hasOwnProperty('details')"
                                :label="el.name"
                                :value="el.id">
                                <strong class="big__font-size">{{el.name}}{{el.region || el.district ? ', ' : '' }}</strong>
                                <span style="font-size: 16px;">{{el.region}}{{(el.district && !el.region) || !el.district? '' : ', ' }}{{el.district}}</span>
                            </el-option>
                        </template>
                    </el-select>
                </div>
                <div style="margin-bottom: 10px;">
                    <label for="">Точка отправления(автовокзал)</label><br>
                    <el-select style="width: 25%;" v-model="newBusStation.dispatchPointId" filterable>
                        <template v-for="el in dispatchData" :key="el.id">
                            <el-option
                                v-if="el.hasOwnProperty('details')"
                                :label="el.name"
                                :value="el.id">
                                <strong>{{el.name}}{{el.region || el.details ? ', ' : '' }}</strong>
                                <span>{{el.region}}{{(el.details && !el.region) || !el.details? '' : ', ' }}{{el.details}}</span>
                            </el-option>
                        </template>
                    </el-select>
                </div>


                <div class="">
                    <el-checkbox v-model="newBusStation.hidden">Скрыть</el-checkbox>
                </div>
                <el-button type="primary" style="margin-top: 10px;" @click="createStation" :disabled="!(newBusStation.title && newBusStation.dispatchPointId)">Добавить</el-button>
            </div>    
            <hr>
            <!-- <el-tabs :tab-position="'left'" v-if="!loading"> -->
                <!-- <template v-for="station in busStations"> -->
                    <!-- <el-tab-pane :label="station.fixTitle"> -->
            <div>
                <div class="" style="margin-bottom: 20px;">
                    <label for=""><strong>Выберите существующий автовокзал для редактирования:</strong></label><br>
                    <el-select style="width: 25%;" v-model="selectedStationId">
                        <el-option
                        v-for="station in busStations"
                        :key="station.id"
                        :label="station.name"
                        :value="station.id">
                        </el-option>
                    </el-select>
                </div>
                <hr>
                <!-- <template v-for="station in busStations"> -->

                    <template v-if="station">
                        <div class="" style="width: 25%; margin-bottom: 10px;">
                            <label for="">Название автовокзала</label>
                            <el-input v-model="station.name" />
                        </div>
                        <div style="width: 25%; margin-bottom: 10px;">
                            <label for="">Описание (description)</label><br>
                            <el-input type="textarea" v-model="station.description"></el-input>
                        </div>                          
                        <div class="" style="width: 25%; margin-bottom: 10px;">
                            <label for="">URL</label>
                            <el-input v-model="station.title" />
                            <el-link :href="url+'/автовокзал/'+station.title" target="_blank" type="primary">{{ url+'/автовокзал/'+station.title }}</el-link>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label for="">Точка отправления(населённый пункт)</label><br>
                            <el-select style="width: 25%;" v-model="station.kladr_id" filterable>
                                <template v-for="el in dispatchData" :key="el.id">
                                    <el-option
                                        v-if="!el.hasOwnProperty('details')"
                                        :label="el.name"
                                        :value="el.id">
                                        <strong class="big__font-size">{{el.name}}{{el.region || el.district ? ', ' : '' }}</strong>
                                        <span style="font-size: 16px;">{{el.region}}{{(el.district && !el.region) || !el.district? '' : ', ' }}{{el.district}}</span>
                                    </el-option>
                                </template>
                            </el-select>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label for="">Точка отправления(автовокзал)</label><br>
                            <el-select style="width: 25%;" v-model="station.dispatch_point_id" filterable>
                                <template v-for="el in dispatchData" :key="el.id">
                                    <el-option
                                        v-if="el.hasOwnProperty('details')"
                                        :label="el.name"
                                        :value="el.id">
                                        <strong>{{el.name}}{{el.region || el.details ? ', ' : '' }}</strong>
                                        <span>{{el.region}}{{(el.details && !el.region) || !el.details? '' : ', ' }}{{el.details}}</span>
                                    </el-option>
                                </template>
                            </el-select>
                        </div>
                        <div class="">
                            <el-checkbox v-model="station.booleanHidden">Скрыть</el-checkbox>
                        </div>
                        <div style="display: flex; align-items: flex-start;">
                            <div>
                                <label for="">Контент страницы</label>
                                <HtmlEditor
                                :key="paramKey"
                                :id="station.id"
                                :name="station.name"
                                :description="station.description"
                                :title="station.title"
                                :data="station.data"
                                :hidden="station.booleanHidden"
                                :dispatch_point_id="station.dispatch_point_id"
                                :kladr_id="station.kladr_id"
                                @deleteStation="deleteStation"
                                @editStation="editStation"/>
                            </div>
                            <!-- <el-button type="danger" style="margin-left: 15px;" @click="deleteStation(station.id)">Удалить автовокзал</el-button> -->
                        </div>
                    </template>
                <!-- </template> -->
            </div>
                    <!-- </el-tab-pane> -->
                <!-- </template> -->
            <!-- </el-tabs> -->
        </el-main>
    </el-container>
</template>

<script>
import ru from 'element-plus/dist/locale/ru.mjs'
import axiosAdmin from '../../axiosAdmin'
import axiosClient from "../../axios";
import Header from '../../components/admin/Header.vue'
import BusLoading from '../../components/BusLoading.vue'
import HtmlEditor from '../../components/admin/HtmlEditor.vue'


export default
{
    components: { Header, BusLoading, HtmlEditor },
    data() {
        return {
            loading: true,
            busStations: [],
            newBusStation: {
                title: '',
                name: '',
                dispatchPointId: '',
                kladrId: '',
                description: '',
                hidden: true
            },
            dispatchData: [],
            selectedStationId: '',
            station: null,
            url: window.location.origin,
            paramKey: 0
        }
    },
    async mounted(){
        console.log(window.location)
        this.getAll()
        const promise = axiosClient
        .get('/dispatch/points')
        .then(response => {
            this.dispatchData = response.data.dispatchPoints
            console.log(this.dispatchData)
        })
        .catch(error => {

        })
        await promise
        this.loading = false
    },
    methods: {
        async getAll(){
            this.busStations = []
            const promise = axiosAdmin
            .get('/bus/stations')
            .then(response => {
                this.busStations = response.data.busStations
            })
            .catch(error => {
                console.lot(error)
            })
            await promise
            this.busStations.forEach(station => {
                station.fixName = station.name
                station.fixTitle = station.title
                station.booleanHidden = station.hidden == 1 ? true : false
            })
            console.log(this.busStations)
            if(!this.selectedStationId){
                this.selectedStationId = this.busStations[0].id
            }
        },
        async createStation(){
            console.log(this.newBusStation)
            this.loading = true
            const promise = axiosAdmin
            .post('/bus/station/create', {title: this.newBusStation.title.replace(/\s/g, '_'),
                                            name: this.newBusStation.name,
                                            description: this.newBusStation.description,
                                            dispatch_point_id: this.newBusStation.dispatchPointId,
                                            kladr_id: this.newBusStation.kladrId,
                                            hidden: this.newBusStation.hidden})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.newBusStation.title = ''
            this.newBusStation.name = ''
            this.newBusStation.description = ''
            this.newBusStation.dispatchPointId = ''
            this.newBusStation.kladrPointId = ''
            this.newBusStation.hidden = false
            this.getAll()
            this.loading = false
        },
        
        async editStation(id, title, name, description, content, dispatch_point_id, kladr_id, hidden){
            this.loading = true
            console.log(id, title, content, dispatch_point_id, hidden)
            const promise = axiosAdmin
            .post('/bus/station/edit', {id: id,
                title: title.replace(/\s/g, '_'), 
                name: name, 
                description: description, 
                content: content, 
                dispatch_point_id: dispatch_point_id, 
                kladr_id: kladr_id, 
                hidden: hidden})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.getAll()
            this.loading = false
        },
        async deleteStation(id){
            if(!confirm('Вы уверены, что хотите удалить станцию? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.loading = true
            const promise = axiosAdmin
            .post('/bus/station/delete', {id: id})
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
    watch: {
        async selectedStationId(newId){
            const promise = axiosAdmin
            .get('/bus/station/id?id='+newId)
            .then(response => {
                console.log(response)
                this.station = response.data.station
                this.station.booleanHidden = this.station.hidden == 1 ? true : false
            })
            .catch(error => {
                console.log(error)
            })
            await promise

            this.paramKey++
        },
        'newBusStation.dispatchPointId': function(newValue, oldValue) {
            if(this.newBusStation.dispatchPointId){
                this.newBusStation.kladrId = '';
            }
        },
        'newBusStation.kladrId': function(newValue, oldValue) {
            if(this.newBusStation.kladrId){
                this.newBusStation.dispatchPointId = '';
            }
        },


        'station.dispatch_point_id': function(newValue, oldValue) {
            if(this.station.dispatch_point_id){
                this.station.kladr_id = '';
            }
        },
        'station.kladr_id': function(newValue, oldValue) {
            if(this.station.kladr_id){
                this.station.dispatch_point_id = '';
            }
        },

    }
}
</script>
<style>

</style>