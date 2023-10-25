<template>
<el-header style="padding: 0;">
    <el-menu
        :default-active="activeIndex"
        class="el-menu-demo"
        mode="horizontal"
        :ellipsis="false"
        @select="handleSelect"
    >
        <el-menu-item index="0">Админ панель</el-menu-item>
        <div class="flex-grow" />
        <el-menu-item index="3" @click="logout">Выход</el-menu-item>
        <el-menu-item index="1" @click="$router.push({name: 'Tickets'})">Список билетов</el-menu-item>
        <el-menu-item index="2-1" @click="$router.push({name: 'Reports'})">Отчёты</el-menu-item>
        
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