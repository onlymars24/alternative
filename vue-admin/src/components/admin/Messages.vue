<script>
import Header from '../../components/admin/Header.vue'
import FilterInput from '../../components/admin/FilterInput.vue'
import FilterSelect from '../../components/admin/FilterSelect.vue'
import axiosAdmin from '../../axiosAdmin'
import dayjs from 'dayjs';
import ru from 'element-plus/dist/locale/ru.mjs'

export default {
 components: {
     Header,
     FilterInput,
     FilterSelect
 },
 props: ['messagesType', 'statuses'],
 data() {
     return {
        // 0-Ошибка отправки (в т.ч. бан/мертвые proxy/etc)
        // 1-В очереди
        // 2-Отправлено
        // 3-Получено
        // 4-Прочитано
        // 10-Аккаунт в бане
        locale: ru,
        filterArr: {
            phone: {
                set: false,
                value: ''
            },
            cost: {
                set: false,
                value: ''
            },
            status: {
                set: false,
                value: ''
            },
            cost: {
                set: false,
                value: ''
            },
            type: {
                set: false,
                value: ''
            },
        },
        datePickerDefaultValue: null,
        datePickerValue: null,
        messagesLoading: false,
        messages: [],
        currency: 'RUB',
        types: {
            'paymentReminder': {value: 'paymentReminder', label: 'Напоминание об оплате'}, 
            'Подтверждение заказа': {value: 'Подтверждение заказа', label: 'Подтверждение заказа'},
            'auth': {value: 'auth', label: 'Аутентификация'},
            'Сообщение о поездке': {value: 'Сообщение о поездке', label: 'Сообщение о поездке'}, 
            'reset': {value: 'reset', label: 'Сброс пароля'},
        },
     }
 },
 async mounted() {
    let date = new Date();
    this.datePickerDefaultValue = this.datePickerValue = [
        new Date(date.getFullYear(), date.getMonth(), 1, 0, 0, 0),
        new Date(date.getFullYear(), date.getMonth(), date.getDate(), 23, 59, 59),
    ]
 },
 watch: {
    datePickerValue(datePickerValue){
        if(!datePickerValue){
            this.datePickerValue = this.datePickerDefaultValue
        }
        this.getMessages()
    }
 },
 computed: {
    period(){
        return [
            dayjs(this.datePickerValue[0]).format('YYYY-MM-DD HH:mm:ss'),
            dayjs(this.datePickerValue[1]).format('YYYY-MM-DD HH:mm:ss')
        ]
    },
    downloadExcel(){
        // return 
    }
 },
 methods: {
    async exportDataToCsv(){
        try {
            const response = await axiosAdmin.post('/export/excel/messages',
                {
                    messagesType: this.messagesType, 
                    filterArr: this.filterArr, 
                    period: this.period,
                    types: this.types,
                    statuses: this.statuses
                }, 
                {
                    responseType: 'blob', // Указываем, что ожидаем бинарный файл
                }
            );
            console.log(response)
            // Создаем ссылку для скачивания файла
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Сообщения_'+this.messagesType+'.xlsx'); // Указываем имя файла
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (error) {
            console.error('Ошибка при экспорте:', error);
        }
        // window.open(this.downloadExcel, '_blank');
    },
    setFilter(filterName, value){
        this.filterArr[filterName].set = true
        this.filterArr[filterName].value = value
        this.getMessages()
    },
    deleteFilter(filterName){
        this.filterArr[filterName].set = false
        this.filterArr[filterName].value = ''
        this.getMessages()
    },
    setSelectFilter(filterName, value, selectData){
        console.log(filterName, value, selectData)
        console.log(this.filterArr[filterName])
        this.filterArr[filterName].set = true
        this.filterArr[filterName].value = value
        this.filterArr[filterName].label = selectData[value].label
        this.getMessages()
    },
    deleteSelectFilter(filterName){
        this.filterArr[filterName].set = false
        this.filterArr[filterName].value = ''
        this.filterArr[filterName].label = ''
        this.getMessages()
    },
    async getMessages(){
        this.messagesLoading = true
        await axiosAdmin
        .post('/messages', {messagesType: this.messagesType, filterArr: this.filterArr, period: this.period})
        .then(response => {
            this.messages = response.data.messages
            
            console.log(response)
        })
        .catch(error => {
            console.log(error)
        })
        this.messages.forEach(message => {
            message.date = dayjs(message.created_at).format('YYYY-MM-DD HH:mm:ss')
        })
        this.messagesLoading = false
    }
 }
}
</script>

<template>
     <div v-loading.fullscreen.lock="messagesLoading">
         <el-container v-if="!messagesLoading">
             <el-main>
                 <div style="margin-top: 20px;">
                     <el-card class="box-card">
                         <template #header>
                             <div class="card-header" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                 <span style="font-size: 30px;">Список сообщений {{messagesType}}</span>
                             </div>
                             <div style="display: flex; flex-wrap: wrap; gap: 30px;">
                                <div class="text item">
                                    <span>Период отправки</span><br>
                                    <el-config-provider :locale="locale">
                                    <el-date-picker
                                        v-model="datePickerValue"
                                        type="datetimerange"
                                        start-placeholder="От"
                                        end-placeholder="До"
                                        :default-time="datePickerDefaultValue"
                                        :clearable="true"
                                    />
                                    </el-config-provider>
                                </div>                                
                                <div class="text item" style="margin-bottom: 10px;">
                                    <div>Телефон получателя:</div>   
                                    <div v-show="!filterArr['phone'].set">
                                        <el-input
                                            v-mask="'+7 (###) ### ####'"
                                            ref="refPhoneInput"
                                            v-model="filterArr['phone'].value"
                                            style="width: 120px; margin-right: 6px;"
                                            size="small"
                                            class="phone__input-filter"
                                        />
                                        <el-button type="primary" @click="setFilter('phone', filterArr['phone'].value)" :disabled="filterArr['phone'].value.length != 17" circle><i class="el-icon-check"></i></el-button>
                                    </div>   
                                    <div v-show="filterArr['phone'].set">
                                        <el-tag style="max-width: 100%;" size="large" closable @close="deleteFilter('phone')">{{ filterArr['phone'].value }}</el-tag>
                                    </div>
                                </div>
                                <FilterInput v-if="messagesType == 'sms'" :title="'Стоимость сообщения'" :ind="'cost'" :filterEl="filterArr['cost']" @setFilter="setFilter" @deleteFilter="deleteFilter"/>
                                <FilterSelect :title="'Статус сообщения'" :ind="'status'" :selectData="statuses" :filterEl="filterArr['status']" @setSelectFilter="setSelectFilter" @deleteSelectFilter="deleteSelectFilter"/>
                                <FilterSelect v-if="messagesType == 'whatsapp'" :title="'Тип сообщения'" :ind="'type'" :selectData="types" :filterEl="filterArr['type']" @setSelectFilter="setSelectFilter" @deleteSelectFilter="deleteSelectFilter"/>
                            </div>
                            <el-button @click="exportDataToCsv" type="primary">Скачать Excel</el-button>
                         </template>
                         <!-- <el-table :data="smsArray" style="width: 100%"> -->
                         <el-table style="width: 100%" :data="messages">
                            <el-table-column prop="id" label="ID" width="135" /> 
                            <el-table-column prop="date" label="Дата и время отправления" width="250" />
                            <el-table-column prop="phone" label="Телефон" width="200" />
                            <el-table-column v-if="messagesType == 'sms'" prop="cost" label="Стоимость сообщения" width="200" />
                            <el-table-column v-if="messagesType == 'sms'" prop="balance" label="Баланс после отправки" width="200" />
                            <el-table-column
                                width="170">
                                <template #header>
                                    <span>Статус</span>
                                </template>
                                <template #default="scope">
                                    {{ statuses[scope.row.status].label }}
                                </template>
                            </el-table-column>
                            <el-table-column 
                                v-if="messagesType == 'whatsapp'"
                                width="190">
                                <template #header>
                                    <span>Тип</span>
                                </template>
                                <template #default="scope">
                                    {{ types[scope.row.type].label }}
                                </template>
                            </el-table-column>
                         </el-table>
                     </el-card>
                 </div>
                 <div style="margin-top: 20px;">
                     <el-space :fill="false" wrap :size="17"></el-space>
                 </div>
             </el-main>
         </el-container>
     </div>
</template>

<style type="text/css">
.table {
width: 100%;
margin-bottom: 20px;
border-collapse: collapse;
}
.table th {
font-weight: bold;
padding: 5px;
background: #efefef;
border: 1px solid #dddddd;
}
.table td {
border: 1px solid #dddddd;
padding: 5px;
}
.table tr td:first-child, .table tr th:first-child {
border-left: none;
background: #efefef;
font-weight: bold;
}
.table tr td:last-child, .table tr th:last-child {
border-right: none;
}

</style>