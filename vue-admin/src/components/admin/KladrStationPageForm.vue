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
        <el-select style="width: 33%;" v-model="page.kladr_id" filterable :disabled="formType=='edit'">
            <template v-for="el in kladrs" :key="el.id">
                <el-option
                    v-if="(!el.kladr_station_page && formType=='create') || formType=='edit'"
                    :label="el.name+(el.region ? ', '+el.region : '')+(el.district ? ', '+el.district : '')+(el.code ? ', '+el.code : '')"
                    :value="el.id">
                    <span style="font-size: 16px;">{{ el.name+(el.region ? ', '+el.region : '')+(el.district ? ', '+el.district : '')+(el.code ? ', '+el.code : '') }}</span>
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
    <div v-if="formType=='edit'" style="margin-bottom: 20px;">
        <label for="">Контент страницы автовокзала или автостанции (редактируется вручную)</label>
        <HtmlEditor
        :value="page.content"
        @updateEditorText="updateEditorTextContent"
        />
    </div>
    <div v-if="formType=='edit'" style="margin-bottom: 20px;">
        <label for="">Вёрстка КАРТЫ страницы {{ page.station_id ? 'автовокзала' : 'населённого пункта' }}</label>
        <el-input
        type="textarea"
        :rows="2"
        v-model="page.map">
        </el-input>
    </div>
    <div class="">
        <el-checkbox v-model="page.booleanHidden">Скрыть страницу от пользователя</el-checkbox>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 10px;">
        <el-button type="primary" @click="$emit('submitFrom', page)">Сохранить</el-button>
        <el-button v-if="formType=='edit'" type="danger" style="margin-left: 15px;" @click="$emit('deletePage', page.id)">Удалить страницу</el-button>
    </div>
    <template v-if="formType=='edit'">
        <hr>
        <div style="margin-bottom: 50px;">
            <label style="margin-bottom: 10px;" for=""><strong>Изображение для шапки страницы:</strong></label><br>
            <div style="width: 30%; display: flex; margin-bottom: 20px;">
                <input type="file" class="form-control" style="margin: 0;" @change="onFileChange">
                <button :disabled="!selectedFile" class="btn btn-primary" type="button" style="margin-left: 10px;" @click="uploadImage(page.id)">Загрузить</button>
            </div>
            <div v-if="page.header_img">
                <div><img style="width: 50%;" :src="baseUrl+'/'+page.header_img" alt=""></div>
                <div style="margin-top: 15px;"><el-button class="ml-3" type="danger" @click="deleteImage(page.id)">Удалить существующее изображение</el-button></div>
            </div>
        </div>
        <div>
            <label style="margin-bottom: 10px;" for=""><strong>Изображение для ссылки на страницу:</strong></label><br>
            <div style="width: 30%; display: flex; margin-bottom: 20px;">
                <input type="file" class="form-control" style="margin: 0;" @change="onMetaFileChange">
                <button :disabled="!selectedMetaFile" class="btn btn-primary" type="button" style="margin-left: 10px;" @click="uploadMetaImage(page.id)">Загрузить</button>
            </div>
            <div v-if="page.metaImg">
                <div><img style="width: 50%;" :src="baseUrl+'/'+page.metaImg" alt=""></div>
                <div style="margin-top: 15px;"><el-button class="ml-3" type="danger" @click="deleteMetaImage(page.id)">Удалить существующее изображение</el-button></div>
            </div>
        </div>
    </template>
</template>
<script>
import HtmlEditor from '../../components/admin/HtmlEditor.vue'
import axiosAdmin from '../../axiosAdmin'

export default
{
    components: {HtmlEditor},
    props: ['kladrs', 'stations', 'formType', 'page', 'paramKey'],
    emits: ['submitForm', 'deletePage'],
    data() {
        return {
            url: window.location.origin,
            selectedFile: null,
            selectedMetaFile: null,
            baseUrl: import.meta.env.VITE_API_BASE_URL,
            loadingImage: false
        }
    },
    async mounted(){
    },
    methods: {
        onFileChange(event) {
            this.selectedFile = event.target.files[0];
        },
        onMetaFileChange(event) {
            this.selectedMetaFile = event.target.files[0];
        },
        async uploadImage(pageId) {
            this.loadingImage = true
            const formData = new FormData();
            formData.append('image', this.selectedFile);
            formData.append('pageId', pageId);
            console.log(this.selectedFile)
            await axiosAdmin
            // .post('/kladr/station/page/image/upload', {pageId: , image: this.selectedFile})
            .post('/kladr/station/page/image/upload', formData)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            this.loadingImage = false
            location.reload(); return false
        },
        async deleteImage(pageId){
            if(!confirm('Вы уверены, что хотите удалить изображение? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.loadingImage = true
            await axiosAdmin
            // .post('/kladr/station/page/image/upload', {pageId: , image: this.selectedFile})
            .post('/kladr/station/page/image/delete', {pageId})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            this.loadingImage = false
            location.reload(); return false
        },

        async uploadMetaImage(pageId) {
            this.loadingImage = true
            const formData = new FormData();
            formData.append('metaImg', this.selectedMetaFile);
            formData.append('pageId', pageId);
            console.log(this.selectedFile)
            await axiosAdmin
            // .post('/kladr/station/page/image/upload', {pageId: , image: this.selectedFile})
            .post('/kladr/station/page/meta/image/upload', formData)
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            this.loadingImage = false
            location.reload(); return false
        },
        async deleteMetaImage(pageId){
            if(!confirm('Вы уверены, что хотите удалить изображение? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.loadingImage = true
            await axiosAdmin
            // .post('/kladr/station/page/image/upload', {pageId: , image: this.selectedFile})
            .post('/kladr/station/page/meta/image/delete', {pageId})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            this.loadingImage = false
            location.reload(); return false
        },

        updateEditorTextContent(newContent){
            this.page.content = newContent
            // this.paramKey++
        },
        updateEditorTextContacts(newContent){
            this.page.contacts = newContent
            // this.paramKey++
        },
        updateEditorTextMap(newContent){
            this.page.map = newContent
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