<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable max-len -->
<template>
  <div class="line-head">
    <div class="container">
      <div class="race-progress">
        <div class="passed">Выбор места <span class="arrow"></span></div>
        <div :class="{'not-passed': step == 'first', 'passed': step == 'second'}">Оформление <span class="arrow"></span></div>
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
            Рейс № <strong>{{race.race.num}} {{race.race.name}}</strong>,
            <strong> на {{dispatchDay}} {{dispatchTime}}</strong>
          </p>
          <p>Тип рейса: {{race.race.type.name}}</p>
          <p v-if="race.race.type.clazz">Класс рейса: {{race.race.type.clazz}}</p>
        </div>
        <div class="more-detail-block-right">
          <p v-if="race.race.description"><strong>Дополнительно</strong><br/>{{race.race.description}}</p>
          <br v-if="race.race.description">
          <strong>Перевозчик</strong>
          <p>Организация перевозчика: {{race.race.carrier}}</p>
          <p>ИНН организации перевозчика: {{race.race.carrierInn}}</p>
          <p v-if="race.race.carrierPhone">Контактнй телефон: {{ race.race.carrierPhone }}</p>
        </div>
      </div>

      </Transition >
      <div class="content-line">
        <div class="more-detail__short-inf">
          <div class="cities-head">
            <div>{{race.race.name}}</div>
          </div>
          <div class="short-description">{{dispatchDay}} {{dispatchTime}}</div>
        </div>
        <div class="more-detail" :class="{rotate: openMoreDetail}">
          <span class="blue__link"  @click="this.openMoreDetail = !this.openMoreDetail"
            ><span class="more-detail-short-discr">Показать</span> подробности</span
          >
          <span class="arrow arrow-more" :class="{'arrow-more-rotate': openMoreDetail}"></span>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DepartureArrival from '../components/DepartureArrival.vue';
import dayjs from 'dayjs'

export default {
  components: { DepartureArrival },
  props: ['race', 'step'],
  data() {
    return {
      openMoreDetail: false,
      months: [
                '', 'янв.', 'февр.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сент.', 'окт.', 'ноябр.', 'дек.', 
            ],
      dispatchDate: '',
      dispatchTime: ''
    };
  },
  mounted(){
    this.dispatchTime = dayjs(this.race.race.dispatchDate).format('HH:mm')
    this.dispatchDay = dayjs(this.race.race.dispatchDate).format('D')+' '+this.months[dayjs(this.race.race.dispatchDate).format('M')]
  }
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
.more-detail-block {
  display: flex;
  justify-content: space-between;
  overflow: auto;
  height: 400px;
  padding-top: 20px;
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
  align-items: center;
}
.not-passed {
  color: rgb(145, 145, 145);
  padding: 10px;
  font-size: 14px;
  position: relative;
  text-align: center;
}
.passed {
  padding: 10px;
  /* padding-left: 0px; */
  font-size: 15px;
  position: relative;
  color: black;
  font-weight: 700;
  display: flex;
  flex-direction: column;
  /* padding-bottom: 12px; */
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
  .more-detail-short-discr
  {
    display: none;
  }
  .more-detail-block-right
  {
    margin: 0;
  }
  .more-detail-block-left
  {
    margin: 0;
  }
}
</style>
