<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable max-len -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/label-has-for -->
<template>
  
  <HeaderСrumbsVue />
  <pre>{{ race }}</pre>
  <pre>{{ formData }}</pre>
  <div class="container">
    <div class="form__content">
    <div class="information-race">
      <h5>О рейсе</h5>
      <hr>
      <DepartureArrival />
      <!-- <div class="information-race__price"><p>Цена:</p><p>1050,00₽</p></div> -->
      <div class="information-race__payment"><p class="inf-race-price-heading">К оплате <span class="inf-race__type__ticket"></span></p><p class="total-cost" >1050,00₽</p></div>
      <hr>
      <a class="blue__link" @click="openWindow = !openWindow, NoScroll()">Условия возврата</a>
    </div>

    <form @submit.prevent="validateFrom" class="" novalidate>
      <div v-for="el in formData" class="form-reg">
        <pre>{{ el.errors }}</pre>
      <h5>Оформление билета</h5>
        <p class="form-description">Указанные данные необходимы для совершения бронирования и будут проверены при посадке в автобус.</p>
      <div class="ticket-registration">
        <select
                    class="form-select form-control"
                    name="region"
                    id="inputRegion"
                    maxlength="60"
                    v-model="el.gender"
                    required
                  >
                    <option value="" disabled selected hidden>Сохранённые пасажиры</option>
                    <option value="">Акакий Акак Акакевович</option>
                    <option value="">Атакий Атак Атакевович</option>
                  </select>
                  <hr>
      <div class="form__all-input">
        <div class="left-input all-input-item">
        <label for="">Фамилия</label>
        <!--  -->
        <input
                    type="text"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иванов"
                    maxlength="60"
                    v-model="el.surname"
                    required
                  />
                  <!--  -->
                     <!--  -->
        <label for="">Имя</label>
        <input
                    type="text"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иван"
                    maxlength="5"
                    v-model="el.name"
                    oninput="this.value=this.value.replace(/[^a-zA-ZА-Яа-яЁё]/g,'');"
                    @focus="el.errors.name = ''"
                    required
                  />
                  <!--  -->
                  <!--  -->
                  <label for="">Отчество</label>
        <input
                    type="text"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иванович"
                    maxlength="60"
                    v-model="el.patronymic"
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
                    v-model="el.gender"
                    required
                  >
                    <option data-gender="M" value="M">Мужчина</option>
                    <option data-gender="F" value="F">Женщина</option>
                  </select>
                  <!--  -->
                          <!--  -->
                          <label for="">Дата рождения</label>
                  <input
                    type="date"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иван"
                    maxlength="60"
                    v-model="el.birth_date"
                    required
                  />
                  <!--  -->
       
        </div>

        <div class="right-input all-input-item">
                             <!--  -->
                             <label for="inputRegion" class="form-label">Документы</label>
                  <select
                    class="form-select form-control"
                    name="region"
                    id="inputRegion"
                    maxlength="60"
                    v-model="el.doc_type_code"
                    required
                  >
                    <option :data-code="doc.code" v-for="doc in race.docTypes" :value="doc.code+'____'+doc.name">{{ doc.name }}</option>
                  </select>
                  <!--  -->
                  <!--  -->
                  <label for="inputRegion" class="form-label">Тип билета</label>
                  <select
                    class="form-select form-control"
                    name="region"
                    id="inputRegion"
                    maxlength="60"
                    v-model="el.ticket_type_code"
                    required
                  >
                    <option :data-code="ticket.code" v-for="ticket in race.ticketTypes" :value="ticket.code">{{ ticket.name }}</option>
                  </select>          
                  <!--  -->
                  <!--  -->
                  <label for="inputRegion" class="form-label">Гражданство</label>
                  <select
                    class="form-select form-control"
                    name="region"
                    id="inputRegion"
                    v-model="el.citizenship"
                    required
                  >
                    <option v-for="country in countries" :data-id="country.id" :value="country.name">{{ country.name }}</option>
                  </select>
                  <!--  -->
                  <!--  -->
                  <label for="inputdoc" class="form-label">Номер документа</label>
                  <input type="text"
                    class="form-control"
                    name="doc"
                    id="inputdoc"
                    maxlength='10'
                    v-model="el.doc_number"
                    required
                    placeholder="номер"
                  >
                  <!--  -->
                  <!--  -->
                  <label for="inputdoc" class="form-label">Серия документа</label>
                  <input type="text"
                    class="form-control"
                    name="dc"
                    id="inputdoc"
                    maxlength='10'
                    required
                    v-model="el.doc_series"
                    placeholder="серия"
                  >
        </div>
      </div>
      </div>
      </div>
      <div class="form-reg">
      <div class="information-buyer">
        <Login v-if="login" @log="login=false"/>
        <Registration v-else @log="login=true"/>
      </div>
      </div>
      <div class="form-reg">
      <div class="pay">
        <div class="information-race__payment"><h3>К оплате</h3><p class="total-cost" >1050,00₽</p></div>
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
        <div class="block__check">
             <label class="check">Я принимаю условия <span class="blue__link" @click="openWindow = !openWindow, NoScroll(), content=2">Пользовательского соглашения</span> (публичной оферты) и <span class="blue__link" @click="openWindow = !openWindow, NoScroll()">политики конфиденциальности</span>
              <input type="checkbox">
              <span class="checkmark"></span>
            </label>
            <label class="check">Я даю <span class="blue__link" @click="openWindow = !openWindow, NoScroll()">Cогласие на обработку персональных данных</span>
              <input type="checkbox">
              <span class="checkmark"></span>
            </label>
          </div>
      </div>
      </div>
      <button class="pay-but">Перейти к оплате</button>
    </form>
    </div>
  </div>
  <Transition name="fade">
    <PopupWindow v-if="openWindow" @CloseWindow="openWindow = false, Scroll()" :content="content" />
  </Transition>
</template>
<script>
import HeaderСrumbsVue from '../components/HeaderСrumbs.vue';
import DepartureArrival from '../components/DepartureArrival.vue';
import PopupWindow from '../components/PopupWindow.vue';
import router from '../router'
import axios from 'axios';
import { useVuelidate } from '@vuelidate/core'
import { required, email } from '@vuelidate/validators'
import Login from '../components/Login.vue';
import Registration from '../components/Registration.vue';

export default
{
  components: { HeaderСrumbsVue, DepartureArrival, PopupWindow, Login, Registration },
  data() {
    return {
      openWindow: false,
      content: 2,
      chosenSeats: [],
      race: [],
      loadingRace: true,
      countries: [],
      formData: [],
      login: true,
    };
  },
  methods: {
    NoScroll() {
      document.body.style.overflow = 'hidden';
    },
    Scroll() {
      document.body.style.overflow = 'auto';
    },
    validateFrom(){
      this.formData.forEach(el => {
        let fields = ['name', 'surname', 'patronymic', 'birth_date', 'gender', 'citizenship', 'doc_type', 'doc_number', 'ticket_type']
        fields.forEach(field => {
          if(!el[field]){
            el.errors[field] = 'Поле обязательно для заполнения!'
          }
          else{
            el.errors[field] = ''
          }
        })
        let doc_type_name = el.doc_type.split('____')[1]
        if(el['doc_type'] && (doc_type_name == 'Паспорт гражданина РФ' 
        || doc_type_name == 'Загранпаспорт гражданина РФ' 
        || doc_type_name == 'Свидетельство о рождении РФ'
        || doc_type_name == 'Военный билет военнослужащего срочной службы') )
        {
          el.errors.doc_series = 'Поле обязательно для заполнения!'
        }
        else{
          el.errors.doc_series = ''
        }

      })
    }
  },
  async mounted(){
    console.log(this.$route.params['race_id'])
    const promise = axios
      .get('http://localhost:8000/api/race/'+this.$route.params['race_id'])
      .then(response => (
          this.race = response.data
      ));
    await promise

    const promise1 = axios
      .get('http://localhost:8000/api/countries')
      .then(response => (
          this.countries = response.data
      ));
    await promise1
    this.chosenSeats = JSON.parse(sessionStorage['chosenSeats'])
    this.chosenSeats.forEach(el => this.formData.push(
      {
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
        seat: el,
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
    this.loadingRace = false
  }
};
</script>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: all 1s ease !important;
}

.fade-enter-from,
.fade-leave-to {
  transform: scale(0)!important;
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
  justify-content: space-between;
}
.place-of-arrival
{
  display: flex;
  justify-content: space-between;
}
.block__check label
{
  font-size: 18px !important;
}
.information-race__price
{
  display: flex;
  justify-content: space-between;
}
.information-race__payment
{
  display: flex;
  justify-content: space-between;
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
.discr::before
{
  content: "";
  position: relative;
  border: 1px dashed rgb(209, 209, 209);
  left: -21px;
  top: -1px;
  transform: scaleY(1.2);
}
.container{
  max-width: 1044px;
  margin: 0 auto;
  padding: 0 20px;
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
  height: 40px;
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

@media (max-width: 768px)
{
  .form__all-input
  {
    display: block;
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
}
</style>
