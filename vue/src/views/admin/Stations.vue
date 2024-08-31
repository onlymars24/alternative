<template>

  <Header/>
  <el-container v-loading.fullscreen.lock="loading">
      <el-main style="min-height: 500px;" v-if="!loading">
          <h4>Создать новый автовокзал</h4>
          <StationForm :formType="'create'" :station="newStation" :kladrsConnected="kladrsConnected" :loading="loading" @submitFrom="createStation"/>
          <hr>
          <div>
            <h4>Редактировать существующий автовокзал</h4>
                <div class="" style="margin-bottom: 20px;">
                  <label for=""><strong>Выберите существующий автовокзал для редактирования:</strong></label><br>
                  <el-select style="width: 25%;" v-model="selectedStationId" filterable>
                      <el-option
                      v-for="station in stations"
                      :key="station.id"
                      :label="station.name+', '+station.kladr.name+', '+station.kladr.region"
                      :value="station.id">
                      </el-option>
                  </el-select>
                </div>
              <hr>
              <template v-if="selectedStation">
                  <StationForm :formType="'edit'" :station="selectedStation" :kladrsConnected="kladrsConnected" :loading="loading" @submitFrom="editStation" @deleteStation="deleteStation"/>
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
import axiosClient from '../../axios'
import router from '../../router'
import StationForm from '../../components/admin/StationForm.vue'
import Header from '../../components/admin/Header.vue'
import axiosAdmin from '../../axiosAdmin'

export default
{
    components: {StationForm, Header},
    data() {
        return {
          newStation: {
            name: '',
            address: '',
            longitude: '',
            latitude: '',
            kladr_id: null
          },
          kladrsConnected: [],
          selectedStationId: null,
          selectedStation: null,
          stations: [],
          loading: false
        }
    },
    methods: {
      async getAll(){
          this.stations = []
          const promise = axiosAdmin
          .get('/stations')
          .then(response => {
              this.stations = response.data.stations
          })
          .catch(error => {
              console.log(error)
          })
          await promise
          if(!this.selectedStationId){
              this.selectedStationId = this.stations[0].id
          }
      },
      async createStation(station){
          console.log(this.newBusStation)
          this.loading = true
          const promise = axiosAdmin
          .post('/station/create', station)
          .then(response => {
              console.log(response)
          })
          .catch(error => {
              console.log(error)
          })
          await promise
          this.newStation.name = ''
          this.newStation.address = '';
          this.newStation.latitude = '';
          this.newStation.longitude = '';
          this.newStation.kladr_id = ''
          this.getAll()
          this.loading = false
          location.reload(); return false;
      },
      
      async editStation(station){
          this.loading = true
          const promise = axiosAdmin
          .post('/station/edit',
              station)
          .then(response => {
              console.log(response)
          })
          .catch(error => {
              console.log(error)
          })
          await promise
          this.getAll()
          this.loading = false
          location.reload(); return false;
      },
      async deleteStation(id){
          if(!confirm('Вы уверены, что хотите удалить станцию? ОТМЕНИТЬ ДЕЙСТВИЕ БУДЕТ НЕВОЗМОЖНО!')){
            return
          }
          this.loading = true
          const promise = axiosAdmin
          .post('/station/delete', {id: id})
          .then(response => {
              console.log(response)
          })
          .catch(error => {
              console.log(error)
          })
          await promise
          this.getAll()
          this.loading = false
          location.reload(); return false;
      }
    },
    async mounted(){
      const promise1 = axiosAdmin
      .get('/kladrs/connected')
      .then(response => {
        this.kladrsConnected = response.data.kladrs
      })
      .catch(error => {
        console.log(error)
      })
      await promise1

      this.getAll()
    },
    watch: {
      async selectedStationId(newStationId){
        const promise = axiosAdmin
        .get('/station/id?id='+newStationId)
        .then(response => {
            this.selectedStation = response.data.station
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        // this.paramKey++
      }
    }

}
</script>