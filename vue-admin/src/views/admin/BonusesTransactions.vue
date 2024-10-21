<template>
    <div class="common-layout" v-loading.fullscreen.lock="orderLoading">
            <Header/>
            <el-container>
                <el-main>
                    <el-card class="box-card" style="width: 55%; margin-top: 10px;">
                        <template #header>
                            <div class="card-header" style="display: flex; justify-content: space-between;">
                                <span>Список транзакций с бонусами</span>
                            </div>
                        </template>
                        <BonusesTable :bonuses="bonuses"/>
                    </el-card>   
                </el-main>
            </el-container>
    </div>
</template>
<script>
import axios from 'axios';
import axiosAdmin from '../../axiosAdmin'
import Header from '../../components/admin/Header.vue'
import BonusesTable from '../../components/admin/BonusesTable.vue'
import router from '../../router'
import dayjs from 'dayjs';
import Paginate from "vuejs-paginate-next";

export default
{
    components: {Header, paginate: Paginate, BonusesTable},
    data() {
        return {
            bonuses: [],
            // transactionsPerPage: 2,
            // paginationOffset: 0,
        }
    },
    async mounted(){
        const promise = axiosAdmin
        .get('/bonuses/transactions')
        .then(response => {
            this.bonuses = response.data.bonuses.reverse()
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        this.bonuses.forEach(bonus => {
            bonus.date = dayjs(bonus.created_at).format('YYYY-MM-DD HH:mm:ss')
        })

    },
    methods: {
        // changePage(page_num){
        //     this.page = page_num
        //     this.paginationOffset = (this.transactionsPerPage * page_num) - this.transactionsPerPage
        //     if(page_num === 1){
        //         this.$router.push({name: 'BonusesTransactions'})
        //     }
        //     else{
        //         this.$router.push('?page='+page_num)
        //     }
        // },
    },
    computed: {
        // pagesCount(){
        //     return Math.ceil(this.bonuses.length / this.transactionsPerPage);
        // },
        // paginatedTransactions(){
        //     return this.bonuses.slice(this.paginationOffset, this.paginationOffset + this.transactionsPerPage)
        // },
    }
}
</script>