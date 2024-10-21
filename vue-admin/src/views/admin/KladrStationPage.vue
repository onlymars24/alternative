<template>
    <Header/>
    <el-container v-loading.fullscreen.lock="loading">
        <el-main style="min-height: 500px;" v-if="!loading">
            <h4>Создать новую страницу населённого пункта или автовокзала</h4>
            <KladrStationPageForm :formType="'create'" :page="newPage" :kladrs="kladrs" :stations="stations" @submitFrom="createPage"/>
            <hr>
            <div>
                <h4>Редактировать существующую страницу населённого пункта или автовокзала</h4>
                <div class="" style="margin-bottom: 20px;">
                    <label for=""><strong>Выберите существующую страницу населённого пункта для редактирования или выбора страницы автовокзала:</strong></label><br>
                    <el-select style="width: 25%;" v-model="selectedKladrPageId" filterable>
                        <el-option
                        v-for="el in kladrPages"
                        :key="el.id"
                        :label="el.name+', '+el.kladr.name+', '+el.kladr.region"
                        :value="el.id">
                        </el-option>
                    </el-select>
                </div>

                <div class="" style="margin-bottom: 20px;">
                    <label for=""><strong>Выберите существующую страницу автовокзала для редактирования:</strong></label><br>
                    <el-select style="width: 25%;" v-model="selectedStationPageId" filterable clearable :disabled="stationPages.length == 0">
                        <el-option
                        v-for="el in stationPages"
                        :key="el.id"
                        :label="el.name+', '+el.station.kladr.name+', '+el.station.kladr.region"
                        :value="el.id">
                        </el-option>
                    </el-select>
                </div>
                <hr>
                <template v-if="page">
                    <KladrStationPageForm :key="paramKey" :formType="'edit'" :page="page" :kladrs="kladrs" :stations="stations" @submitFrom="editPage" @deletePage="deletePage"/>
                </template>
            </div>
        </el-main>
    </el-container>
</template>

<script>
import ru from 'element-plus/dist/locale/ru.mjs'
import axiosAdmin from '../../axiosAdmin'
import Header from '../../components/admin/Header.vue'
import HtmlEditor from '../../components/admin/HtmlEditor.vue'
import KladrStationPageForm from '../../components/admin/KladrStationPageForm.vue';


export default
{
    components: { Header, HtmlEditor, KladrStationPageForm },
    provide() {
        return {
            paramK: 'hello'
        }
    },
    data() {
        return {
            loading: true,
            kladrPages: [],
            stationPages: [],
            newPage: {
                name: '',
                description: '',
                station_id: '',
                kladr_id: '',
                booleanHidden: true
            },
            kladrs: [],
            stations: [],
            selectedKladrPageId: '',
            selectedStationPageId: '',
            page: null,
            url: window.location.origin,
            paramKey: 0
        }
    },
    async mounted(){
        this.getAll()
        const promise = axiosAdmin
        .get('/kladrs/connected')
        .then(response => {
            this.kladrs = response.data.kladrs
        })
        .catch(error => {
            console.log(error)
        })
        await promise

        const promise1 = axiosAdmin
        .get('/stations')
        .then(response => {
            this.stations = response.data.stations
            console.log(this.stations)
        })
        .catch(error => {
            console.log(error)
        })
        await promise1
        this.loading = false
    },
    methods: {
        async getAll(){
            this.kladrPages = []
            const promise = axiosAdmin
            .get('/kladr/station/pages/kladr')
            .then(response => {
                console.log(response)
                this.kladrPages = response.data.pages
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            // this.busStationsKladrs.forEach(station => {
            //     station.fixName = station.name
            //     station.fixTitle = station.title
            //     station.booleanHidden = station.hidden == 1 ? true : false
            // })
            // console.log(this.busStations)
            if(!this.selectedKladrPageId){
                this.selectedKladrPageId = this.kladrPages[0].id
            }
        },
        async createPage(page){
            this.loading = true
            const promise = axiosAdmin
            .post('/kladr/station/page/create', page)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.newPage.name = ''
            this.newPage.description = ''
            this.newPage.station_id = ''
            this.newPage.kladr_id = ''
            this.newPage.hidden = false
            this.getAll()
            this.loading = false
            // location.reload(); return false;
        },
        
        async editPage(page){
            this.loading = true
            const promise = axiosAdmin
            .post('/kladr/station/page/edit', 
            page)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.getAll()
            this.loading = false
            location.reload(); return false
        },
        async deletePage(id){
            if(!confirm('Вы уверены, что хотите удалить станцию? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.loading = true
            const promise = axiosAdmin
            .post('/kladr/station/page/delete', {id: id})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.getAll()
            this.loading = false
            location.reload(); return false;
        }
    },
    watch: {
        async selectedKladrPageId(newId){
            this.selectedStationPageId = ''
            const promise1 = axiosAdmin
            .get('/kladr/station/page/id?id='+newId)
            .then(response => {
                console.log(response)
                this.page = response.data.page
                this.page.booleanHidden = this.page.hidden == 1 ? true : false
                this.page.new_url_region_code = this.page.url_region_code+(this.page.kladr.region ? (' - '+this.page.kladr.region) : '')
            })
            .catch(error => {
                console.log(error)
            })
            await promise1

            const promise2 = axiosAdmin
            .get('/kladr/station/pages/station?kladrId='+this.page.kladr_id)
            .then(response => {
                console.log(response)
                this.stationPages = response.data.pages
            })
            .catch(error => {
                console.log(error)
            })
            await promise2
            this.paramKey++
        },

        async selectedStationPageId(newId){
            console.log(newId, this.selectedKladrPageId)
            const promise1 = axiosAdmin
            .get('/kladr/station/page/id?id='+(newId ? newId : this.selectedKladrPageId))
            .then(response => {
                console.log(response)
                this.page = response.data.page
                this.page.booleanHidden = this.page.hidden == 1 ? true : false
                this.page.new_url_region_code = this.page.url_region_code+(this.page.station && this.page.station.kladr && this.page.station.kladr.region ? (' - '+this.page.station.kladr.region) : (' - '+this.page.kladr.region))
            })
            .catch(error => {
                console.log(error)
            })
            await promise1

            this.paramKey++
        }
    }
}
</script>
<style>

</style>