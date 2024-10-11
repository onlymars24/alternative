<template>
<!-- <el-input size="small" ref="refPhoneInput" v-mask="'+7 (###) ### ####'" /> -->
<el-input size="small" ref="refPhoneInput" v-mask="'+7 (###) ### ####'" v-model="phone" />
<el-table :data="paginatedTransactions">
    <el-table-column label="Телефон получателя" width="200" >
        <template #default="scope">
            <el-link type="primary" :href="'/fj239f3j984jsdiaisja/bonuses/user/'+scope.row.user_id" target="_blank">{{ scope.row.user_phone }}</el-link>
        </template>
    </el-table-column>
    <el-table-column prop="date" label="Дата" width="200" />
    <el-table-column prop="transaction" label="Транзакция" width="180">
        <template #default="scope">
            <el-text class="mx-1" type="success" v-if="scope.row.transaction == 'plus'">+{{ scope.row.amount }}</el-text>
            <el-text class="mx-1" type="danger" v-if="scope.row.transaction == 'minus'">-{{ scope.row.amount }}</el-text>
        </template>
    </el-table-column>
    <el-table-column prop="descr" label="Описание" width="400" />
    
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
</template>
<script>
import axios from 'axios';
import axiosClient from '../../axios';
import Header from '../../components/admin/Header.vue'
import router from '../../router'
// import PopupWindow from '../../components/PopupWindow.vue';
import dayjs from 'dayjs';
import Paginate from "vuejs-paginate-next";
import TheMask from 'vue-the-mask'

export default
{
    components: {Header, paginate: Paginate},
    props: ['bonuses'],
    data() {
        return {
            phone: '',
            transactionsPerPage: 20,
            paginationOffset: 0,
        }
    },
    async mounted(){
        this.$refs.refPhoneInput.focus()
    },
    methods: {
        changePage(page_num){
            this.page = page_num
            this.paginationOffset = (this.transactionsPerPage * page_num) - this.transactionsPerPage
            if(page_num === 1){
                this.$router.push({name: 'BonusesTransactions'})
            }
            else{
                this.$router.push('?page='+page_num)
            }
        },
    },
    computed: {
        pagesCount(){
            return Math.ceil(this.filteredBonuses.length / this.transactionsPerPage);
        },
        paginatedTransactions(){
            return this.filteredBonuses.slice(this.paginationOffset, this.paginationOffset + this.transactionsPerPage)
        },
        filteredBonuses(){
            return this.bonuses.filter(
                (bonuse) =>
                !this.phone ||
                bonuse.user_phone.toLowerCase().includes(this.phone.toLowerCase())
            )
        }
    }
}
</script>