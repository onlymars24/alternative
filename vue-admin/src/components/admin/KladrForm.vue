<template>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Название кладра</label>
        <el-input v-model="kladr.name" />
    </div>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Тип точки(socr)</label>
        <el-input v-model="kladr.socr" />
    </div>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Код 13-значный</label>
        <el-input v-model="kladr.code" v-mask="'#############'"/>
    </div>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Регион(строка)</label>
        <el-input v-model="kladr.region" />
    </div>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Город</label>
        <el-input v-model="kladr.city" />
    </div>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Район</label>
        <el-input v-model="kladr.district" />
    </div>
    <div style="width: 25%; margin-bottom: 10px;">
        <label for="">Актуальность</label>
        <el-input v-model="kladr.relevance" />
    </div>
    <!-- {{ kladr.code }} -->
    <div style="display: flex; justify-content: space-between; margin-top: 10px;">
        <el-button type="primary" :disabled="!kladr.name || (kladr.code != '' && kladr.code != null && kladr.code.length != 13)" :loading="false" @click="$emit('submitForm', kladr)">{{loading ? 'Загрузка' : 'Сохранить' }}</el-button>
        <el-button v-if="formType=='edit'" type="danger" style="margin-left: 15px;" @click="$emit('deleteKladr', kladr.id)">Удалить точку кладр</el-button>
    </div>
</template>

<script>
import axiosAdmin from '../../axiosAdmin'
export default
{
    props: ['kladr', 'formType'],
    emits: ['submitForm', 'deleteKladr'],
    data() {
        return {

        }
    },
    methods: {

    }
}
</script>