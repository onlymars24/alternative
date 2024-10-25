<template>
  <Header/>
  <el-container v-loading.fullscreen.lock="loading">
      <el-main style="min-height: 500px;" v-if="!loading">
          <h4>Создать новую точку кладр</h4>
          <KladrForm :formType="'create'" :kladr="newKladr" @submitForm="create"/>
          <hr>
          <div>
            <h4>Редактировать существующую точку кладр</h4>
                <div class="" style="margin-bottom: 20px;">
                  <label for=""><strong>Выберите существующий точку кладр для редактирования:</strong></label><br>
                  <el-select style="width: 25%;" v-model="selectedKladrId" placeholder="" filterable clearable :loading="loadingFilter" remote :remote-method="customKladrFilter">
                      <el-option
                      v-for="kladr in kladrs"
                      :key="kladr.id"
                      :value="kladr.id"
                      :label="kladr.name"
                      >
                      <KladrExpression :kladr="kladr"/>
                      </el-option>
                  </el-select>
                </div>
              <hr>
              <template v-if="selectedKladr">
                  <KladrForm :formType="'edit'" :kladr="selectedKladr" @submitForm="edit" @deleteKladr="deleteKladr"/>
              </template>

            <!--  <div class="" style="margin-bottom: 20px;">
                  <label for=""><strong>Выберите существующий автовокзал для редактирования:</strong></label><br>
                  <el-select style="width: 25%;" v-model="selectedBusStationDispatchPointId" filterable clearable :disabled="busStationsDispatchPoints.length == 0">
                      <el-option
                      v-for="station in busStationsDispatchPoints"
                      :key="station.id"
                      :label="station.name"
                      :value="station.id">
                      </el-option>
                  </el-select>
              </div> -->

          </div>
      </el-main>
  </el-container>
</template>


<script>
import KladrForm from '../../components/admin/KladrForm.vue'
import Header from '../../components/admin/Header.vue'
import KladrExpression from '../../components/KladrExpression.vue'
import axiosAdmin from '../../axiosAdmin'
export default
{
    components: {KladrForm, KladrExpression, Header},
    data() {
        return {
            newKladr: {
                name: '',
                code: ''
            },
            selectedKladrId: '',
            selectedKladr: null,
            kladrs: [],
            loading: false,
            loadingFilter: false
        }
    },
    methods: {
        async customKladrFilter(query){
            if(query.length <= 2){
                return
            }
            this.loadingFilter = true
            const promise =  axiosAdmin
            .get('/custom/kladrs/filter?customKladrFilter='+query)
            .then(response => {
                this.kladrs = response.data.kladrs
                // console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.loadingFilter = false
        },
        async create(newKladr){
            this.loading = true
            await axiosAdmin
            .post('/custom/kladrs/create', newKladr)
            .then(response => {
                console.log(response)
                this.kladrs = response.data.kladrs
            })
            .catch(error => {
                console.log(error)
            })
            this.newKladr.name = this.newKladr.socr = this.newKladr.code = this.newKladr.region 
            = this.newKladr.city = this.newKladr.district = this.newKladr.relevance = ''
            this.loading = false
        },
        async edit(kladr){
          this.loading = true
          const promise = axiosAdmin
          .post('/custom/kladrs/edit', kladr)
          .then(response => {
              console.log(response)
          })
          .catch(error => {
              console.log(error)
          })
          await promise
          this.loading = false
        //   location.reload(); return false;
      },
      async deleteKladr(kladrId){
          if(!confirm('Вы уверены, что хотите удалить точку кладр? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
            return
          }
          this.loading = true
          const promise = axiosAdmin
          .post('/custom/kladrs/delete', {kladrId})
          .then(response => {
              console.log(response)
          })
          .catch(error => {
              console.log(error)
          })
          await promise
          this.loading = false
          location.reload(); return false;
      }
    },
    watch: {
        async selectedKladrId(selectedKladrId){
            await axiosAdmin
            .get('/custom/kladrs/one?kladrId='+selectedKladrId)
            .then(response => {
                console.log(response)
                this.selectedKladr = response.data.kladr
            })
            .catch(error => {
                console.log(error)
            })
        }
    }

}
</script>