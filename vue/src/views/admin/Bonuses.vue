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
                        <el-table :data="users">
                            <el-table-column prop="id" label="ID" width="80" />
                            <el-table-column prop="phone" label="Телефон" width="160" />
                            <el-table-column prop="bonuses" label="Бонусы"/>
                            <!-- <el-table-column prop="city" label="City" width="320" /> -->
                            <!-- <el-table-column prop="address" label="Address" width="600" /> -->
                            <!-- <el-table-column prop="zip" label="Zip" width="120" /> -->
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
                    </el-card>   

                    <el-card class="box-card" style="width: 35%; margin-top: 10px;">
                        <template #header>
                            <div class="card-header" style="display: flex; justify-content: space-between;">
                                <span>Список транзакций с бонусами</span>
                            </div>
                        </template>
                        <el-table :data="bonuses">
                            <el-table-column prop="user_phone" label="Телефон получателя" width="200" />
                            <!-- <el-table-column prop="amount" label="Транзакция"/> -->
                            <el-table-column prop="transaction" label="Транзакция" width="180">
                                <template #default="scope">
                                    <el-text class="mx-1" type="success" v-if="scope.row.transaction == 'plus'">+{{ scope.row.amount }}</el-text>
                                    <el-text class="mx-1" type="danger" v-if="scope.row.transaction == 'minus'">-{{ scope.row.amount }}</el-text>
                                </template>
                            </el-table-column>
                            <el-table-column prop="date" label="Дата" width="200" />
                            <!-- <el-table-column prop="address" label="Address" width="600" /> -->
                            <!-- <el-table-column prop="zip" label="Zip" width="120" /> -->
                        </el-table>                            
                    </el-card>   


    
                    <el-dialog v-model="plusModalOpened" :title="'Ночислить бонусы для пользователя: '+currentPlusRow.phone" width="550">
                        <el-form :model="form">
                        <el-form-item label="Количество бонусов" :label-width="formLabelWidth">
                            <el-input v-model="currentPlusRow.amount" autocomplete="off" />
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

export default
{
    components: {Header, PopupWindow},
    data() {
        return {
            users: [],
            bonuses: [],
            plusModalOpened: false,
            currentPlusRow: {},

            minusModalOpened: false,
            currentMinusRow: {},
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

        const promise2 = axiosClient
        .get('/bonuses/transactions')
        .then(response => {
            this.bonuses = response.data.bonuses.reverse()
        })
        .catch(error => {
            console.log(error)
        })
        await promise2
        this.bonuses.forEach(bonus => {
            bonus.date = dayjs(bonus.created_at).format('YYYY-MM-DD HH:mm:ss')
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
            .post('/bonuses/plus', {id: row.id, bonuses: row.amount})
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
            .post('/bonuses/minus', {id: row.id, bonuses: row.amount})
            .then(response => {
                this.users = response.data.users
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            location.reload(); return false;
        },
    }
}
</script>