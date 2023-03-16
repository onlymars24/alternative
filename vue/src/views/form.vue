<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable max-len -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/label-has-for -->
<template>
  <HeaderСrumbsVue />
  <div class="container">
    <div class="form__content">
    <div class="information-race">
      <h5>О рейсе</h5>
      <hr>
      <DepartureArrival />
      <div class="information-race__price"><p>Цена:</p><p>1050,00₽</p></div>
      <div class="information-race__payment"><p class="inf-race-price-heading">К оплате <span class="inf-race__type__ticket">(1 взрослый)</span></p><p class="total-cost" >1050,00₽</p></div>
      <hr>
      <a class="blue__link" @click="openWindow = !openWindow, NoScroll()">Условия возврата</a>
    </div>
    <form action="" class="">
      <div class="form-reg">
      <h5>Оформление билета</h5>
        <p class="form-description">Указанные данные необходимы для совершения бронирования и будут проверены при посадке в автобус.</p>
      <div class="ticket-registration">
      <div class="form__all-input">
        <div class="left-input all-input-item">
        <label for="">Фамилия</label>
        <input
                    type="text"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иванов"
                    maxlength="60"
                    required
                  />
                  <label for="">Отчество</label>
        <input
                    type="text"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иванович"
                    maxlength="60"
                    required
                  />
                  <label for="inputRegion" class="form-label">Пол</label>
                  <select
                    class="form-select form-control"
                    name="region"
                    id="inputRegion"
                    maxlength="60"
                    required
                  >
                    <option value="man">Мужчина</option>
                    <option value="woman">Женщина</option>
                  </select>
                  <label for="inputRegion" class="form-label">Документы</label>
                  <select
                    class="form-select form-control"
                    name="region"
                    id="inputRegion"
                    maxlength="60"
                    required
                  >
                    <option value="RUS">Российская Федерация</option>
                    <option value="KAZ">Казахстан</option>
                  </select>
        </div>
        <div class="right-input all-input-item">
        <label for="">Имя</label>
        <input
                    type="text"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иван"
                    maxlength="60"
                    required
                  />
                  <label for="">Дата рождения</label>
        <input
                    type="date"
                    class="form-control is-invalid"
                    name="name"
                    id="inputName"
                    placeholder="Иван"
                    maxlength="60"
                    required
                  />
                  <label for="inputRegion" class="form-label">Гражданство</label>
                  <select
                    class="form-select form-control"
                    name="region"
                    id="inputRegion"
                    maxlength="60"
                    required
                  >
                    <option value="RUS">Российская Федерация</option>
                    <option value="KAZ">Казахстан</option>
                  </select>
                  <label for="inputdoc" class="form-label">Номер документа</label>
                  <input type="text"
                    class="form-control"
                    name="doc"
                    id="inputdoc"
                    maxlength='10'
                    required
                    placeholder="серия и номер: 10 цифр"
                  >
        </div>
      </div>
      <div class="seat-bus"><h5>Место в автобусе</h5>
      <button class="seat-bus__but" type="button" @click="openWindow = !openWindow, NoScroll(), content=1">Место: 32</button>
      </div>
      </div>
      </div>
      <div class="form-reg">
      <div class="information-buyer">
        <h5>Оформление билета</h5>
        <p class="form-description">Указанные данные необходимы для совершения бронирования и будут проверены при посадке в автобус.</p>
        <div class="inf-buyer__input">
          <label class="inf-buyer__input-item" for="">E-mail
            <input type="email"
            class="form-control"
                      name="tel"
                      id="inputTel"
                      placeholder="name@mail.ru"
                      required
            >
          </label>
          <label class="inf-buyer__input-item" for="">Телефон
            <input
                      type="tel"
                      class="form-control"
                      name="tel"
                      id="inputTel"
                      placeholder="+7 (999) 999 9999"
                      required
                    />
          </label>
        </div>
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

export default
{
  components: { HeaderСrumbsVue, DepartureArrival, PopupWindow },
  data() {
    return {
      openWindow: false,
      content: 2,

    };
  },
  methods: {
    NoScroll() {
      document.body.style.overflow = 'hidden';
    },
    Scroll() {
      document.body.style.overflow = 'auto';
    },
  },

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
