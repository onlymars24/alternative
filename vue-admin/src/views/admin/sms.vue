<template>
       <div class="common-layout">
        <Header/>
        <Messages :messagesType="'sms'" :statuses="statuses"/>
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
                0: {value: 0, label: 'В очереди'}, 
                1: {value: 1, label: 'Доставлено'}, 
                2: {value: 2, label: 'Не доставлено'}, 
                3: {value: 3, label: 'Передано'}, 
                8: {value: 8, label: 'На модерации'}, 
                6: {value: 6, label: 'Сообщение отклонено'}, 
                4: {value: 4, label: 'Ожидание статуса сообщения'}, 
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