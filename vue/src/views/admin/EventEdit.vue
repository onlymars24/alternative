<template>
    
    <Header/>
    <el-container v-loading.fullscreen.lock="loading">
        <el-main style="min-height: 500px;" v-if="!loading">
            <div>
                <div class="" style="margin-bottom: 20px;">
                    <label for=""><strong>Выберите существующую новость для редактирования:</strong></label><br>
                    <el-select v-model="selectedEventId">
                        <el-option
                        v-for="event in events"
                        :key="event.id"
                        :label="event.title"
                        :value="event.id">
                        </el-option>
                    </el-select>
                </div>
                <!-- {{ currentEvent }} -->
                <div style="width: 25%; margin-bottom: 10px;">
                    <!-- <label for=""><strong>Новая новость</strong></label><br> -->
                    <label for="">Название новости</label><br>
                    <el-input v-model="currentEvent.title"></el-input>
                </div> 
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Описание</label><br>
                    <el-input type="textarea" v-model="currentEvent.descr"></el-input>
                </div>  
                <div style="width: 70%; margin-bottom: 10px;">
                    <!-- {{ testVal }} -->
                    <label for="">Контент страницы</label>
                    <CkEditor v-model="currentEvent.content"/>
                </div>              
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Дата новости</label><br>
                    <el-config-provider :locale="locale">
                        <el-date-picker
                        v-model="currentEvent.date"
                        type="date"
                        style="max-width: 100%;"
                        required
                        >
                        </el-date-picker>
                    </el-config-provider>
                </div> 
                <!-- <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Аватарка</label><br>
                    <input type="file">

                </div>
                <div class="">
                    <el-checkbox v-model="newEvent.hidden">Скрыть</el-checkbox>
                </div> -->
                <el-button type="primary" style="margin-top: 10px;" @click="editEvent" :disabled="false">Сохранить</el-button> 
            </div>    
            <!-- <hr v-show="true">
            <div v-show="true" class="block" style="width: 15%; margin-bottom: 10px;">
                <span class="demonstration">Аватар</span>
                <el-image style="margin-top: 10px;" :src="newEvent.avatar"></el-image>
                <el-button type="danger">Удалить аватар</el-button>
            </div> -->
            <hr>
            <div style="width: 50%; margin-bottom: 10px;">
                <label for="">Привязанные автовокзалы</label><br>
                <div>
                    <el-select v-model="newStationId" placeholder="Select" style="margin-right: 10px;">
                        <el-option
                        v-for="station in busStations"
                        :key="station.id"
                        :label="station.name"
                        :value="station.id">
                        </el-option>
                    </el-select>
                    <el-button type="primary" @click="addStation">Добавить</el-button>
                </div>
                <el-table
                :data="currentEvent.bus_stations"
                style="width: 35%; margin-top: 20px;">
                <el-table-column
                    prop="title"
                    label="Ссылка на автовокзал"
                    width="180">
                    <template #default="scope">
                        <a :href="url+'/автовокзал/'+scope.row.title">{{scope.row.name}}</a>
                    </template>
                    
                </el-table-column>
                <el-table-column label="">
                    <template #default="scope">
                        <el-button
                        size="small"
                        type="danger"
                        @click="deleteStation(currentEvent.id, scope.row.id)"
                        >Удалить</el-button
                        >
                    </template>
                </el-table-column>
                </el-table>

            </div>
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
import dayjs from 'dayjs'


export default
{
    components: { Header, BusLoading, CkEditor, Calendar },
    data() {
        return {
            pickerOptions: {
            disabledDate(time) {
              return time.getTime() > Date.now();
            },
            shortcuts: [{
              text: 'Today',
              onClick(picker) {
                picker.$emit('pick', new Date());
              }
            }, {
              text: 'Yesterday',
              onClick(picker) {
                const date = new Date();
                date.setTime(date.getTime() - 3600 * 1000 * 24);
                picker.$emit('pick', date);
              }
            }, {
              text: 'A week ago',
              onClick(picker) {
                const date = new Date();
                date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
                picker.$emit('pick', date);
              }
            }]
            },
            loading: false,
            busStations: [],
            currentEvent: {
            },
            events: [],
            selectedEventId: '',
            newStationId: '',
            url: window.location.origin
        }
    },
    async mounted(){
        this.loading = true
        this.getAll()
        const promise = axiosClient
        .get('/bus/stations/')
        .then(response => {
            this.busStations = response.data.busStations
            console.log(this.busStations)
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        this.loading = false
        console.log(this.events)
        
    },
    methods: {
        async getAll(){
            this.events = []
            const promise = axiosClient
            .get('/events/')
            .then(response => {
                this.events = response.data.events.reverse()
            })
            .catch(error => {

            })
            
            await promise
            this.selectedEventId = ''
            this.selectedEventId = this.events[0].id
        },
        async editEvent(){
            console.log(this.newBusStation)
            this.loading = true
            this.currentEvent.date = this.currentEvent.date ? dayjs(this.currentEvent.date).format('YYYY-MM-DD') : dayjs().format('YYYY-MM-DD')
            const promise = axiosAdmin
            .post('/event/edit', this.currentEvent)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            // this.getAll()
            // this.loading = false
            location.reload(); return false;
        },
        async addStation(){
            console.log(this.selectedEventId, this.newStationId)
            this.loading = true
            const promise = axiosAdmin
            .post('/event/add/station', {eventId: this.selectedEventId, stationId: this.newStationId})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            // this.getAll()
            // this.loading = false
            location.reload(); return false;
        },
        async deleteStation(eventId, stationId){
            if(!confirm('Вы уверены, что хотите удалить станцию из новости? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.loading = true
            const promise = axiosAdmin
            .post('/event/delete/station', {eventId: eventId, stationId: stationId})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            // this.getAll()
            // this.loading = false
            location.reload(); return false;
        }
    },
    watch: {
        async selectedEventId(){
            this.loading = true
            const promise = axiosClient
            .get('/event?id='+this.selectedEventId)
            .then(response => {
                this.currentEvent = response.data.event
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.loading = false
        }
    }
}
</script>
<style>

</style>