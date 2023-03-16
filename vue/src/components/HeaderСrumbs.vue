<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable max-len -->
<template>
  <div class="line-head">
    <div class="container">
      <div class="race-progress">
        <div class="passed">Выбор рейса <span class="arrow"></span></div>
        <div class="not-passed">Пассажиры <span class="arrow"></span></div>
        <div class="not-passed">Оплата</div>
      </div>
      <div class="race__info-head"></div>
    </div>
  </div>
  <div class="line-head short-description-block">
    <hr class="hr-no-m" />
    <div class="container">
      <Transition name="animation">

      <div class="more-detail-block" v-if="openMoreDetail">
        <div class="more-detail-block-left">
          <p>
            Рейс № <strong>2847 Саларьево АВ - Воронеж</strong>, по маршруту
            <strong>Москва - Воронеж, на 12 марта 2023</strong> года в <strong>21:30 </strong>
          </p>
          <p>Тип рейса: Регулярный</p>
          <p>Отправление и прибытие по местному времени</p>
            <DepartureArrival />
        </div>
        <div class="more-detail-block-right">
          <p><strong>Дополнительно</strong><br />Для посадки необходим паспорт</p>
          <br>
          <strong>Перевозчик</strong>
          <p>Бренд:ИП Дробышев Владимир Викторович <br>
          Автобус: 46 мест<br>
          Перевозчик: ИП Дробышев Владимир Викторович <br>
          Адрес: Россия, Воронежская обл., г. Воронеж, ул. Вл. Невского 81, кв. кв. 121 <br>
          ОГРН: 304366221500112 <br>
          Время работы: Пн-Пт 10:00-17:00 (по местному времени)
          </p>
        </div>
      </div>

      </Transition >
      <div class="content-line">
        <div class="more-detail__short-inf">
          <div class="cities-head">
            <div>Москва</div>
            -
            <div>Воронеж</div>
          </div>
          <div class="short-description">12 марта в 2015, 1 взрослый, 1100</div>
        </div>
        <div class="more-detail" :class="{rotate: openMoreDetail}">
          <span class="blue__link"  @click="this.openMoreDetail = !this.openMoreDetail"
            >Показать подробности</span
          >
          <span class="arrow arrow-more" :class="{'arrow-more-rotate': openMoreDetail}"></span>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DepartureArrival from '../components/DepartureArrival.vue';

export default {
  components: { DepartureArrival },
  data() {
    return {
      openMoreDetail: false,
    };
  },
};
</script>
<style>
.animation-enter-active,
.animation-leave-active {
  height: 400px !important;
  transition: all 1s ease !important;
  overflow: hidden !important;
}

.animation-enter-from,
.animation-leave-to {
  height: 0px !important;
}

.line-head {
  background-color: white;
  width: 100vw;
}
.short-description-block {
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
  margin-bottom: 20px;
}
.container {
  max-width: 1044px;
  margin: 0 auto;
  padding: 0 20px;
}
.more-detail-block {
  display: flex;
  justify-content: space-between;
  overflow: auto;
  height: 400px

}
.more-detail-block-left
{
  max-width: 60%;
}
.more-detail-block-right
{
  margin-left: 30px;
}
.race-progress {
  display: flex;
}
.not-passed {
  color: rgb(145, 145, 145);
  padding: 10px;
  font-size: 16px;
  position: relative;
}
.passed {
  padding: 10px;
  padding-left: 0px;
  font-size: 16px;
  position: relative;
  color: black;
  font-weight: 700;
  display: flex;
  flex-direction: column;
  padding-bottom: 12px;
  border-bottom: 3px solid #2196f3;
}
.short-description {
  color: rgb(179, 179, 179);
  font-size: 12px;
  padding-bottom: 10px;
}
.content-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.cities-head {
  display: flex;
  margin-top: 10px;
}
.arrow {
  display: block;
  background-image: url("/public/img/arrow-open-right-svgrepo-com.svg");
  width: 10px;
  height: 10px;
  background-position: center center;
  background-size: contain;
  background-repeat: no-repeat;
  right: -8px;
  top: 17px;
  position: absolute;
}
.arrow-more
{
  transform: rotate(90deg);
  top: 8px;
  right: 5px;
  transition: transform 0.5s ease;
}
.arrow-more-rotate
{
  transform: rotate(-90deg);
}
.hr-no-m {
  margin: 0px;
  color: rgba(0, 0, 0, 0.11);
}
.more-detail {
  position: relative;
  padding-right: 20px;
}
@media (max-width: 500px)
{
  .more-detail-block
  {
    display: block;
  }
}
</style>
