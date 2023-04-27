<!-- eslint-disable vuejs-accessibility/label-has-for -->
<!-- eslint-disable max-len -->
<template>
  <div class="container">
    <div class="login-container">
      <div class="login-block">
        <a class="go-back" href="/">&#8592; Вернуться назад</a>
        <Login v-if="login" @log="login = false" />
        <Registration v-else @log="login = true" />
      </div>
    </div>
  </div>
  <div class="decor">
    <span class="bus-stop-decor"></span><span class="ticket-decor"></span>
  </div>
</template>
<script>
import Login from "../components/Login.vue";
import Registration from "../components/Registration.vue";

export default {
  components: { Login, Registration },
  data() {
    return {
      login: true,
    };
  },
};
</script>
<style>
.bus-stop-decor {
  background-image: url("/public/img/bus-stop.svg");
  background-position: left bottom;
  background-size: contain;
  background-repeat: no-repeat;
  height: 100%;
  width: 50%;
  margin-left: 160px;
  transform: scale(1.2);
}
.ticket-decor {
  background-image: url("/public/img/ticket.svg");
  background-position: right bottom;
  background-size: contain;
  background-repeat: no-repeat;
  height: 100%;
  width: 50%;
  margin-right: 140px;
}
.decor {
  display: flex;
  position: absolute;
  opacity: 0.1;
  top: 65vh;
  width: 100vw;
  height: 30vh;
}
.btn__new-code {
  background-color: transparent;
}
.code__tel {
  font-size: 14px;
}
.login-head {
  display: flex;
}
.head-link {
  color: var(--blue);
  margin-top: 12px;
  cursor: pointer;
}
.form-label {
  margin-bottom: 0px;
}
.login-block {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  max-width: 800px;
  width: 100%;
  padding: 10px;
  z-index: 10;
  border-radius: 10px;
  padding: 30px 50px 50px 50px;
  box-shadow: rgb(0 0 0 / 10%) 0px 20px 90px 0px;
}
.approval {
  font-size: 14px;
}
.possible {
  color: var(--gray);
  margin: 0px;
}
.possible-item {
  position: relative;
  padding-left: 22px;
  margin: 0;
}

.go-back {
  position: relative;
  top: -60px;
  right: 40px;
}
.btn-code {
  width: 100%;
  padding: 12px;
}
.possible-item::before {
  content: "";
  background-image: url("/public/img/check.svg");
  height: 20px;
  width: 20px;
  position: absolute;
  background-position: center center;
  display: block;
  background-size: contain;
  left: 0px;
}
@media (max-width: 1000px) {
  .decor {
    display: none;
  }
}
@media (max-width: 768px) {
  .possible-block {
    font-size: 13px;
  }
}
@media (max-width: 580px) {
  .row-mobil {
    flex-direction: column-reverse;
  }
}
</style>
