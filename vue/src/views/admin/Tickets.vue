<template>
    <div class="common-layout" v-loading.fullscreen.lock="ticketsLoading">
            <Header/>
            <el-container>
                <el-aside width="200px" style="padding-top: 20px;">
                    <el-card class="box-card">
                            <template #header>
                            <div class="card-header" style="display: flex; justify-content: space-between;">
                                <span>Фильтрация</span>
                            </div>
                            </template>
                            <FilterInput :title="'Фамилия'" :ind="'lastName'" :filterEl="filterArr['lastName']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            <FilterInput :title="'Имя'" :ind="'firstName'" :filterEl="filterArr['firstName']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            <FilterInput :title="'Отчество'" :ind="'middleName'" :filterEl="filterArr['middleName']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            <FilterInput :title="'Пункт отправления'" :ind="'dispatchStation'" :filterEl="filterArr['dispatchStation']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            <FilterInput :title="'Пункт прибытия'" :ind="'arrivalStation'" :filterEl="filterArr['arrivalStation']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            
                            <FilterInput :title="'ID заказа'" :ind="'order_id'" :filterEl="filterArr['order_id']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            <FilterInput :title="'Серия билета'" :ind="'ticketSeries'" :filterEl="filterArr['ticketSeries']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            <FilterInput :title="'Номер билета'" :ind="'ticketNum'" :filterEl="filterArr['ticketNum']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                            <!-- <FilterInput :title="'Серия документа'" :ind="'docSeries'" :filterEl="filterArr['docSeries']" @setFilter="setFilter" @deleteFilter="deleteFilter"/> -->
                            <!-- <FilterInput :title="'Номер документа'" :ind="'docNum'" :filterEl="filterArr['docNum']" @setFilter="setFilter" @deleteFilter="deleteFilter"/> -->
                            <FilterSelect :title="'Тип билета'" :ind="'ticketType'" :selectData="ticketTypes" :filterEl="filterArr['ticketType']" @setSelectFilter="setSelectFilter" @deleteSelectFilter="deleteSelectFilter"/>
                            <FilterSelect :title="'Статус билета'" :ind="'status'" :selectData="ticketStatuses" :filterEl="filterArr['status']" @setSelectFilter="setSelectFilter" @deleteSelectFilter="deleteSelectFilter"/>
                            <div class="text item" style="margin-bottom: 10px;">
                                <div>Телефон аккаунта:</div>   
                                <div v-show="!filterArr['phone'].set">
                                    <el-input
                                        v-mask="'+7 (###) ### ####'"
                                        ref="refPhoneInput"
                                        v-model="filterArr['phone'].value"
                                        style="width: 120px; margin-right: 6px;"
                                        size="small"
                                        class="phone__input-filter"
                                    />
                                    <el-button type="primary" @click="setFilter('phone', filterArr['phone'].value)" :disabled="!filterArr['phone'].value || filterArr['phone'].value == '+7' || filterArr['phone'].value == '+7 ('" circle><i class="el-icon-check"></i></el-button>                                    
                                </div>   
                                <div v-show="filterArr['phone'].set">
                                    <el-tag style="max-width: 100%;" size="large" closable @close="deleteFilter('phone')">{{ filterArr['phone'].value }}</el-tag>
                                </div>
                            </div>
                            <div class="text item" style="margin-bottom: 10px;">
                                <div>Дата рождения:</div> 
                                <template v-if="!filterArr.birthday.set">
                                    <CalendarFilter @setDateFilter="setDateFilter" :filterName="'birthday'"/>                                
                                </template>
                                <template v-else>
                                    <el-tag size="large" closable @close="deleteFilter('birthday')">{{filterArr.birthday.value}}</el-tag>
                                </template>
                            </div>
                            <div class="text item" style="margin-bottom: 10px;">
                                <div>Дата отправления:</div> 
                                <template v-if="!filterArr.dispatchDate.set">
                                    <CalendarFilter @setDateFilter="setDateFilter" :filterName="'dispatchDate'"/>                                
                                </template>
                                <template v-else>
                                    <el-tag size="large" closable @close="deleteFilter('dispatchDate')">{{filterArr.dispatchDate.value}}</el-tag>
                                </template>
                            </div>
                            <div class="text item" style="margin-bottom: 10px;">
                                <div>Дата бронирования(мск):</div> 
                                <template v-if="!filterArr.created_at.set">
                                    <CalendarFilter @setDateFilter="setDateFilter" :filterName="'created_at'"/>                                
                                </template>
                                <template v-else>
                                    <el-tag size="large" closable @close="deleteFilter('created_at')">{{filterArr.created_at.value}}</el-tag>
                                </template>
                            </div>
                            <div class="text item">
                                <div>Цена:</div> 
                                <el-slider @change="setPriceFilter" v-model="filterArr.price.value" range :max="10000"></el-slider>
                            </div>
                            
                        </el-card>
                </el-aside>
                <el-main>
                    <div>
                        <el-space :fill="false" wrap :size="17">
                            <template v-if="!ticketsLoading">
                                <TicketCard v-for=" ticket in tickets.data" :ticket="ticket" :ticketStatuses="ticketStatuses" :isOrderPage="false"/>
                            </template>
                            <!-- <pre>{{ tickets }}</pre> -->
                        </el-space>
                        <Bootstrap5Pagination
                            :data="tickets"
                            @pagination-change-page="getTickets"
                            :limit="1"
                        />    
                        <!-- <paginate
                            v-if="!ticketsLoading && filteredTickets.length > ticketsPerPage"
                            :page-count="pagesCount"
                            :click-handler="changePage"
                            :prev-text="'<<'"
                            :next-text="'>>'"
                            :container-cass="'pagination'"
                            :page-class="'page-item'"
                            v-model="page"
                        >
                        </paginate> -->

                    </div>
                </el-main>
            </el-container>
    </div>
</template>

<script>
import { ref } from "vue";
import CalendarFilter from '../../components/admin/CalendarFilter.vue'
import dayjs from 'dayjs'
import router from '../../router'
import Header from '../../components/admin/Header.vue'
import TicketCard from '../../components/admin/TicketCard.vue'
import FilterInput from '../../components/admin/FilterInput.vue'
import FilterSelect from '../../components/admin/FilterSelect.vue'
import axiosClient from "../../axios";
import axiosAdmin from "../../axiosAdmin";
import ticketStatuses from '../../data/TicketStatuses';
import TheMask from 'vue-the-mask'
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
export default
{
    components: {CalendarFilter, Header, TicketCard, FilterInput, FilterSelect, Bootstrap5Pagination},
    data() {
        return {
            drawer: false,
            isCollapse: true,
            page: 1,
            ticketsPerPage: 8,
            paginationOffset: 0,
            filterArr: {
                firstName: {
                    set: false,
                    value: ''
                },
                lastName: {
                    set: false,
                    value: ''
                },
                middleName: {
                    set: false,
                    value: ''
                },
                birthday: {
                    set: false,
                    value: ''
                },
                price: {
                    set: false,
                    value: [0, 10000]
                },
                dispatchStation: {
                    set: false,
                    value: ''
                },
                arrivalStation: {
                    set: false,
                    value: ''
                },
                dispatchDate: {
                    set: false,
                    value: ''
                },
                created_at: {
                    set: false,
                    value: ''
                },
                ticketSeries: {
                    set: false,
                    value: ''
                },
                ticketNum: {
                    set: false,
                    value: ''
                },
                docSeries: {
                    set: false,
                    value: ''
                },
                docNum: {
                    set: false,
                    value: ''
                },
                phone: {
                    set: false,
                    value: ''
                },
                dispatchDate: {
                    set: false,
                    value: ''
                },
                ticketType: {
                    set: false,
                    value: '',
                    label: ''
                },
                order_id: {
                    set: false,
                    value: ''
                },
                status: {
                    set: false,
                    value: '',
                    label: ''
                },
            },
            ticketTypes: {
                Полный: {
                    value: 'Полный',
                    label: 'Полный'
                },
                Детский: {
                    value: 'Детский',
                    label: 'Детский',
                },
                Багажный: {
                    value: 'Багажный',
                    label: 'Багажный'
                }
            },
            ticketStatuses: ticketStatuses,
            tickets: [],
            ticketsLoading: false,
        }
    },
    methods: {
        setFilter(filterName, value){
            this.filterArr[filterName].set = true
            this.filterArr[filterName].value = value
            this.getTickets(1)
        },
        setPriceFilter(){
            this.filterArr['price'].set = true
            this.getTickets(1)
        },
        deleteFilter(filterName){
            this.filterArr[filterName].set = false
            this.filterArr[filterName].value = ''
            this.getTickets(1)
        },
        setSelectFilter(filterName, value, selectData){
            this.filterArr[filterName].set = true
            this.filterArr[filterName].value = value
            this.filterArr[filterName].label = selectData[value].label
            this.getTickets(1)
        },
        deleteSelectFilter(filterName){
            this.filterArr[filterName].set = false
            this.filterArr[filterName].value = ''
            this.filterArr[filterName].label = ''
            this.getTickets(1)
        },
        setDateFilter(filterName, value){
            this.filterArr[filterName].value = dayjs(value).format('YYYY-MM-DD')
            this.filterArr[filterName].set = true
            this.getTickets(1)
        },
        async getTickets(page = 1){
            this.ticketsLoading = true
            const promise = axiosAdmin
            .post('/tickets/paginate', {page: page, filterArr: this.filterArr})
            .then(response => {
                console.log(response.data)
                this.tickets = response.data.tickets
            })
            .catch( error => {
                console.log(error)
            })
            await promise
            this.tickets.data.forEach(elem => {
                elem.birthday = dayjs(elem.birthday).format('YYYY-MM-DD')
                elem.created_at = dayjs(elem.created_at).format('YYYY-MM-DD HH:mm')
                elem.insurance = elem.insurance ? JSON.parse(elem.insurance) : null
            })
            this.ticketsLoading = false
        }
    },
    computed: {
        ticketStatuses(){
            return ticketStatuses;
        }
    },
    watch: {
        filterArr: {
            handler: function(){
                console.log(this.filterArr)
            },
            deep: true
        }
    },
    async mounted(){
        this.$refs.refPhoneInput.focus()
        this.getTickets()
    }
}
</script>
<style>
 /* @import "https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"; */
 .pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    justify-content: center;
}

.page-link {
    position: relative;
    display: block;
    color: #0d6efd;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #dee2e6;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    padding: 0.375rem 0.75rem;
    cursor: pointer;
}
.page-item:first-child .page-link {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

</style>