<template>
<el-header style="padding: 0;">
    <el-menu
        :default-active="activeIndex"
        class="el-menu-demo"
        mode="horizontal"
        :ellipsis="false"
        @select="handleSelect"
    >
        <el-menu-item index="0" @click="$router.push({name: 'Tickets'})">Админ панель</el-menu-item>
        <div class="flex-grow" />
        <el-menu-item index="1" @click="logout">Выход</el-menu-item>
        <el-menu-item index="2" @click="$router.push({name: 'Tickets'})">Список билетов</el-menu-item>
        <el-menu-item index="3" @click="$router.push({name: 'Debugging'})">Отладка</el-menu-item>
        <el-sub-menu index="4">
            <template #title>Редактировать контент</template>
            <el-menu-item index="4-1" @click="$router.push({name: 'EditMain'})">Главная страница</el-menu-item>
            <el-menu-item index="4-2" @click="$router.push({name: 'EditUpcomingTrips'})">Список заказов</el-menu-item>
            <el-menu-item index="4-3" @click="$router.push({name: 'BusRoutes'})">Страница с маршрутом</el-menu-item>
            <el-menu-item index="4-4" @click="$router.push({name: 'EditRobotsTxt'})">robots.txt</el-menu-item>
        </el-sub-menu>
        <el-menu-item index="5" @click="$router.push({name: 'EditPoints'})">Настройка точек</el-menu-item>
        <el-menu-item index="6" @click="$router.push({name: 'BusStations'})">Автовокзалы</el-menu-item>
        <el-sub-menu index="7">
            <template #title>Новости</template>
            <el-menu-item index="7-1" @click="$router.push({name: 'EventCreate'})">Добавить новость</el-menu-item>
            <el-menu-item index="7-2" @click="$router.push({name: 'EventEdit'})">Редактировать новости</el-menu-item>
        </el-sub-menu>
        <!-- <el-sub-menu index="8">
            <template #title>Sitemap</template>
            <el-menu-item index="8-1" @click="sitemapView()">Предпросмотр</el-menu-item>
            <el-menu-item index="8-2" @click="sitemapExport()">Экспорт</el-menu-item>
        </el-sub-menu> -->
        <el-menu-item index="9" @click="$router.push({name: 'Feedback'})">Обратная связь</el-menu-item>
        <el-menu-item index="10" @click="$router.push({name: 'Reports'})">Отчёты</el-menu-item>
        <el-sub-menu index="11">
            <template #title>Учет сообщений</template>
            <el-menu-item index="11-1" @click="$router.push({name: 'Sms'})">Smsaero</el-menu-item>
            <el-menu-item index="11-2" @click="$router.push({name: 'WhatsAppSms'})">WhatsApp</el-menu-item>
        </el-sub-menu>
        
        <el-sub-menu index="12">
            <template #title>Бонусы</template>
            <el-menu-item index="12-1" @click="$router.push({name: 'BonusesUsers'})">Пользователи</el-menu-item>
            <el-menu-item index="12-2" @click="$router.push({name: 'BonusesTransactions'})">Транзакции</el-menu-item>
        </el-sub-menu>
        <el-menu-item index="13" @click="$router.push({name: 'Settings'})">Базовые настройки</el-menu-item>
    </el-menu>
</el-header>



</template>
<script>
import { ref } from "vue";
import router from '../../router'

export default
{
    data() {
        return {

        }
    },
    methods: {
        logout(){
            localStorage.removeItem('authAdminToken')
            router.push({ name: 'ALogin'})
        },
        sitemapExport(){
            window.open(import.meta.env.VITE_API_BASE_URL+'/download/sitemap')
        },
        sitemapView(){
            window.open(import.meta.env.VITE_API_BASE_URL+'/'+import.meta.env.VITE_API_SITEMAP)
        }
    }
}
</script>
<style>
@import url("//unpkg.com/element-ui@2.15.13/lib/theme-chalk/index.css");
.flex-grow {
  flex-grow: 1;
}
</style>