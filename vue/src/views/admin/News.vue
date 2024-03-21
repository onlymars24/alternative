<template>
    
    <Header/>
    <el-container v-loading.fullscreen.lock="loading">
        <el-main style="min-height: 500px;" v-if="!loading">
            <div>
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for=""><strong>Новая новость</strong></label><br>
                    <label for="">Название новости</label><br>
                    <el-input v-model="newBusStation.name"></el-input>
                </div> 
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Описание</label><br>
                    <el-input type="textarea" v-model="newBusStation.description"></el-input>
                </div>                
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Дата новости</label><br>
                    <Calendar @setDateFilter="setDateFilter" :filterName="'created_at'"/>  
                </div> 
                <div>
                    <label for="">Привязка к автовокзалам</label><br>
                    <el-select v-model="newBusStation.dispatchPointId">
                        <el-option
                        v-for="point in dispatchPoints"
                        :key="point.id"
                        :label="point.name"
                        :value="point.id">
                        </el-option>
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
                    <label for=""><strong>Выберите существующую новость для редактирования:</strong></label><br>
                    <el-select v-model="selectedStationId">
                        <el-option
                        v-for="station in busStations"
                        :key="station.id"
                        :label="station.fixName"
                        :value="station.id">
                        </el-option>
                    </el-select>
                </div>
                <hr>
                <template v-for="station in busStations">
                    <template v-if="selectedStationId == station.id">
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
                            <el-link :href="url+'/автовокзал/'+station.fixTitle" target="_blank" type="primary">{{ url+'/автовокзал/'+station.fixTitle }}</el-link>
                        </div>
                        <div class="">
                            <label for="">Точка отправления</label><br>
                            <el-select v-model="station.dispatch_point_id">
                                <el-option
                                v-for="point in dispatchPoints"
                                :key="point.id"
                                :label="point.name"
                                :value="point.id">
                                </el-option>
                            </el-select>
                        </div>
                        <div class="">
                            <el-checkbox v-model="station.booleanHidden">Скрыть</el-checkbox>
                        </div>
                        <div style="display: flex; align-items: flex-start;">
                            <div>
                                {{ testVal }}
                                <label for="">Контент страницы</label>
                                <CkEditor v-model="testVal"/>
                            </div>
                            <!-- <el-button type="danger" style="margin-left: 15px;" @click="deleteStation(station.id)">Удалить автовокзал</el-button> -->
                        </div>
                    </template>
                </template>
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
import CkEditor from '../../components/admin/CkEditor.vue'
import Calendar from '../../components/admin/Calendar.vue'


export default
{
    components: { Header, BusLoading, CkEditor, Calendar },
    data() {
        return {
            loading: true,
            busStations: [],
            newBusStation: {
                title: '',
                name: '',
                dispatchPointId: '',
                description: '',
                hidden: true
            },
            dispatchPoints: [],
            selectedStationId: '',
            url: window.location.origin,
            testVal: ''
        }
    },
    async mounted(){
        console.log(window.location)
        this.getAll()
        const promise = axiosClient
        .get('/dispatch_points/')
        .then(response => {
            this.dispatchPoints = response.data
            console.log(this.dispatchPoints)
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
            this.selectedStationId = this.busStations[0].id
        },
        async createStation(){
            console.log(this.newBusStation)
            this.loading = true
            const promise = axiosAdmin
            .post('/bus/station/create', {title: this.newBusStation.title.replace(/\s/g, '_'), 
                                            name: this.newBusStation.name, 
                                            description: this.newBusStation.description, 
                                            dispatch_point_id: this.newBusStation.dispatchPointId, 
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
            this.newBusStation.dispatchPointId = ''
            this.newBusStation.hidden = false
            this.getAll()
            this.loading = false
        },
        async editStation(id, title, name, description, content, dispatch_point_id, hidden){
            this.loading = true
            console.log(id, title, content, dispatch_point_id, hidden)
            const promise = axiosAdmin
            .post('/bus/station/edit', {id: id,
                title: title.replace(/\s/g, '_'), 
                name: name, 
                description: description, 
                content: content, 
                dispatch_point_id: dispatch_point_id, 
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
    }
}
</script>
<style>

</style>