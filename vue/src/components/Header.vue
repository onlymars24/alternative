<template>
    <div class="header__inner">
        <div class="logo__and__hamburger">
            <div class="header__logo">
                <a href="/" style="align-items: center;"> 
                    <img src="../img/741411.png" alt="" class="header-inner-image"> 
                    <span :class="{'black__text': blackText}">Автовокзал</span>
                </a>

            </div>  
            <div class="hamburger" @click="NoScroll(), mobileMenuOpen = true">
                <svg  height="16" viewBox="0 0 25 16" width="25" fill="#0275fe" xmlns="http://www.w3.org/2000/svg" ><path d="M0 1a1 1 0 0 1 1-1h23a1 1 0 0 1 0 2H1a1 1 0 0 1-1-1zm0 7a1 1 0 0 0 1 1h23a1 1 0 0 0 0-2H1a1 1 0 0 0-1 1zm0 7a1 1 0 0 0 1 1h23a1 1 0 0 0 0-2H1a1 1 0 0 0-1 1z" fill-rule="evenodd"></path></svg>
            </div>                      
        </div>

        <ul class="header__links">
            <li @mouseleave="$store.commit('windowHeader', 0)">
                <a @click="" @click.prevent="$store.commit('windowHeader', 1)" href="">
                    <img :src="blackText ? '/src/img/headphones.png': '/src/img/headphones-white.png'" alt="">
                    <span :class="{'black__text': blackText}">Служба поддержки</span>
                </a>
                <transition name="anim-window">
                    <nav class="header__links__window" v-show="$store.state.windowHeader == 1" @mouseenter="windowOpen = 1" @mouseleave="windowOpen = 0">
                        <a href="tel:8 (800) 700-42-12" class="header__links__window__phone-link">8 (800) 700-42-12</a>
                        <router-link to="/Faq" class="header__links__window__faq-link">
                            Вопросы и ответы
                        </router-link>
                    </nav>
                </transition>
            </li>
            <li v-if="!auth && !authForForm">
                <a href="#" @click.prevent="openWindow = true , NoScroll">
                    <img :src="blackText ? '/src/img/login_man.png': '/src/img/login_man-white.png'" alt="">
                    <span :class="{'black__text': blackText}">Авторизоваться</span>
                </a>
            </li>
            <!-- <li v-if="auth || authForForm"> -->
            <li v-if="auth || authForForm" @mouseleave="$store.commit('windowHeader', 0)">
                <a @click.prevent="$store.commit('windowHeader', 2)" href="/account">
                    <img :src="blackText ? '/src/img/login_man.png': '/src/img/login_man-white.png'" alt="">
                    <span :class="{'black__text': blackText}">Личный кабинет</span>
                </a>
                <transition name="anim-window">
                    <nav  class="header__links__window" v-show="$store.state.windowHeader == 2">
                        <a href="/account" class="header__links__window__myRace-link">Мои поездки</a>
                        <a @click.prevent="logout" href="" class="header__links__window__exit-link" >Выйти из аккаунта</a>
                    </nav>
                </transition>
            </li>
            <!-- <li v-if="auth || authForForm">
                <a @click="logout" href="#">
                    <img src="../img/login_man.png" alt="">
                    <span :class="{'black__text': blackText}">Выйти</span>
                </a>
            </li> -->
        </ul>
        <transition name="mob-menu">
            <MobailMenu @logout="logout" @OpenWindowLogin="openWindow = true, mobileMenuOpen = false" :auth="auth || authForForm" v-if="mobileMenuOpen" @closeMobMenu="Scroll(), mobileMenuOpen = false"/>
        </transition>
    </div>
    
        <PopupWindow v-if="openWindow" @CloseWindow="openWindow = false" @authenticateForForm="$emit('authenticateForForm')" @authSelf="authSelf" :content="3"/>
    
</template>

<script>
import router from '../router'
import axiosClient from '../axios'
import PopupWindow from '../components/PopupWindow.vue';
import MobailMenu from './MobailMenu.vue';
import { RouterLink } from 'vue-router';

export default
{
  components: { PopupWindow, MobailMenu },
  props: {
    blackText: {
        type: Boolean,
        default: true
    },
    authForForm: {
        type: Boolean,
        default: false
    },
  },
  emits: ['authenticateForForm'],
  data()
  {
    return{
        auth: false,
        user: [],
        openWindow: false,
        mobileMenuOpen: false, 
    }
  },
  methods: {
    logout(){
        localStorage.removeItem('authToken')
        this.auth = false
        this.$emit('disauthenticateForForm')
    },
    authSelf(){
        this.auth = true
        this.openWindow = false
        Scroll()
    },
    NoScroll() {
        document.body.style.overflow = 'hidden';
    },
    Scroll() {
        document.body.style.overflow = 'auto';
    },
  },
  watch: {
    openWindow(newQuestion, oldQuestion){
        if(newQuestion == true)
        {
            this.NoScroll()
        }
        else
        {
            this.Scroll()
        }
    }
  },
  async mounted(){
    console.log(localStorage.getItem('authToken'))
    if(localStorage.getItem('authToken')){
        this.auth = true
        this.$emit('authenticateForForm')
        const promise = axiosClient
        .get('/user')
        .then(response => {
            this.user = response.data.user
            console.log(this.user)
        })
        .catch(error => {

        })
        await promise
    }
  }
}
</script>

<style>
      .header__logo a img{
        margin-right: 5px;
      }
      .header__link
      {
          position: relative;
      }
      .header__links__window
      {
          background-color: white;
          box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
          color: black;
          border-radius: 4px;
          padding: 12px 0px;
          border: 1px solid rgb(212, 214, 218);
          font-size: 14px;
          position: absolute;
          top: 51px;
      }
      .header__links__window a
      {
          color: black ;
          padding: 8px 20px 8px 43px;
          white-space: nowrap;
      }
      .header__links__window a:hover
      {
          color: var(--blue) ;
      }
      .header__links__window__faq-link::before
      {
          content: "";
          background-image: url('../img/faq.svg');
          height: 15px;
          width: 20px;
          background-size: contain;
          background-repeat: no-repeat;
          position: absolute;
          left: 17px;
      }
      .header__links__window__phone-link::before
      {
          content: "";
          background-image: url('../img/phone.svg');
          height: 15px;
          width: 20px;
          background-size: contain;
          background-repeat: no-repeat;
          position: absolute;
          left: 17px;
      }
      .header__links__window__myRace-link::before
      {
          content: "";
          background-image: url('../img/ticket.svg');
          height: 15px;
          width: 20px;
          background-size: contain;
          background-repeat: no-repeat;
          position: absolute;
          left: 17px;
      }
      .header__links__window__exit-link::before
      {
          content: "";
          background-image: url('../img/exit.svg');
          height: 15px;
          width: 20px;
          background-size: contain;
          background-repeat: no-repeat;
          position: absolute;
          left: 17px;
      }
      .logo__and__hamburger
      {
          display: flex;
          justify-content: space-between;
          align-items: center;
      }
      .main__table-date
      {
        color: black;
      }
      
      
      
      /* animation */
      /* menu */
      .anim-window-enter-active {
        transition: all 0.3s ease-out;
      }
      
      .anim-window-leave-active {
        transition: all 1.5s cubic-bezier(.95, .05, .795, .035);
      }
      
      .anim-window-enter-from,
      .anim-window-leave-to {
        transform: translateY(20px);
        opacity: 0;
      }

      /* menu mobail */
      .mob-menu-enter-active {
        transition: all 0.5s ease-in-out;
      }
      
      .mob-menu-leave-active {
        transition: all 0.5s ease-in-out;
      }
      
      .mob-menu-enter-from,
      .mob-menu-leave-to {
        transform: translatex(100vw);
        
      }
      .black__text{
        color: black;
      }
      .header__links li a span:hover{
        text-decoration: underline;
        color: white;
      }      
      .header__links li a .black__text:hover{
        text-decoration: underline;
        color: black;
      }
      /* //black__text */





      @media (max-width: 768px) {
          .header__links{
              display: none;
          }
      }
      @media (min-width: 768px) {
          .hamburger{
              display: none;
          }
      }
</style>