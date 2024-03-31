<!-- eslint-disable vuejs-accessibility/label-has-for -->
<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
    <div><div class="container"><Header/></div></div>
    <div class="header-menu__personal-account">
      <!--    -->
      <div class="container">
        
        <ul class="header-menu__all">
            <li class="header-menu__item" :class="{'header-menu__item-active': activeTab == 'UpcomingTrips'}" @click="activeTab = 'UpcomingTrips'" >Список заказов</li>
            <li class="header-menu__item" :class="{'header-menu__item-active': activeTab == 'ContactInformation'}" @click="activeTab = 'ContactInformation'">Контактная информация</li>
            <li class="header-menu__item" :class="{'header-menu__item-active': activeTab == 'Person'}" @click="activeTab = 'Person'">Пассажиры</li>
            
        </ul>
        
        </div>
    </div>
    <div class="container">
        <!-- <div style="width: 40%; padding: 15px; margin-bottom: 15px;" class="menu__ticket">Мои бонусы: 0.00 руб</div> -->
        <Transition  name="slide-pers" mode="out-in">
            <component :is="activeTab"></component>
        </Transition>
    </div>
    <template v-if="popupEmail">
        <PopupWindow @CloseWindow="popupEmail = false;" :content="9" @editEmail="editEmail"/>
	  </template>
</template>
<script>
import TravelHistory from '../components/TravelHistory.vue';
import UpcomingTrips from '../components/UpcomingTrips.vue';
import ContactInformation from '../components/ContactInformation.vue';
import Person from '../components/PersonSave.vue';
import Header from '../components/Header.vue'
import PopupWindow from '../components/PopupWindow.vue';
import axiosClient from '../axios';

export default
{
  components: { TravelHistory, UpcomingTrips, ContactInformation, Person, Header, PopupWindow },
  data() {
    return {
      activeTab: 'UpcomingTrips',
      popupEmail: false,
      user: {}
    };
  },
  methods: {
    async editEmail(email){
      const promise = axiosClient
        .post('/edit/email', {email: email})
        .then(response => {
          console.log(response)
        })
      await promise
    }
  },
  async mounted(){
      const promise = axiosClient
      .get('/user')
      .then(response => {
          this.user = response.data.user
      })
      await promise
      console.log(this.user.email)
      if(!this.user.email){
        this.popupEmail = true
      }
  }

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
.btn-secondary
{
  margin-right:20px;
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
@media (max-width:542px){
  .person__all-button
  {
    display: flex;
    margin-top: 30px;
  }
  .btn-secondary
  {
    margin: 0px;
    margin-right:20px;
  }
}
</style>
