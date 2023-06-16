<template>
  <div>
    <ul>
      <li v-for="(passenger, index) in passengers" class="person-window">
        <div class="person-btn">
          <div>{{passenger.surname}} {{passenger.name}} {{passenger.patronymic}}</div>
          <div class="person__all-button">
            <button class="btn btn-secondary" @click="passenger.openInputs=!passenger.openInputs">Редактировать</button>
          <button v-if="!deleteLoading" class="btn btn-danger" 
            @click="deletePassanger(passenger.id)">Удалить</button>
            <button v-else class="btn btn-danger" type="button" disabled>
              <span style="margin-right: 10px;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <span class="sr-only">Загрузка...</span>
            </button>
          </div>
        </div>
        <Transition name="animation">
          <div v-if="passenger.openInputs" novalidate>
          <div class="form__all-input">
         
          <div class="left-input all-input-item">
            <label for="">Фамилия</label>
            <!--  -->
            <input
              type="text"
              class="form-control"
              name="name"
              id="inputName"
              placeholder="Иванов"
              maxlength="60"
              :class="{'is-invalid': passenger.errors.surname}"
              v-model="passenger.surname"
              oninput="this.value=this.value.replace(/[^a-zA-ZА-Яа-яЁё]/g,'');"
              required
            />
            <!--  -->
            <!--  -->
            <label for="">Имя</label>
            <input
              type="text"
              class="form-control"
              name="name"
              id="inputName"
              placeholder="Иван"
              v-model="passenger.name"
              maxlength="60"
              :class="{'is-invalid': passenger.errors.name}"
              oninput="this.value=this.value.replace(/[^a-zA-ZА-Яа-яЁё]/g,'');"
              required
            />
            <!--  -->
            <!--  -->
            <label for="">Отчество</label>
            <input
              type="text"
              class="form-control"
              name="name"
              id="inputName"
              placeholder="Иванович"
              v-model="passenger.patronymic"
              :class="{'is-invalid': passenger.errors.patronymic}"
              maxlength="60"
              oninput="this.value=this.value.replace(/[^a-zA-ZА-Яа-яЁё]/g,'');"
              required
            />
            <!--  -->
            <!--  -->
            <label for="inputRegion" class="form-label">Пол</label>
            <select
              class="form-select form-control"
              name="region"
              id="inputRegion"
              maxlength="60"
              required
            >
              <option :selected="passenger.gender == 'M'" data-gender="M" value="Мужчина">Мужчина</option>
              <option :selected="passenger.gender == 'F'" data-gender="F" value="Женщина">Женщина</option>
            </select>
            <!--  -->
            <!--  -->
            <label for="">Дата рождения</label>
            <input
              type="date"
              class="form-control"
              name="name"
              id="inputName"
              placeholder="Иван"
              maxlength="60"
              :class="{'is-invalid': passenger.errors.birth_date}"
              v-model="passenger.birth_date"
              required
            />
          </div>
          <div class="right-input all-input-item">
            <!--  -->
            <!--  -->
            <label for="inputdoc" class="form-label">Тип билета</label>
            <input
              type="text"
              class="form-control"
              name="doc"
              id="inputdoc"
              maxlength="30"
              :value="passenger.ticket_type"
              required
              disabled
              placeholder="серия и номер: 10 цифр"
            />
            <label for="" class="form-label">Гражданство</label>
            <select
              class="form-select form-control"
              v-model="passenger.citizenship"
              :class="{'is-invalid': passenger.errors.citizenship}"
              required
            >
              <template v-for="country in countries">
                <option :selected="country.name == passenger.citizenship" :data-id="country.id" :value="country.name">{{ country.name }}</option>
              </template>
            </select>            
            <label for="inputdoc" class="form-label">Тип документа</label>
            <input
              type="text"
              class="form-control"
              name="doc"
              id="inputdoc"
              maxlength="30"
              :value="passenger.doc_type"
              required
              disabled
              placeholder="серия и номер: 10 цифр"
            />


            <!--  -->
            <!--  -->
            <label for="inputdoc" class="form-label">Номер документа</label>
            <input
              type="text"
              class="form-control"
              name="doc"
              id="inputdoc"
              maxlength="10"
              v-model="passenger.doc_number"
              :class="{'is-invalid': passenger.errors.doc_number}"
              required
              placeholder="серия и номер: 10 цифр"
            />
            <!--  -->
            <!--  -->
            <label for="inputdoc" class="form-label">Серия документа</label>
            <input
              type="text"
              class="form-control"
              name="dc"
              id="inputdoc"
              maxlength="10"
              required
              v-model="passenger.doc_series"
              :class="{'is-invalid': passenger.errors.doc_series}"
              placeholder="серия и номер: 10 цифр"
            />


          </div>
          
          </div>
          <button v-if="!editLoading" class="btn btn-primary" @click="editPassanger(passenger.id, index)">сохранить</button>
          <button v-else class="btn btn-primary" type="button" disabled>
            <span style="margin-right: 10px;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="sr-only">Загрузка...</span>
          </button>
        </div>
        </Transition>
      </li>
    </ul>
    <!-- <div class="personal-account__content-empty">
        <h2> "У вас пока нет сохранённых пользователей" </h2>
        </div> -->
  </div>
</template>
<script>
import axiosClient from '../axios';

export default
{
  data()
  {
    return{
      openInputs: false,
      passengers: [],
      countries: [],
      deleteLoading: false,
      editLoading: false
    }
  },
  async mounted(){
    const promise = axiosClient
    .get('/passengers')
    .then(response => {
      this.passengers = response.data.passengers
    })
    await promise
    this.passengers.forEach(el => {
      el.openInputs = false
      el.errors = {}
    })

    const promise1 = axiosClient
    .get('/countries')
    .then(response => {
      this.countries = response.data
    })
    await promise1
  },
  methods: {
    async deletePassanger(passengerId){
      if(!confirm('Вы действительно хотите удалить данного пассажира?')){
        return
      }
      this.deleteLoading = true
      const promise = axiosClient
      .post('/passenger/delete', {passengerId: passengerId})
      .then(response => {
      })
      await promise
      this.passengers = this.passengers.filter(el => {
          return el.id != passengerId
      });
      this.deleteLoading = false
    },
    async editPassanger(passengerId, index){
      this.editLoading = true
      this.passengers[index].errors = {}
      const promise = axiosClient
      .post('/passenger/edit', this.passengers[index])
      .then(response => {
      })
      .catch(error => {
        if(error.response.status == 422){
          this.passengers[index].errors = error.response.data.errors
        }
        
      })
      await promise
      this.editLoading = false
    }
  }
}
</script>
<style>
.person-window {

  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 25px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}
.person-btn
{
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.spinner-border-sm{
  margin-right: 0;
}
@media (max-width:500px){
  .person-btn
{
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
}
}
</style>
