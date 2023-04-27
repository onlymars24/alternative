<template>
  <div class="mobail__container">
    <div class="mobail__header">
      <div class="mobail-menu__logo">
        <div class="header__logo">
          <a href="/"
            ><img src="/src/img/741411.png" alt="" class="header-inner-image" />
            <span class="logo-txt-mob">Автовокзал</span>
          </a>
        </div>
      </div>
      <button class="mobail-menu__exit" @click="this.$emit('closeMobMenu')">
        <svg
          width="12"
          height="12"
          viewBox="0 0 12 12"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
          class="Style__CrossImg-d8v3q6-5 ffAWor"
        >
          <path
            d="M6 5.107L10.922.185a.632.632 0 0 1 .893.893L6.893 6l4.922 4.922a.631.631 0 1 1-.893.893L6 6.893l-4.922 4.922a.631.631 0 1 1-.893-.893L5.107 6 .185 1.078a.632.632 0 1 1 .893-.893L6 5.107z"
            fill="#F9253F"
          ></path>
        </svg>
      </button>
    </div>
    <hr>
    <nav>
      <ul class="mobail-menu__links">
        <li class="mobail-menu__link mobail-menu__search">
          <router-link to="">Поиск билетов</router-link>
        </li>

        <div class="accordion">
          <input
            class="input-accord"
            type="checkbox"
            name="radio-a"
            id="phoneLink"
          />
          <label class="accordion-label mobail-menu__support" for="phoneLink"
            >Служба поддержки</label
          >
          <div class="accordion-content mobail-accordion-content">
            <p class="mobil_phone_link">
              <a href="tel:8 (800) 700-42-12">8 (800) 700-42-12</a>
            </p>
          </div>
        </div>

        <li v-if="auth" class="mobail-menu__link mobail-menu__ticket">
          <router-link to="/account">Мои поездки</router-link>
        </li>
      </ul>
    </nav>
    <hr />
    <nav>
      <ul class="mobail-menu__links2">
        <li><router-link to="/Faq">Вопросы и ответы</router-link></li>
        <li><router-link to="">Сотрудничество</router-link></li>
        <li><router-link to="">О нас</router-link></li>
        <li><router-link to="">Блог</router-link></li>
      </ul>
    </nav>
    <hr />
    <div v-if="auth" class="mobil__exit__account__block">
      <a class="mobil__exit__account" href="" @click.prevent="$emit('logout')">Выйти из аккаунта</a>
    </div>
    <div v-if="!auth" class="mobil__exit__account__block">
      <a class="mobil__exit__account" href="/login">Авторизоваться</a>
    </div>
  </div>
</template>
<script>
export default {
  props: ['auth'],
  emits: ['logout'],
  data() {
    return {};
  },
};
</script>
<style>
hr{
    color: gray;
}
.mobail__container {
  background-color: white;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 20;
  height: 100vh;
  width: 100vw;
  overflow: auto;
}

.mobail__header {
  padding: 0px 10px;
  display: flex;
  justify-content: space-between;
}
.mobail-menu__exit {
  background-color: transparent;
  padding: 20px;
}
.logo-txt-mob {
  color: black;
}
.mobail-menu__links {
  font-size: 18px;
  font-weight: bold;
  padding: 0px 10px;
}
.mobail-menu__links a {
  color: black;
  font-weight: 500;
}

.mobail-menu__link {
  padding-left: 40px;
  position: relative;
}
.mobail-menu__link:not(:last-child) {
  margin-bottom: 20px;
}
.mobil_phone_link {
  top: 0px;
  padding-left: 60px;
  margin-top: 10px;
}
.mobil_phone_link a {
    font-size: 18px;
    font-weight: 300;
}
.mobil_phone_link::before {
  content: "";
  background-image: url("../img/phone.svg");
  height: 17px;
  width: 20px;
  background-size: contain;
  background-repeat: no-repeat;
  position: absolute;
  left: 30px;
  top: 2px;
}
.mobail-menu__ticket::before {
  content: "";
  background-image: url("../img/ticket.svg");
  height: 17px;
  width: 20px;
  background-size: contain;
  background-repeat: no-repeat;
  position: absolute;
  left: 7px;
  top: 5px;
}
.mobail-menu__support {
  padding-left: 40px !important;
  font-size: 18px !important;
  font-weight: 500 !important;
  position: relative;
}
.mobail-menu__support::before {
  content: "";
  background-image: url("../img/headphones.png");
  height: 17px;
  width: 20px;
  background-size: contain;
  background-repeat: no-repeat;
  position: absolute;
  left: 5px;
  top: 22px;
}
.mobail-menu__search::before {
  content: "";
  background-image: url("../img/lupa.svg");
  height: 17px;
  width: 20px;
  background-size: contain;
  background-repeat: no-repeat;
  position: absolute;
  left: 5px;
  top: 5px;
}
.mobail-menu__links2 {
  padding: 0px 10px;
}
.mobail-menu__links2 a {
  color: black;
  margin-bottom: 20px;
  padding-left: 5px;
}
.mobail-accordion-content {
  position: relative;
  padding: 0px !important;
}
.mobil__exit__account {
  position: relative;
}

.mobil__exit__account::before {
  content: "";
  background-image: url("../img/exit.svg");
  height: 17px;
  width: 20px;
  background-size: contain;
  background-repeat: no-repeat;
  position: absolute;
  left: 19px;
  top: 5px;
}
.mobil__exit__account__block a {
  color: black;
  padding-left: 50px;
  font-size: 18px;
  font-weight: 500;
  margin-top: 30px;
}
</style>
