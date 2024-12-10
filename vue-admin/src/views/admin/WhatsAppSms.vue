<template>
    <div class="common-layout">
        <Header/>
        <Messages :messagesType="'whatsapp'" :statuses="statuses"/>
    </div>
</template>
<script>
import Header from '../../components/admin/Header.vue'
import Messages from '../../components/admin/Messages.vue'
import axiosAdmin from '../../axiosAdmin'
import dayjs from 'dayjs';

export default {
 components: {
     Header,
     Messages
 },
 data() {
     return {
        statuses: {
            0: {value: 0, label: 'Ошибка отправки'}, 
            1: {value: 1, label: 'В очереди'}, 
            2: {value: 2, label: 'Отправлено'}, 
            3: {value: 3, label: 'Получено'}, 
            4: {value: 4, label: 'Прочитано'}, 
            5: {value: 5, label: 'Аккаунт в бане'}
        },
     }
 },
 async mounted() {

 }
}
</script>
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