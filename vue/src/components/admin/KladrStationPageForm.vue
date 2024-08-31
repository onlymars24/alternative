<template>
    <div v-if="formType=='edit'" class="" style="width: 25%; margin-bottom: 10px;">
        <label for="">Название страницы населённого пункта или автовокзала (редактируется вручную)</label>
        <el-input v-model="page.name" />
    </div>
    <div v-if="formType=='edit'" style="width: 25%; margin-bottom: 10px;">
        <label for="">Описание страницы (description) (редактируется вручную)</label><br>
        <el-input type="textarea" v-model="page.description"></el-input>
    </div>       
    <template v-if="formType=='edit'">
        <div class="" style="width: 25%; margin-bottom: 10px;">
            <label for="">URL код региона</label>
            <el-input v-model="page.new_url_region_code" disabled />
        </div>          
        <div class="" style="width: 25%; margin-bottom: 10px;">
            <label for="">URL {{page.kladr_id ? 'населённый пункт' : 'автовокзал'}}</label>
            <el-input v-model="page.url_settlement_name" disabled/>
        </div>
        <div>
            <el-link :href="pageUrl" target="_blank" type="primary">
                {{ pageUrl }}
            </el-link>
        </div>
    </template>    
    <div v-if="(formType=='edit' && page.kladr_id) || formType=='create'" style="margin-bottom: 10px;">
        <label for="">Населённый пункт</label><br>
        <el-select style="width: 25%;" v-model="page.kladr_id" filterable :disabled="formType=='edit'">
            <template v-for="el in kladrs" :key="el.id">
                <el-option
                    v-if="(!el.kladr_station_page && formType=='create') || formType=='edit'"
                    :label="el.name"
                    :value="el.id">
                    <strong class="big__font-size">{{el.name}}{{el.region || el.district ? ', ' : '' }}</strong>
                    <span style="font-size: 16px;">{{el.region}}{{(el.district && !el.region) || !el.district? '' : ', ' }}{{el.district}}</span>
                </el-option>
            </template>
        </el-select>
    </div>
    <div v-if="(formType=='edit' && page.station_id) || formType=='create'" style="margin-bottom: 10px;">
        <label for="">Автовокзал</label><br>
        <el-select style="width: 25%;" v-model="page.station_id" filterable  :disabled="formType=='edit'">
            <template v-for="el in stations" :key="el.id">
                <el-option
                    v-if="(!el.kladr_station_page && formType=='create') || formType=='edit'"
                    :label="el.name"
                    :value="el.id">
                </el-option>
            </template>
        </el-select>
    </div>
    <div v-if="formType=='edit' && page.station_id" style="margin-bottom: 20px;">
        <label for="">Контакты (редактируется вручную)</label>
        <HtmlEditor
        :value="page.contacts"
        @updateEditorText="updateEditorTextContacts"
        />
    </div>    
    <div v-if="formType=='edit'">
        <label for="">Контент страницы автовокзала или автостанции (редактируется вручную)</label>
        <HtmlEditor
        :value="page.content"
        @updateEditorText="updateEditorTextContent"
        />
    </div>
    <div class="">
        <el-checkbox v-model="page.booleanHidden">Скрыть страницу от пользователя</el-checkbox>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 10px;">
        <el-button type="primary" @click="$emit('submitFrom', page)">Сохранить</el-button>
        <el-button v-if="formType=='edit'" type="danger" style="margin-left: 15px;" @click="$emit('deletePage', page.id)">Удалить страницу</el-button>
    </div>
</template>
<script>
import HtmlEditor from '../../components/admin/HtmlEditor.vue'

export default
{
    components: {HtmlEditor},
    props: ['kladrs', 'stations', 'formType', 'page', 'paramKey'],
    emits: ['submitForm', 'deletePage'],
    data() {
        return {
            url: window.location.origin
        }
    },
    async mounted(){
    },
    methods: {
        updateEditorTextContent(newContent){
            this.page.content = newContent
            // this.paramKey++
        },
        updateEditorTextContacts(newContent){
            this.page.contacts = newContent
            // this.paramKey++
        },
    },
    watch: {
        'page.station_id': function(newValue, oldValue) {
            if(this.page.station_id){
                this.page.kladr_id = '';
                // this.paramKey++
            }
        },
        'page.kladr_id': function(newValue, oldValue) {
            if(this.page.kladr_id){
                this.page.station_id = '';
                // this.paramKey++
            }
        },
    },
    computed: {
        pageUrl(){
            return this.url+'/'+( this.page.kladr_id ? 'расписание' : 'автовокзал' )+'/'+this.page.url_region_code+'/'+this.page.url_settlement_name
        }
    }
}
</script>
<style>
.table-cell-word-wrap {
    white-space: normal;
    word-wrap: break-word;
}
</style>