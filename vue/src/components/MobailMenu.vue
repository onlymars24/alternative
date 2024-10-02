<template>
  <div class="mobail__container">
    <div class="mobail__header">
      <div class="mobail-menu__logo">
        <div class="header__logo">
          <a href="/">
            <!-- <img src="/src/img/741411.png" alt="" class="header-inner-image" /> -->
            <svg style="width: 30px; margin-right: 5px;" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#2196F3" class="bi bi-geo-alt">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier"> 
                    <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"></path> 
                    <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path> 
                </g>
            </svg>     
            <span class="logo-txt-mob">Росвокзалы.рф</span>
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
          <a href="/">Поиск билетов</a>
        </li>

        <!-- <div class="accordion">
          <input
            class="input-accord"
            type="checkbox"
            name="radio-a"
            id="phoneLink"
          />
          <label class="mobail-menu__support" for="phoneLink"
            >Служба поддержки</label
          >
          <div class="accordion-content mobail-accordion-content">
            <p class="mobil_phone_link">
              <a href="tel:8 (800) 700-42-12">8 (800) 700-42-12</a>
            </p>
          </div>
        </div> -->

        <li v-if="auth" class="mobail-menu__link mobail-menu__ticket">
          <a href="/account">Мои поездки</a>
        </li>
      </ul>
    </nav>
    <hr />
    <nav>
      <ul class="mobail-menu__links2">
        <li><a href="/faq" target="_blank">Вопросы и ответы</a></li>
        <li><a @click.prevent="this.$emit('OpenWindowFeedback')" target="_blank">Задать вопрос</a></li>
      </ul>
    </nav>
    <hr />
    <div v-if="auth" class="mobil__exit__account__block">
      <a class="mobil__exit__account" href="" @click.prevent="$emit('logout'), this.$emit('closeMobMenu')">Выйти из аккаунта</a>
    </div>
    <div v-if="!auth" class="mobil__exit__account__block">
      <a class="mobil__exit__account" @click.prevent="this.$emit('OpenWindowLogin')" href="/login">Авторизоваться</a>
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
  position: fixed;
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
