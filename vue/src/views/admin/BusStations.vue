<template>
    
    <Header/>
    <el-container v-loading.fullscreen.lock="loading">
        <el-main style="min-height: 500px;" v-if="!loading">
            <el-form-item label="Новый автовокзал">
                <el-input v-model="newBusStationTitle"></el-input>
                <el-button type="primary" style="margin-top: 10px;" @click="createStation">Добавить</el-button> 
            </el-form-item>    
            <hr>
            <el-tabs :tab-position="'left'" v-if="!loading">
                <template v-for="station in busStations">
                    <el-tab-pane :label="station.fixTitle">
                        <div>
                            <div class="" style="width: 25%; margin-bottom: 20px;">
                                <label for="">Название автовокзала</label>
                                <el-input v-model="station.title" />
                            </div>
                            <div style="display: flex; align-items: flex-start;">
                                <div>
                                    <label for="">Контент страницы</label>  
                                    <HtmlEditor :id="station.id" :title="station.title" :data="station.data" @editStation="editStation"/>
                                </div>
                                <el-button type="danger" style="margin-left: 15px;" @click="deleteStation(station.id)">Удалить автовокзал</el-button>
                            </div>
                        </div>
                    </el-tab-pane>
                </template>
            </el-tabs>
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
            newBusStationTitle: ''
        }
    },
    async mounted(){
        this.loading = true
        this.getAll()
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
                station.fixTitle = station.title
            })
            console.log(this.busStations)
        },
        async createStation(){
            this.loading = true
            const promise = axiosAdmin
            .post('/bus/station/create', {title: this.newBusStationTitle})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.lot(error)
            })
            await promise
            this.newBusStationTitle = ''
            this.getAll()
            this.loading = false
        },
        async editStation(id, title, content){
            this.loading = true
            const promise = axiosAdmin
            .post('/bus/station/edit', {id: id, title: title, content: content})
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
                console.lot(error)
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