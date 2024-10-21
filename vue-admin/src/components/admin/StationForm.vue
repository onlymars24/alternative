<template>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Название автовокзала</label>
        <el-input v-model="station.name" />
    </div>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Адрес автовокзала на яндекс карте (редактируется вручную)</label>
        <el-input v-model="station.address" />
    </div>

    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Широта на яндекс карте (редактируется вручную)</label>
        <el-input v-model="station.latitude" />
    </div>

    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Долгота на яндекс карте (редактируется вручную)</label>
        <el-input v-model="station.longitude" />
    </div>
    <div style="margin-bottom: 10px;">
        <label for="">Населённый пункт (из кладра)</label><br>
        <el-select style="width: 25%;" v-model="station.kladr_id" filterable :disabled="formType=='edit'">
            <template v-for="el in kladrsConnected" :key="el.id">
                <el-option
                    :label="el.name"
                    :value="el.id">
                    <strong class="big__font-size">{{el.name}}{{el.region || el.district ? ', ' : '' }}</strong>
                    <span style="font-size: 16px;">{{el.region}}{{(el.district && !el.region) || !el.district? '' : ', ' }}{{el.district}}</span>
                </el-option>
            </template>
        </el-select>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 10px;">
        <el-button type="primary" :disabled="!station.kladr_id || !station.name" :loading="loading" @click="$emit('submitFrom', station)">{{loading ? 'Загрузка' : 'Сохранить' }}</el-button>
        <el-button v-if="formType=='edit'" type="danger" style="margin-left: 15px;" @click="$emit('deleteStation', station.id)">Удалить страницу</el-button>
    </div>
</template>

<script>
import axiosAdmin from '../../axiosAdmin'
import router from '../../router'
// import StationForm from '../../components/admin/StationForm.vue'
// import Header from '../../components/admin/Header.vue'

export default
{
    components: {},
    props: ['station', 'kladrsConnected', 'formType', 'loading'],
    emits: ['submitFrom', 'deleteStation'],
    data() {
        return {

        }
    },
    methods: {

    }

}
</script>