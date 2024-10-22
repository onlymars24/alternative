<template>
       <div class="common-layout">
        <Header/>
        <div class="container" v-loading.fullscreen.lock="rolesLoading">  
            <el-container v-if="!smsLoading">
                <el-main>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <span>Администраторы</span>
                                </div>
                            </template>
                            <el-table :data="admins" style="width: 100%">
                                <el-table-column prop="email" label="Email" width="400" />
                            </el-table>
                        </el-card>
                    </div>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card">
                            <template #header>
                                <div class="card-header" style="display: flex; justify-content: space-between;">
                                    <span>Редакторы</span>
                                </div>
                            </template>
                            <el-table :data="editors" style="width: 100%">
                                <el-table-column prop="email" label="Email" width="400" />
                                <el-table-column
                                width="230">
                                <!-- <template #header>
                                    <el-checkbox label="Без связки с автовокзалом" v-model="noStation" />
                                </template>             -->
                                <template #default="scope">
                                    <div>
                                        <el-button type="danger" @click="editorsDelete(scope.row.id)">Снять с должности</el-button>
                                    </div>
                                </template>
                                </el-table-column>
                            </el-table>
                        </el-card>
                    </div>
                    <div style="margin-top: 20px;">
                        <el-card class="box-card" style="margin-top: 20px;">
                            <h4>Новый редактор</h4>
                            <div style="display: flex; width: 60%; justify-content: space-between;">
                                <div>
                                    <span class="demo-input-label">Email</span>
                                    <el-input v-model="newEmail"/>
                                </div>
                            </div>
                            <div>
                                <el-button style="margin-top: 10px;" type="primary" @click="editorsAdd">Добавить</el-button>
                            </div>
                        </el-card>
                    </div>
                </el-main>
            </el-container>
        </div>
    </div>
</template>

<script>
import Header from '../../components/admin/Header.vue'
import dayjs from 'dayjs';
import axiosAdmin from '../../axiosAdmin';

export default {
    components: {
        Header
    },
    data() {
        return {
            admins: [],
            editors: [],
            newEmail: '',
            rolesLoading: true
        }
    },
    async mounted() {
        this.getUser()
        this.getAllRoles()
    },
    methods: {
        async getAllRoles(){
            await axiosAdmin
            .get('/roles/admins')  
            .then(response => {
                console.log(response.data)
                this.admins = response.data.admins
            })
            .catch( error => {
                console.log(error)
            })

            await axiosAdmin
            .get('/roles/editors')
            .then(response => {
                console.log(response.data)
                this.editors = response.data.editors
            })
            .catch( error => {
                console.log(error)
            })     
            this.rolesLoading = false       
        },
        async editorsAdd(){
            this.rolesLoading = true
            await axiosAdmin
            .post('/roles/editors/add', {email: this.newEmail})
            .then(response => {
                console.log(response.data)
                this.editors = response.data.editors
            })
            .catch( error => {
                console.log(error)
            })
            this.newEmail = ''
            this.getAllRoles()
        },
        async editorsDelete(editorId){
            if(!confirm('Вы уверены, что хотите удалить станцию? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.rolesLoading = true
            await axiosAdmin
            .post('/roles/editors/delete', {editorId})
            .then(response => {
                console.log(response.data)
                this.editors = response.data.editors
            })
            .catch( error => {
                console.log(error)
            })
            this.getAllRoles()
        },
        async getUser(){
            await axiosAdmin
            .get('/member')
            .then(response => {
                console.log(response)
            })
            .catch(error => {  
                console.log(error)
            })

        }
    }
}

</script>