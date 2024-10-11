<template>
    <div class="common-layout" v-loading.fullscreen.lock="loading">
        <!-- <div class="container"> -->
            <Header/>
            <el-container>
                <el-main style="min-height: 500px;">
                    <h3 style="margin-bottom: 10px;">Новый контент для точек отправления и прибытия</h3>
                    <el-alert
                        :title="errorMessage"
                        type="error"
                        show-icon
                        v-if="errorMessage"
                        :closable="false">
                    </el-alert>
                    
                    <div class="" style="display: flex; align-items: flex-end;">
                        <div class="">
                            <p>Точка отправления</p>
                            <el-select
                                filterable
                                placeholder="Не выбрано"
                                style="width: 240px; margin-right: 20px;"
                                v-model="dispatchPointId"
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
                            <p>Точка прибытия</p>
                            <el-select
                                filterable
                                placeholder="Не выбрано"
                                style="width: 240px; margin-right: 20px;"
                                v-model="arrivalPointId"
                                :disabled="!dispatchPointId"
                            >
                                <el-option
                                v-for="point in arrivalPoints"
                                :key="point.name"
                                :label="point.name"                   
                                :value="point.id"
                                />
                            </el-select>                
                        </div>
                        <div style="margin-top: 10px;">
                            <el-button @click="create" type="primary" :loading="loading" :disabled="!(dispatchPointId && arrivalPointId)">Создать</el-button>
                        </div>
                    </div>
                    <h3 style="margin-top: 30px;">Существующий контент для точек отправления и прибытия</h3>
                    <el-table :data="busRoutes" style="margin-top: 20px; width: 40%;">
                        <el-table-column prop="dispatchPointName" label="Точка отправления" />
                        <el-table-column prop="arrivalPointName" label="Точка прибытия"/>
                        <el-table-column label="Точка отправления" >
                            <template #default="scope">
                                <router-link :to="{ name: 'BusRoutesEdit', params: { dispatchPointName: scope.row.dispatchPointName, arrivalPointName: scope.row.arrivalPointName }}">Редактировать</router-link>
                            </template>
                        </el-table-column>
                    </el-table>  
                </el-main>

            </el-container>
        <!-- </div> -->
    </div>
</template>

<script>
// import axios from 'axios';
import axios from 'axios';
import axiosClient from '../../axios';
import Header from '../../components/admin/Header.vue'


export default {
//   components: { Header, ckeditor: CKEditor.component },
  components: { Header },
  data(){
    return {
        loading: false,
        points: [
            {
                name: 'point.name'
            }
        ],
        dispatchPoints: [],
        arrivalPoints: [],
        dispatchPointId: '',
        arrivalPointId: '',

        busRoutes: [],
        errorMessage: ''
    }
  },
  async mounted(){
    this.loading = true
    const promise = axiosClient
    .get('/dispatch_points')
    .then(response => {
        this.dispatchPoints = response.data
    })
    .catch(error => {
        console.log(error)
    })
    await promise
    this.getBusRoutes()
    this.loading = false
  },
  methods: {
    async create(){
        this.errorMessage = ''
        this.loading = true

        let dispatchPointName = this.dispatchPoints.filter(point => {
            return point.id == this.dispatchPointId
        })[0].name
        let arrivalPointName = this.arrivalPoints.filter(point => {
            return point.id == this.arrivalPointId
        })[0].name
        console.log(dispatchPointName, arrivalPointName)
        const promise = axiosClient
        .post('/bus/route/create', {dispatchPointName: dispatchPointName, arrivalPointName: arrivalPointName})
        .then(response => {
            console.log(response.data)
            this.dispatchPointId = ''
        })
        .catch(error => {
            console.log(error)
            if(error.status = 422){
                this.errorMessage = error.response.data.errorMessage
            }
            console.log(error)
        })
        await promise
        this.getBusRoutes()
        this.loading = false
    },
    async getBusRoutes(){
        const promise = axiosClient
        .get('/bus/routes')
        .then(response => {
            console.log(response.data)
            this.busRoutes = response.data.busRoutes
        })
        .catch(error => {
            console.log(error)
        })
        await promise        
    }
  },
  watch: {
    async dispatchPointId(id){
        this.arrivalPoints = []
        this.arrivalPointId = ''
        const promise = axiosClient
        .get('/arrival_points/'+this.dispatchPointId)
        .then(response => {
            this.arrivalPoints = JSON.parse(response.data.arrival_points)
        })
        .catch(error => {
            console.log(error)
        })
        await promise
    }
  }
}
</script>
<style>
  /* > * + * {
    margin-top: 0.75em;
  } */
  .ck ul,  .ck ol{
    margin: 0px;
    padding: 20px;
    /* list-style-type: inherit; */
  }
  .ck li{
    margin: 0px;
    padding: 0px;
    list-style-type: inherit;
  }
  .ck.ck-content h3.category {
        font-family: 'Bebas Neue';
        font-size: 20px;
        font-weight: bold;
        color: #555;
        letter-spacing: 10px;
        margin: 0;
        padding: 0;
    }

    .ck.ck-content h2.document-title {
        font-family: 'Bebas Neue';
        font-size: 50px;
        font-weight: bold;
        margin: 0;
        padding: 0;
        border: 0;
    }

    .ck.ck-content h3.document-subtitle {
        font-family: 'Bebas Neue';
        font-size: 20px;
        color: #555;
        margin: 0 0 1em;
        font-weight: normal;
        padding: 0;
    }

    .ck.ck-content p.info-box {
        --background-size: 30px;
        --background-color: #e91e63;
        padding: 1.2em 2em;
        border: 1px solid var(--background-color);
        background: linear-gradient(135deg, var(--background-color) 0%, var(--background-color) var(--background-size), transparent var(--background-size)), linear-gradient(135deg, transparent calc(100% - var(--background-size)), var(--background-color) calc(100% - var(--background-size)), var(--background-color));
        border-radius: 10px;
        margin: 1.5em 2em;
        box-shadow: 5px 5px 0 #ffe6ef;
    }

    .ck.ck-content blockquote.side-quote {
        font-family: 'Bebas Neue';
        font-style: normal;
        float: right;
        width: 35%;
        position: relative;
        border: 0;
        overflow: visible;
        z-index: 1;
        margin-left: 1em;
    }

    .ck.ck-content blockquote.side-quote::before {
        content: "“";
        position: absolute;
        top: -37px;
        left: -10px;
        display: block;
        font-size: 200px;
        color: #e7e7e7;
        z-index: -1;
        line-height: 1;
    }

    .ck.ck-content blockquote.side-quote p {
        font-size: 2em;
        line-height: 1;
    }

    .ck.ck-content blockquote.side-quote p:last-child:not(:first-child) {
        font-size: 1.3em;
        text-align: right;
        color: #555;
    }

    .ck.ck-content span.marker {
        background: yellow;
    }

    .ck.ck-content span.spoiler {
        background: #000;
        color: #000;
    }

    .ck.ck-content span.spoiler:hover {
        background: #000;
        color: #fff;
    }

    .ck.ck-content pre.fancy-code {
        border: 0;
        margin-left: 2em;
        margin-right: 2em;
        border-radius: 10px;
    }

    .ck.ck-content pre.fancy-code::before {
        content: "";
        display: block;
        height: 13px;
        background: url(data:image/svg+xml;base64,PHN2ZyBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1NCAxMyI+CiAgPGNpcmNsZSBjeD0iNi41IiBjeT0iNi41IiByPSI2LjUiIGZpbGw9IiNGMzZCNUMiLz4KICA8Y2lyY2xlIGN4PSIyNi41IiBjeT0iNi41IiByPSI2LjUiIGZpbGw9IiNGOUJFNEQiLz4KICA8Y2lyY2xlIGN4PSI0Ny41IiBjeT0iNi41IiByPSI2LjUiIGZpbGw9IiM1NkM0NTMiLz4KPC9zdmc+Cg==);
        margin-bottom: 8px;
        background-repeat: no-repeat;
    }

    .ck.ck-content pre.fancy-code-dark {
        background: #272822;
        color: #fff;
        box-shadow: 5px 5px 0 #0000001f;
    }

    .ck.ck-content pre.fancy-code-bright {
        background: #dddfe0;
        color: #000;
        box-shadow: 5px 5px 0 #b3b3b3;
    }
</style>