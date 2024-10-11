<template>
    
    <Header/>
    <el-container v-loading.fullscreen.lock="loading">
        <el-main style="min-height: 500px;" v-if="!loading">
            <!-- {{ newEvent }} -->
            <div>
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for=""><strong>Новая новость</strong></label><br>
                    <label for="">Название новости</label><br>
                    <el-input v-model="newEvent.title" required></el-input>
                </div> 
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Описание</label><br>
                    <el-input type="textarea" v-model="newEvent.descr" required></el-input>
                </div>  
                <div style="width: 70%; margin-bottom: 10px;">
                    <!-- {{ newEvent.content }} -->
                    <label for="">Контент страницы</label>
                    <CkEditor v-model="newEvent.content"/>
                </div>
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Дата новости</label><br>
                    <el-config-provider :locale="locale">
                        <el-date-picker
                        v-model="newEvent.date"
                        type="date"
                        style="max-width: 100%;"
                        required
                        >
                        </el-date-picker>
                    </el-config-provider>
                </div> 
                <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Привязка к автовокзалам</label><br>
                    <el-select v-model="newEvent.stations" multiple placeholder="">
                        <el-option
                        v-for="station in busStations"
                        :key="station.id"
                        :label="station.name"
                        :value="station.id">
                        </el-option>
                    </el-select>
                </div>
                <!-- <div style="width: 25%; margin-bottom: 10px;">
                    <label for="">Аватарка</label><br>
                    <input type="file" @change="changeImage">
                </div> -->
                <!-- <div class="">
                    <el-checkbox v-model="newEvent.hidden">Скрыть</el-checkbox>
                </div> -->
                <el-button type="primary" style="margin-top: 10px;" @click="createEvent" :disabled="false">Создать новость</el-button>
            </div>
            <hr>
        </el-main>
    </el-container>
</template>

<script>
import ru from 'element-plus/dist/locale/ru.mjs'
import axiosAdmin from '../../axiosAdmin'
import axiosClient from "../../axios"
import Header from '../../components/admin/Header.vue'
import CkEditor from '../../components/admin/CkEditor.vue'
import dayjs from 'dayjs'
import router from '../../router';


export default
{
    components: { Header, CkEditor },
    props: {
    value: {
        type: Object,
        required: true
        }
    },
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
            locale: ru,
            loading: false,
            busStations: [],
            newEvent: {
                title: '',
                descr: '',
                content: '',
                date: '',
                stations: [],
                image: '',
                // hidden: true
            },
        }
    },
    async mounted(){
        this.loading = true
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
    },
    methods: {
        async createEvent(){
            // console.log(this.newBusStation)
            this.loading = true
            // const formData = new FormData();
            // formData.append('image', this.newEvent.image);
            // console.log(formData)
            this.newEvent.date = this.newEvent.date ? dayjs(this.newEvent.date).format('YYYY-MM-DD') : dayjs().format('YYYY-MM-DD')
            const promise = axiosAdmin
            .post('/event/create', this.newEvent)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            router.push({ name: 'EventEdit' })
            this.loading = false
        },
        changeImage(event){
            this.newEvent.image = event.target.files[0];
            // const formData = new FormData();
            // formData.append('image', file);
            // console.log(formData)
        }
    },
    watch: {
        
    }
}
</script>
<style>

</style>