<template>
  <el-card style="margin-top: 20px;">
    <template #header>
      <div class="card-header">
        <span>Проверка новых точек в e-traffic</span>
      </div>
    </template>
    <div>
        <el-button @click="opened = true; getNewPoints()" type="primary">Проверить</el-button>
    </div>
  </el-card>
  <el-dialog v-model="opened"  width="550">
    
        <!-- <el-form :model="form">
        <el-form-item label="Количество бонусов" :label-width="formLabelWidth">
            <el-input v-model="currentMinusRow.amount" autocomplete="off" />
        </el-form-item>
        <el-form-item label="Описание">
            <el-input v-model="currentMinusRow.descr" type="textarea" />
        </el-form-item>
        </el-form> -->
        <div v-if="step == 1">
          <div v-if="loading">
            <h3>Поиск новых точек...</h3>
            <div style="display: flex; justify-content: flex-end;">
                <el-button :loading="true">Загрузка...</el-button>
            </div>
          </div>
          <div v-else>
            <div v-if="newPoints.length">
              <h4>Новые точки отправления из e-traffic</h4>
              <el-table :data="newPoints" style="width: 100%; margin: 10px 0;">
                <el-table-column label="ID" prop="id" />
                <el-table-column label="Город" prop="name" />
                <el-table-column label="Регион" prop="region" />
                <el-table-column label="Детали" prop="details" />
              </el-table>
              <el-button @click="addNewPoints()" type="primary">Добавить</el-button>            
            </div>
            <div v-else>
              <h4>Новых точек в e-traffic нет</h4>
            </div>
          </div>
        </div>
        <div v-if="step==2">
          <div v-if="loading">
            <h3>Поиск новых точек...</h3>
            <div style="display: flex; justify-content: flex-end;">
                <el-button :loading="true">Загрузка...</el-button>
            </div>
          </div>
          <div v-else-if="error">
            <el-alert title="Непредвиденная ошибка" type="error" :closable="false" />            
          </div>
          <div v-elseЯ>
            <el-alert title="Точки успешно загружены, а sitemap дополнен рейсами 
            из новых точек." type="success" :closable="false" />
          </div>
        </div>
    
  </el-dialog>  
</template>
<script>
import axiosClient from '../../axios';

export default {
    data() {
    return {
        opened: false,
        loading: false,
        newPoints: [],
        step: 1,
        error: false
    };
    },
    watch: {

    },
    methods: {
      async getNewPoints() {
        this.loading = true
        const promise = axiosClient
        .get('/new/points')
        .then(response => {
          console.log(response)
          this.newPoints = response.data.newPoints
        })
        .catch(error => {
          console.log(error)
        })
        await promise
        this.loading = false
      },
      async addNewPoints() {
        this.loading = true
        this.step = 2
        const promise = axiosClient
        .post('/new/points', {newPoints: this.newPoints})
        .then(response => {
          console.log(response)
          this.newPoints = response.data.newPoints
        })
        .catch(error => {
          console.log(error)
          this.error = true
        })
        await promise
        this.loading = false
      },      
    },
    async mounted(){

    }
};
</script>
<style scoped>
    
</style>