<template>
    <div>
        <el-card class="box-card" style="margin-top: 20px;">
            <h4>PDF файл для рекламы в квитанции билета или в квитанции о возврате билета</h4>
            <el-alert v-if="errorMessage" :title="errorMessage" type="error"/>
            <div style="display: flex; width: 60%; justify-content: space-between;">
                <div style="width: 18%;">
                    <span class="demo-input-label">Новый файл</span>
                    <input type="file" style="margin: 0;" @change="onFileChange">
                </div>
            </div>
            <div>
                <el-button :loading="loading" :disabled="!selectedFile" style="margin-top: 10px;" type="primary" @click="uploadPdf">Загрузить</el-button>
            </div>
            <div style="margin-top: 30px;" v-if="adPdf">
                <h4>Существующий файл</h4>
                <div>
                    <el-link :href="baseUrl+'/'+adPdf" type="primary" target="_blank">Проверить</el-link>
                </div>
                <div style="margin-top: 15px;">
                    <el-button class="ml-3" type="danger" @click="deletePdf" :loading="loading">Удалить существующий PDF файл</el-button>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
import axiosAdmin from '@/axiosAdmin';
import axios from 'axios';

export default{
    data(){ 
        return {
            selectedFile: null,
            adPdf: null,
            baseUrl: import.meta.env.VITE_API_BASE_URL,
            loading: false,
            errorMessage: null
        }
    },
    methods: {
        onFileChange(event) {
            this.selectedFile = event.target.files[0];
        },
        async uploadPdf(){
            this.errorMessage = null
            this.loading = true
            const formData = new FormData();
            formData.append('adPdf', this.selectedFile);
            await axiosAdmin
            .post('/ad/pdf/upload', formData)
            .then(response => {
                console.log(response)
                location.reload(); return false;
            })
            .catch(error => {
                console.log(error)
                if(error.response.status == 422){
                    this.errorMessage = error.response.data.errors.adPdf[0]
                }
                else{
                    this.errorMessage = 'Произошла непредвиденная ошибка'
                }
            })
            this.loading = false
        },
        async deletePdf(){
            if(!confirm('Вы уверены, что хотите удалить файл? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
				return
			}
            this.loading = true
            await axiosAdmin
            .post('/ad/pdf/delete')
            .then(response => {
                console.log(response)
                location.reload(); return false;
            })
            .catch(error => {
                console.log(error)
            })
            this.loading = false
        }
    },
    async mounted(){
        await axiosAdmin
        .get('/ad/pdf')
        .then(response => {
            this.adPdf = response.data.adPdf
        })
        .catch(error => {
            console.log(error)
        })
    }
}
</script>