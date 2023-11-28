<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable max-len -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/label-has-for -->
<template>
    <div>
      <div class="container">
        <Header :authForForm="authForForm" @authenticateForForm="authenticateForForm" @disauthenticateForForm="disauthenticateForForm"/>
      </div>
    </div>
    <div class="container">
      <BusLoading v-if="loadingRace"/>
    </div>

  <HeaderСrumbsVue :step="'second'" :race="race" v-if="!loadingRace"/>

  <div class="container" v-if="!loadingRace">
  <!-- <pre>{{ date1 }}</pre> -->
  <!-- <pre>{{ formData }}</pre>     -->
    <div class="form__content">
    <div class="information-race">
      <h5>О рейсе</h5>
      <hr>
      <DepartureArrival :race="race"/>
      <!-- <div class="information-race__price"><p>Цена:</p><p>1050,00₽</p></div> -->
      <div class="information-race__payment"><p class="inf-race-price-heading">К оплате <span class="inf-race__type__ticket"></span></p><p class="total-cost" >{{totalCost}},00₽  </p></div>
      <p style="font-size: 13px;">Включая сервисный сбор<br/> {{duePrice}},00₽</p>
      <p v-if="insured" style="font-size: 13px;">Включая страхование<br/> {{insurancePrice}},00₽</p>
      <hr>
      <a href="/return/conditions" class="blue__link" target="_blank">Условия возврата</a>
    </div>

    <div class="">
      <div v-for="(el, indexTicket) in formData" class="form-reg" style="position: relative;">
        <!-- <pre>{{ el.errors }}</pre> -->
        <svg v-if="formData.length > 1" @click="removePassenger(el.seat.code)" style="position: absolute; top: 7px; right: 7px; cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
        </svg>
        
      <h5>Оформление билета</h5>
        <p class="form-description">Указанные данные необходимы для совершения бронирования и будут проверены при посадке в автобус.</p>
        <div class="ticket-registration">
                  <select v-if="authForForm && passengers.length > 0"
                    class="form-select form-control "
                    maxlength="60"
                    @change="choosePassenger($event, indexTicket)"
                    required
                  >
                    <option value="" disabled selected hidden>Сохранённые пасажиры</option>
                    <option value="new"><strong>Новый пассажир</strong></option>
                    <option v-for="(passenger, indexPassenger) in passengers" :value="indexPassenger">{{passenger.surname}} {{passenger.name}} {{passenger.patronymic}}</option>
                  </select>
                  <hr>
      <div class="form__all-input">
        <div class="left-input all-input-item">
        <label for="">Фамилия</label>
        <!--  -->
        <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': el.errors.surname}"
                  placeholder="Укажите фамилию"
                  maxlength="60"
                  v-model="el.surname"
                  oninput="this.value=this.value.replace(/[^a-zA-ZА-Яа-яЁё]/g,'');"
                  @focus="el.errors.surname = ''"
                  required
                  :disabled="el.saved"
                />
                  <!--  -->
                  <!--  -->
        <label for="">Имя</label>
        <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid': el.errors.name}"
                    placeholder="Укажите имя"
                    v-model="el.name"
                    oninput="this.value=this.value.replace(/[^a-zA-ZА-Яа-яЁё]/g,'');"
                    @focus="el.errors.name = ''"
                    required
                    :disabled="el.saved"
                  />
                  <!--  -->
                  <!--  -->
                  <label for="">Отчество</label>
        <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid': el.errors.patronymic}"
                    placeholder="Укажите отчество"
                    maxlength="60"
                    v-model="el.patronymic"
                    oninput="this.value=this.value.replace(/[^a-zA-ZА-Яа-яЁё]/g,'');"
                    @focus="el.errors.patronymic = ''"
                    required
                    :disabled="el.saved"
                  />
                  <!--  -->
                  <!--  -->
                
                  <!--  -->
                  <!--  -->
                  <!--  -->
       
        </div>

        <div class="right-input all-input-item">
          <label for="" class="form-label">Тип билета</label>
                  <select
                    class="form-select form-control"
                    :class="{'is-invalid': el.errors.ticket_type_code}"
                    maxlength="60"
                    v-model="el.ticket_type_code"
                    @focus="el.errors.ticket_type_code = ''"
                    required
                    :disabled="el.saved"
                  >
                    <option :data-code="ticket.code" v-for="ticket in race.ticketTypes" :value="ticket.code">{{ ticket.name }}</option>
                  </select> 
                             <!--  -->
                  <!--  -->         
                  <!--  -->
                  <!--  -->
                  <label for="" class="form-label">Пол</label>
                  <select
                    class="form-select form-control"
                    :class="{'is-invalid': el.errors.gender}"
                    maxlength="60"
                    v-model="el.gender"
                    @focus="el.errors.gender = ''"
                    required
                    :disabled="el.saved"
                  >
                    <!-- <option data-gender="M" value="M">Мужчина</option>
                    <option data-gender="F" value="F">Женщина</option> -->
                    <option :selected="el.gender == 'M'" data-gender="M" value="M">Мужчина</option>
                    <option :selected="el.gender == 'F'" data-gender="F" value="F">Женщина</option>
                  </select>
                  <!--  -->
                  <!--  -->
                  <label for="">Дата рождения</label>
                  <input
                    type="date"
                    name="date"
                    id="date"
                    class="form-control"
                    :class="{'is-invalid': el.errors.birth_date}"
                    maxlength="60"
                    :max="dateNew"
                    v-model="el.birth_date"
                    @focus="el.errors.birth_date = ''"
                    required
                    :disabled="el.saved"
                  />
        </div>

        
      </div>
      <label for="" class="form-label">Гражданство</label>
                  <select
                    class="form-select form-control form-select-cust select2-results__options"
                    :class="{'is-invalid': el.errors.citizenship}"
                    v-model="el.citizenship"
                    @focus="el.errors.citizenship = ''"
                    required
                    :disabled="el.saved"
                  >
                    <option v-for="country in countries" :data-id="country.id" :value="country.name">{{ country.name }}</option>
                  </select>
      <!--  -->
        <div class="bottom__inputs">
                  <div class="bottom__inputs__container">
                    <label for="" class="form-label">Тип документа</label>
                    <select
                      class="form-select form-control"
                      :class="{'is-invalid': el.errors.doc_type}"
                      maxlength="60"
                      v-model="el.doc_type"
                      @focus="el.errors.doc_type = ''"
                      required
                      :disabled="el.saved"
                    >
                      <option :data-code="doc.code" v-for="doc in race.docTypes" :value="doc.code+'____'+doc.name">{{ doc.name }}</option>
                    </select>                
                  </div>

                  <!--  -->
                  <!--  -->
                  <div  class="bottom__inputs__container">
                    <label for="" class="form-label">Серия документа</label>
                    <input type="text"
                      class="form-control"
                      :class="{'is-invalid': el.errors.doc_series}"
                      maxlength='10'
                      required
                      v-model="el.doc_series"
                      placeholder="Укажите серию документа"
                      @focus="el.errors.doc_series = ''"
                      :disabled="el.saved"
                    >                     
                  </div>
 
                  <!--  -->
                  <div class="bottom__inputs__container">
                    <label for="" class="form-label">Номер документа</label>
                    <input type="text"
                      class="form-control"
                      :class="{'is-invalid': el.errors.doc_number}"
                      maxlength='10'
                      v-model="el.doc_number"
                      @focus="el.errors.doc_number = ''"
                      required
                      placeholder="Укажите номер документа"
                      :disabled="el.saved"
                    >
                  </div>
        
        </div>      
      <div class="seat-bus"><h5>Место в автобусе</h5>
        <select
          class="form-select form-control"
          :class="{'is-invalid': el.errors.doc_type}"
          maxlength="60"
          @change="changeSeat($event, el.seat.name)"
          required
        >
          <template v-for="seat in race.seats">
            <option v-if="(formData.filter(elem => {return seat.name == elem.seat.name}).length == 0) || (formData.filter(elem => {return seat.name == elem.seat.name}).length != 0  && el.seat.name == seat.name)" :selected="seat.name == el.seat.name" :value="seat.name">{{ seat.name }}</option>
          </template>
          
        </select>
      </div>      
      <div class="block__check">
        
        <label class="check" style="padding: 0px; display: flex; align-items: center; margin-bottom: 0px; ">
          <input style="opacity: 1; background-color: initial; margin-right: 3px;" :value="true" v-model="insured" type="radio">Страхование на время поездки
        </label>

        <p style="font-size: 12px; color: grey; margin-left: 26px;">АО "АльфаСтрахование" , тел.: 8 800 333 0 999, <a href="#">alfastrah.ru</a> предлагает страховую защиту от несчастных случаев на время поездки. Стоимость страхового полиса для полного билета составляет 25, 50 или 100 рублей в зависимости от стоимости билета. Размер страховой выплаты до 500 000 рублей. Подробнее в условиях <a href="#">Публичной оферты</a>, <a href="#">Правилах страхования</a></p> 
        <label class="check" style="padding: 0px; display: flex; align-items: center;">
          <input style="opacity: 1; background-color: initial; margin-right: 3px;" :value="false" v-model="insured" type="radio">Без страховки
        </label>

      </div> 
    </div>
      </div>
      <div class="form-reg">
        <div class="passenger__addition-outside">
          <button class="seat-bus__but" type="button" v-if="formData.length < 5" @click="addPassenger">Добавить пассажира</button>
          <button class="seat-bus__but" type="button" v-if="formData.length > 1" @click="removeLastPassenger">Удалить последнего пассажира</button>
        </div>

      </div> 
      <label v-if="unAuthMessage" style="color: red; font-size: 12px;">Для оформления билета необходимо авторизоваться!</label>
      <div v-if="!authForForm" class="form-reg" :class="{'unauth__user': unAuthMessage}">
        <div class="information-buyer">
          <Login v-if="option == 'login'" @resetSection="option = 'reset'" @registrationSection="option = 'registration'" :authForForm="authForForm" @authenticateForForm="authenticateForForm" @putRedFromLoginAway="putRedFromLoginAway" />
          <Registration v-else-if="option == 'registration'" @loginSection="option = 'login'" @putRedFromLoginAway="putRedFromLoginAway" :authForForm="authForForm" @authenticateForForm="authenticateForForm"/>
          <ResetPassword v-else @loginSection="option = 'login'" @putRedFromLoginAway="putRedFromLoginAway" :authForForm="authForForm" @authenticateForForm="authenticateForForm"/>
        </div>
      </div>
      <div class="form-reg">
      <div class="pay">
        <div class="information-race__payment"><h3>К оплате</h3><p class="total-cost" >{{ totalCost }},00₽</p></div>
        <p style="font-size: 13px;">Включая сервисный сбор<br/> {{duePrice}},00₽</p>
        <p style="font-size: 13px;">Включая страхование<br/> {{insurancePrice}},00₽</p>
        <hr class="line-pay">
        <div class="pay-discription">
          <p>Ваши платежные и личные данные надежно защищены в соответствии с международными стандартами безопасности.</p>
          <div class="payment-systems">
            <div class="maestro__logo pay-sys__logo"></div>
            <div class="mastercard__logo pay-sys__logo"></div>
            <div class="visa__logo pay-sys__logo"></div>
            <div class="mir__logo pay-sys__logo"></div>
          </div>
        </div>  
      </div>
      </div>
      <div v-if="errorMessageFromAPI" style="color: red;" class="">
        {{ errorMessageFromAPI }}
      </div>
      <div v-if="confirmBookLoading" class="text-center" style="margin: 10px 0;">
          <div class="spinner-border" role="status"></div>
      </div>      
      <button @click="confirmBook" :disabled="confirmBookLoading" class="pay-but">Перейти к оплате</button>
    </div>
    </div>
  </div>
  <transition name="fade" >
    <PopupWindow v-if="openWindow" @CloseWindow="openWindow = false, Scroll()" :content="content"/>
  </transition>
</template>
<script>
import HeaderСrumbsVue from '../components/HeaderСrumbs.vue';
import DepartureArrival from '../components/DepartureArrival.vue';
import PopupWindow from '../components/PopupWindow.vue';
import router from '../router'
import axios from 'axios';
import axiosClient from '../axios';
import { useVuelidate } from '@vuelidate/core'
import { required, email } from '@vuelidate/validators'
import Login from '../components/Login.vue';
import Registration from '../components/Registration.vue';
import ResetPassword from '../components/ResetPassword.vue';
import Header from '../components/Header.vue';
import BusLoading from '../components/BusLoading.vue';

export default
{
  components: { HeaderСrumbsVue, DepartureArrival, PopupWindow, Login, Registration, Header, ResetPassword, BusLoading },
  data() {
    return {
      user: [],
      openWindow: false,
      content: 2,
      chosenSeats: [],
      race: [],
      loadingRace: true,
      countries: [],
      formData: [],
      code: '',
      login: true,
      authForForm: false,
      unAuthMessage: false,
      sale: [],
      order: [],
      passengers: [],
      option: 'login',
      errorMessageFromAPI: '',
      dateNew: '',
      confirmBookLoading: false,
      payment: [],
      duePercent: 0,
      duePrice: 0,
      date1: '',
      insured: false
    };
  },
  methods: {
    NoScroll() {
      document.body.style.overflow = 'hidden';
    },
    Scroll() {
      document.body.style.overflow = 'auto';
    },
    async confirmBook(code){
      if(!this.validateFrom()){
        this.confirmBookLoading = true
        this.errorMessageFromAPI = ''
        this.sale = []
        this.formData.forEach(el => {this.sale.push(
          {
            lastName: el.surname,
            firstName: el.name,
            middleName: el.patronymic,
            docTypeCode: el.doc_type.split('____')[0],
            docTypeName: el.doc_type.split('____')[1],
            docSeries: el.doc_series,
            docNum: el.doc_number,
            gender: el.gender,
            citizenship: el.citizenship,
            birthday: el.birth_date,
            phone: this.user.phone,
            email: this.user.email,
            seatCode: el.seat.code,
            ticketTypeCode: el.ticket_type_code,
            ticketTypeName: this.race.ticketTypes.filter(elem => {
                return elem.code == el.ticket_type_code
            })[0].name,
            saved: el.saved
          }
          )
        })
        const promise2 = axiosClient
        .post('/order/book', {uid: this.$route.params['race_id'], sale: this.sale, insured: this.insured, insurancePrice: this.insurancePrice})
        .then(response => {
          console.log(response)
          this.order = response.data.order
          this.payment = response.data.payment
          console.log(this.payment.formUrl)
        })
        .catch(error => {
          console.log(error)
          if(error.response.data.error == null || error.response.data.error.errorMessage == null){
            this.errorMessageFromAPI = 'Произошла непредвиденная ошибка. Повторите ещё раз позже!'
          }
          else{
            this.errorMessageFromAPI = error.response.data.error.errorMessage
            
          }
        })
        await promise2
        if(!this.errorMessageFromAPI){
          window.open(this.payment.formUrl, '_self');
        }
        this.confirmBookLoading = false
      }
    },
    validateFrom(){
      let formHasErrors = false
      this.formData.forEach(el => {
        let fields = ['name', 'surname', 'patronymic', 'birth_date', 'gender', 'citizenship', 'doc_type', 'doc_number', 'ticket_type_code']
        fields.forEach(field => {
          if(!el[field]){
            el.errors[field] = 'Поле обязательно для заполнения!'
            formHasErrors = true
          }
          else{
            el.errors[field] = ''
          }
        })
        let doc_type_name = el.doc_type.split('____')[1]
        if(el.doc_type && (doc_type_name == 'Паспорт гражданина РФ' 
        || doc_type_name == 'Загранпаспорт гражданина РФ' 
        || doc_type_name == 'Свидетельство о рождении РФ'
        || doc_type_name == 'Военный билет военнослужащего срочной службы') && !el.doc_series)
        {
          el.errors.doc_series = 'Поле обязательно для заполнения!'
          formHasErrors = true
        }
        else{
          el.errors.doc_series = ''
        }
      })
      if(!this.authForForm){
        this.unAuthMessage = 'Необходимо авторизоваться!'
        formHasErrors = true
      }
      return formHasErrors
    },
    async authenticateForForm(){
      const promise1 = axiosClient
      .get('/user')
      .then(response => (
          this.user = response.data.user
      ));
      await promise1
      const promise2 = axiosClient
      .get('/passengers')
      .then(response => {
        this.passengers = response.data.passengers
      })
      .catch(error => {

      })
      await promise2  
      this.authForForm = true
    },
    disauthenticateForForm(){
      this.authForForm = false
      this.user = []
      this.formData.forEach(el => {
        el.saved = false
      })
    },
    putRedFromLoginAway(){
      this.unAuthMessage = ''
    },
    choosePassenger(event, indexTicket){
      let passenger = this.passengers[event.target.value]
      if(event.target.value == 'new'){
        this.formData[indexTicket].name = ''
        this.formData[indexTicket].surname = ''
        this.formData[indexTicket].patronymic = ''
        this.formData[indexTicket].birth_date = ''
        this.formData[indexTicket].citizenship = 'РОССИЯ'
        this.formData[indexTicket].doc_number = ''
        this.formData[indexTicket].doc_series = ''
        this.formData[indexTicket].gender = ''

        this.formData[indexTicket].doc_type = ''

        this.formData[indexTicket].ticket_type_code = this.race.ticketTypes.filter(el => {
          return el.name == 'Полный' || 'Пассажирский';
        })[0].code

        this.formData[indexTicket].saved = false     
      }
      else{
        this.formData[indexTicket].name = passenger.name
        this.formData[indexTicket].surname = passenger.surname
        this.formData[indexTicket].patronymic = passenger.patronymic
        this.formData[indexTicket].birth_date = passenger.birth_date
        this.formData[indexTicket].citizenship = passenger.citizenship
        this.formData[indexTicket].doc_number = passenger.doc_number
        this.formData[indexTicket].doc_series = passenger.doc_series
        this.formData[indexTicket].gender = passenger.gender

        this.formData[indexTicket].doc_type = this.race.docTypes.filter(el => {
            return el.name == passenger.doc_type;
          })[0].code+'____'+passenger.doc_type

        this.formData[indexTicket].ticket_type_code = this.race.ticketTypes.filter(el => {
            return el.name == passenger.ticket_type;
          })[0].code

        this.formData[indexTicket].saved = true        
      }

    },
    changeSeat(event, oldSeatName){
      let newSeat = this.race.seats.filter(el => {
        return el.name == event.target.value //имя места
      })[0]
      this.formData.forEach(function (el) {
        if (el.seat.name == oldSeatName) {
          el.seat = newSeat 
        }    
      });
      this.updateSession()
    },
    updateSession(){
      let tempChosenSeats = []
      let tempRaceSeats = this.race.seats
      this.formData.forEach(function(el){
        let temp = tempRaceSeats.filter(elem => {
          return el.seat.code == elem.code
        })[0]
        el.seat = temp
        tempChosenSeats.push(temp)
      })
      localStorage.setItem('chosenSeats', JSON.stringify(tempChosenSeats))
    },
    addPassenger(){
      if(this.formData.length == 5){
        return
      }
      let totalTicketCode = this.race.ticketTypes.filter(el => {
        return el.name == 'Полный' || 'Пассажирский';
      })
      totalTicketCode = totalTicketCode[0].code
      // let seat = this.formData.filter(el => {
      //   return 
      // })
      let seat = []
      this.race.seats.forEach(el => {
        let tempSeat = this.formData.filter(elem => {
          return elem.seat.name == el.name;
        })
        if(tempSeat.length == 0){
          seat = el
        }
      })
      this.formData.push(
        {
          name: '',
          surname: '',
          patronymic: '',
          birth_date: '',
          gender: '',
          citizenship: 'РОССИЯ',
          doc_type: '',
          doc_number: '',
          doc_series: '',
          ticket_type_code: totalTicketCode,
          seat: seat,
          saved: false,
          errors: {
            name: '',
            surname: '',
            patronymic: '',
            birth_date: '',
            gender: '',
            citizenship: '',
            doc_type: '',
            doc_number: '',
            doc_series: '',
            ticket_type_code: '',
          }
        }
      )
      this.updateSession()
    },
    removeLastPassenger(){
      if(this.formData.length == 1){
        return
      }
      this.formData.pop();
      this.updateSession()
    },
    removePassenger(seatCode){
      if(this.formData.length == 1){
        return
      }
      let tempFormData = this.formData.filter(el => {
        return el.seat.code != seatCode
      })
      
      this.formData = tempFormData
      this.updateSession()
    },
    checkChosenSeats(){
      if(!this.chosenSeats){
        return false
      }
      let tempRaceSeats = this.race.seats
      let invalidSeats = false
      this.chosenSeats.forEach(function(el){
        let temp = tempRaceSeats.filter(elem => {
          return el.code && elem.code && el.code == elem.code
        })[0]
        if(!temp){
          invalidSeats = true
        }
      })
      if(invalidSeats){
        return false
      }
      else{
        return true        
      }

    }//[{"cdode":"118011","name":"Место 20","type":null}]
  },
  computed: {
    totalCost(){
      let totalCost = 0;
      this.duePrice = 0
      this.formData.forEach(el => {
        let ticket_type_code = el.ticket_type_code
        let ticket_price = this.race.ticketTypes.filter(el => {
          return el.code == ticket_type_code;
        })
        ticket_price = ticket_price[0].price
        totalCost += ticket_price
        this.duePrice += Math.ceil(ticket_price * this.duePercent / 100)
      })
      console.log(totalCost, this.duePrice, this.insurancePrice)
      console.log(totalCost+this.duePrice+this.insurancePrice)
      return totalCost+this.duePrice+this.insurancePrice;
    },
    insurancePrice(){
      if(this.insured){
        let insurancePrice = 0
        this.formData.forEach(el => {
          let ticket_type_code = el.ticket_type_code
          let ticket_price = this.race.ticketTypes.filter(el => {
            return el.code == ticket_type_code;
          })
          ticket_price = ticket_price[0].price
          if(ticket_price <= 500){
            insurancePrice += 140
          }
          else if(ticket_price > 500 && ticket_price <= 1000){
            insurancePrice += 140
          }
          else{
            insurancePrice += 140
          }
        })
        console.log(insurancePrice)
        return insurancePrice
      }
      else{
        console.log(0)
        return 0;
      }
    }
  },
  async mounted(){
    this.loading = true
    var dateNewGet = new Date();
    this.dateNew = dateNewGet.getFullYear()+ "-" + (dateNewGet.getMonth() + 1 > 9? dateNewGet.getMonth() + 1 : "0" + (dateNewGet.getMonth()+ 1)) + "-" + dateNewGet.getDate();
    this.loadingRace = true
    if(localStorage.getItem('authToken')){
      this.auth = true
      this.authenticateForForm()
    }
    const promise = axiosClient
      .get('/race/'+this.$route.params['race_id'])
      .then(response => (
          this.race = response.data
      ));
    await promise
    const promise1 = axiosClient
      .get('/countries')
      .then(response => (
          this.countries = response.data
      ));
    await promise1
    this.chosenSeats = JSON.parse(localStorage.getItem('chosenSeats'))
    
    if(!this.checkChosenSeats()){
      router.push({ name: 'SeatPage', params: { race_id: this.race.race.uid} })
      localStorage.removeItem('chosenSeats')
      return    
    }
    let totalTicketCode = this.race.ticketTypes.filter(el => {
      return el.name == 'Полный' || 'Пассажирский';
    })
    totalTicketCode = totalTicketCode[0].code
    this.chosenSeats.forEach(el => this.formData.push(
      {
        name: '',
        surname: '',
        patronymic: '',
        birth_date: '',
        gender: '',
        citizenship: 'РОССИЯ',
        doc_type: '',
        doc_number: '',
        doc_series: '',
        ticket_type_code: totalTicketCode,
        seat: el,
        saved: false,
        errors: {
          name: '',
          surname: '',
          patronymic: '',
          birth_date: '',
          gender: '',
          citizenship: '',
          doc_type: '',
          doc_number: '',
          doc_series: '',
          ticket_type_code: '',
        }
      }
    ))
    this.updateSession()
    if(this.authForForm){
      const promise2 = axiosClient
      .get('/passengers')
      .then(response => {
        this.passengers = response.data.passengers
      })
      .catch(error => {

      })
      await promise2      
    }
    const promise3 = axiosClient
    .get('/settings/cluster/due')
    .then(response => {
      console.log(response.data.clusterDue)
      this.duePercent = response.data.clusterDue
    })
    .catch(error => {
      console.log(error)
    })
    await promise3
    this.loadingRace = false
  }
};
</script>
<style>

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
  transition-delay: 10s;
}
.fade-enter-from, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */ {
  opacity: 0;
}
.fade-enter-to, .fade-leave-frome{
  opacity: 1;
}
:root{
  --blue: #2196F3;
}

body
{
  background-color: #f5f5f5 !important;
}
.form__content
{
  display: flex;
  flex-direction: row-reverse;
  justify-content: space-between;
  align-items: start;
}
.form-select-cust
{
  overflow: hidden;
  
}
.form-reg
{
  flex: 1;
  margin-right: 20px;
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 25px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}
.information-race
{
  background-color: #fff;
  padding: 20px;
  border-radius: 10px;
  max-width: 300px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}
.place__of__departure
{
  display: flex;
  justify-content:start;
}
.place-of-arrival
{
  display: flex;
  justify-content: start;
}
.block__check label
{
  font-size: 13px !important;
}
.information-race__price
{
  display: flex;
  justify-content: space-between;
}
.information-race__payment
{
  /* width: 85%; */
  /* display: flex; */
  justify-content: space-between;
  margin-top: 10px;
}
.inf-race__date-and-time
{
  min-width: 50px;
}
.inf-race__city
{
  font-weight: 700;
  position: relative;
}
label
{
  margin-bottom: 0px;
}
.inf-race__city::before
{
  content: "";
  position: absolute;
  left: -25px;
  top: 5px;
  height: 10px;
  width: 10px;
  border: 1px solid var(--blue) ;
  border-radius: 50%;
  background-color: rgb(233, 233, 233);
  z-index: 5;
}
.inf-race__place
{
  margin-left: 40px;
  font-size: 14px;
  line-height: 1.1;
}
.inf-race__type__ticket
{
  display: block;
  font-weight: 100;
  font-size: 14px;
}
.inf-race-price-heading
{
  font-weight: 700;
  font-size: 18px;
}
.inf-buyer__input
{
  display: flex;
  justify-content: space-between;
}
.inf-buyer__input-item
{
  flex: 1;
  max-width: 49%;
}
.inf-race__date
{
  font-size: 11px;
}
.inf-race__time
{
  margin-bottom: 3px;
  font-weight: 700;
}
.discr
{
  position: relative;
  display: flex;
  margin-bottom: 4px;
  min-height: 25px;
}
.punktir::after
{
  content: "";
  height: 100%;
  position: absolute;
  border: 1px dashed rgb(209, 209, 209);
  left: -21px;
  top: 5px;

}
.punktir
{
  position: relative;
}
.container{
  max-width: 1044px;
  margin: 0 auto;
  padding: 0 15px;
}
.form__all-input
{
  display: flex;
  justify-content: space-between;
}
.all-input-item
{
  display: flex;
  flex-direction: column;
  flex: 1;
  max-width: 49%;
}
.form-control
{
  height: 40px;
  border-radius: 5px;
  border: 1px solid black;
  margin: 10px 0px;
}
.form-description
{
  display: flex;
}
.form-description::before
{
  content: "";
  position: relative;
  width: 5px;
  background-color: var(--blue);
  left: -30px;
  border-radius: 5px;
}
.total-cost
{
  font-size: 26px;
}
.seat-bus__but
{
  width: 49%;
  min-height: 40px;
  border-radius: 10px;
  border: 1px solid var(--blue);
  color: var(--blue);
  background-color: #fff;
}
.maestro__logo
{
  background-image: url("/public/img/ms_vrt_pos.svg");
}
.mastercard__logo
{
  background-image: url("/public/img/mc_symbol.svg");
}
.visa__logo
{
  background-image: url("/public/img/visa_payment_method_card_icon_142746.png");
}
.mir__logo
{
  background-image: url("/public/img/mir-logo.png");
}
.pay-sys__logo
{
  width: 45px !important;
  height: 45px !important;
  background-size: contain;
  background-position: center;
  background-repeat: no-repeat;
  margin: 5px;
}
.payment-systems
{
  display: flex;
  margin-left: 10px;
}
.pay-discription
{
  display: flex;
  justify-content: space-between;
}
.line-pay
{
  margin-top: 0px;
}

.checkmark {
  position: absolute;
  top: 4px;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: #eee;
  border-radius: 5px;
}
.check {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.check:hover input ~ .checkmark {
  background-color: #ccc;
}
.check input:checked ~ .checkmark {
  background-color: var(--blue);
}
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}
.check input
{
  opacity: 0;
}
.check input:checked ~ .checkmark:after {
  display: block;
}
.check .checkmark:after {
  left: 5px;
  top: 3px;
  width: 9px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
  border-radius: 2px;
}

.pay-but
{
  width: 97%;
  height: 50px;
  background-color: var(--blue);
  border-radius: 5px;
  color: white;
  flex: 1;
  margin-right: 20px;
  border-radius: 10px;
  margin-bottom: 25px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}

.unauth__user{
  border: 1px solid red;
}
.bottom__inputs{
  display: flex;
  justify-content: space-between;
}
.bottom__inputs__container{
  width: 32%;  
}
/* select */
.select2-results__options::-webkit-scrollbar {
  width: 6px;
}
.select2-results__options::-webkit-scrollbar-track {
  background-color: transparent;
  height: 8px;


}

.select2-results__options::-webkit-scrollbar-thumb {

  background-color: #c4c4c4;
  border-radius: 20px;

}
select
{
  max-height: 300px;
  overflow: hidden;
}

/*  */
.passenger__addition-outside{
  display: flex;
  justify-content: space-between;
}
.passenger__addition-outside button{
  width: 48%;
}
@media (max-width: 768px)
{
  .form__all-input
  {
    display: block;
    margin-top: 20px;
  }
  .all-input-item
  {
    display: block;
    max-width: 100%;
  }
  .form__content
  {
    display: block;
  }
  .information-race
  {
    max-width: 100%;
    margin-bottom: 40px;
  }
  .payment-systems
  {
    display: block;
  }
  .form-reg
  {
    margin-right: 0px;
  }
  .pay-but{
    margin-right: 0px;
  }
  .bottom__inputs{
    display: flex;
    flex-direction: column;
  }
  .bottom__inputs div{
    width: 100%;  
  }
  .passenger__addition-outside{
  display: flex;
  justify-content: space-between;
  flex-direction: column;
}
.passenger__addition-outside button{
  width: 100%;
}
.passenger__addition-outside button:nth-child(1){
  margin-bottom: 20px;
}
.seat-bus__but{
    font-size: 14px;
  }
  h3{
    font-size: 22px;
  }
  .total-cost
  {
    font-size: 22px;
  }
  .information-race__payment
  {
    width: 100%;
    justify-content: space-between;
  }
}
  
</style>