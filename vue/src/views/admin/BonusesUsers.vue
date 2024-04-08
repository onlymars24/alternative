<template>
    <div class="common-layout" v-loading.fullscreen.lock="orderLoading">
        <!-- <div class="container"> -->
            <Header/>
            <el-container>
                <el-main>
                    <el-card class="box-card" style="width: 35%;">
                        <template #header>
                            <div class="card-header" style="display: flex; justify-content: space-between;">
                                <span>Список пользователей</span>
                            </div>
                        </template>
                        <el-table :data="paginatedUsers">
                            <el-table-column prop="id" label="ID" width="80" />
                            <el-table-column prop="phone" label="Телефон" width="160" />
                            <el-table-column prop="bonuses_balance" label="Бонусы"/>
                            <el-table-column label="Операции с бонусами" width="220" >
                                <template #default="scope">
                                    <el-button size="small" @click="openPlusModal(scope.row)"
                                    >Начислить</el-button
                                    >
                                    <el-button
                                    size="small"
                                    type="danger"
                                    @click="openMinusModal(scope.row)"
                                    >Списать</el-button
                                    >
                                </template>
                            </el-table-column>                  
                        </el-table>
                        <paginate
                            :page-count="pagesCount"
                            :click-handler="changePage"
                            :prev-text="'<<'"
                            :next-text="'>>'"
                            :container-cass="'pagination'"
                            :page-class="'page-item'"
                            v-model="page"
                        >
                        </paginate>                               
                    </el-card>   



    
                    <el-dialog v-model="plusModalOpened" :title="'Ночислить бонусы для пользователя: '+currentPlusRow.phone" width="550">
                        <el-form :model="form">
                        <el-form-item label="Количество бонусов" :label-width="formLabelWidth">
                            <el-input v-model="currentPlusRow.amount" autocomplete="off" />
                        </el-form-item>
                        <el-form-item label="Описание">
                            <el-input v-model="currentPlusRow.descr" type="textarea" />
                        </el-form-item>
                        </el-form>
                        <template #footer>
                        <div class="dialog-footer">
                            <el-button @click="plusModalOpened = false">Отмена</el-button>
                            <el-button type="primary" @click="plus(currentPlusRow)">
                            Начислить
                            </el-button>
                        </div>
                        </template>
                    </el-dialog>  

                    <el-dialog v-model="minusModalOpened" :title="'Списать бонусы для пользователя: '+currentMinusRow.phone" width="550">
                        <el-form :model="form">
                        <el-form-item label="Количество бонусов" :label-width="formLabelWidth">
                            <el-input v-model="currentMinusRow.amount" autocomplete="off" />
                        </el-form-item>
                        <el-form-item label="Описание">
                            <el-input v-model="currentMinusRow.descr" type="textarea" />
                        </el-form-item>
                        </el-form>
                        <template #footer>
                        <div class="dialog-footer">
                            <el-button @click="plusModalOpened = false">Отмена</el-button>
                            <el-button type="danger" @click="minus(currentMinusRow)">
                            Списать
                            </el-button>
                        </div>
                        </template>
                    </el-dialog>  
                </el-main>
            </el-container>
        <!-- </div> -->
    </div>
</template>
<script>
import axios from 'axios';
import axiosClient from '../../axios';
import Header from '../../components/admin/Header.vue'
import router from '../../router'
import PopupWindow from '../../components/PopupWindow.vue';
import dayjs from 'dayjs';
import Paginate from "vuejs-paginate-next";

export default
{
    components: {Header, PopupWindow, paginate: Paginate},
    data() {
        return {
            users: [],
            plusModalOpened: false,
            currentPlusRow: {},

            minusModalOpened: false,
            currentMinusRow: {},
            usersPerPage: 2,
            paginationOffset: 0,
        }
    },
    async mounted(){
        const promise1 = axiosClient
        .get('/users')
        .then(response => {
            this.users = response.data.users.reverse()
        })
        .catch(error => {
            console.log(error)
        })
        await promise1
        
        this.users.forEach(user => {
            user.dialogFormVisible = false
        })

    },
    methods: {
        openPlusModal(row){
            this.plusModalOpened = true
            this.currentPlusRow = row
            this.currentPlusRow.amount = ''
        },
        async plus(row){
            const promise = axiosClient
            .post('/bonuses/plus', {id: row.id, bonuses: row.amount, descr: row.descr})
            .then(response => {
                this.users = response.data.users
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            location.reload(); return false;
        },

        openMinusModal(row){
            this.minusModalOpened = true
            this.currentMinusRow = row
            this.currentMinusRow.amount = ''
        },
        async minus(row){
            const promise = axiosClient
            .post('/bonuses/minus', {id: row.id, bonuses: row.amount, descr: row.descr})
            .then(response => {
                this.users = response.data.users
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            location.reload(); return false;
        },

        changePage(page_num){
            this.page = page_num
            this.paginationOffset = (this.usersPerPage * page_num) - this.usersPerPage
            if(page_num === 1){
                this.$router.push({name: 'BonusesUsers'})
            }
            else{
                this.$router.push('?page='+page_num)
            }
        },
    },

    computed: {
        pagesCount(){
            return Math.ceil(this.users.length / this.usersPerPage);
        },
        paginatedUsers(){
            return this.users.slice(this.paginationOffset, this.paginationOffset + this.usersPerPage)
        },
    }
}
</script>