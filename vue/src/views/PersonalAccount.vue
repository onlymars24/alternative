<!-- eslint-disable vuejs-accessibility/label-has-for -->
<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
    <div><div class="container"><Header/></div></div>
    <div class="header-menu__personal-account">
        <div class="container">
        
        <ul class="header-menu__all">
            <li class="header-menu__item" :class="{'header-menu__item-active': activeTab == 'UpcomingTrips'}" @click="activeTab = 'UpcomingTrips'" >Список заказов</li>
            <li class="header-menu__item" :class="{'header-menu__item-active': activeTab == 'ContactInformation'}" @click="activeTab = 'ContactInformation'">Контактная информация</li>
            <li class="header-menu__item" :class="{'header-menu__item-active': activeTab == 'Person'}" @click="activeTab = 'Person'">Пассажиры</li>
        </ul>
        </div>
    </div>
    <div class="container">
        <Transition  name="slide-pers" mode="out-in">
            <component :is="activeTab"></component>
        </Transition>
    </div>
</template>
<script>
import TravelHistory from '../components/TravelHistory.vue';
import UpcomingTrips from '../components/UpcomingTrips.vue';
import ContactInformation from '../components/ContactInformation.vue';
import Person from '../components/PersonSave.vue';
import Header from '../components/Header.vue'

export default
{
  components: { TravelHistory, UpcomingTrips, ContactInformation, Person, Header },
  data() {
    return {
      activeTab: 'UpcomingTrips',
    };
  },

};
</script>
<style>
.contact-information__block
{
  background-color: #fff;
  padding: 35px;
  border-radius: 10px;
  margin-bottom: 25px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
  height: 100%;
}
.personal-account__content-empty
{
    text-align: center;
    padding: 30px;
}
.header-menu__personal-account
{
    box-shadow: rgb(0 0 0 / 15%) 0px 2px 4px;
    height: 60px;
    margin-bottom: 30px;
}
.header-menu__item-active
{
    border-color:var(--blue) !important;
    color: black;
}
.header-menu__all
{
    display: flex;
    height: 100%;
    align-items: center;
    font-weight: 500;
    color: var(--gray);
    overflow: auto;
}
.header-menu__all::-webkit-scrollbar {
  width: 0;
}
.header-menu__item
{
    padding: 17px 10px 17px 10px;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    white-space: nowrap;
}
.header-menu__item:nth-child(1)
{
    padding-left: 0px;
}
.header-menu__item:hover
{
    border-color:var(--blue);
}

.slide-pers-enter-active,
.slide-pers-leave-active {
  transition: all 0.5s ease;
}

.slide-pers-enter-from
{
    transform: translateX(-100%);
    opacity: 0;
}
.slide-pers-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
