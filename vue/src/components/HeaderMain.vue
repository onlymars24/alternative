<template>
    <div class="main__header">
            <div class="container">
                <div class="header__inner">
                    <div class="logo__and__hamburger">
                        <div class="header__logo">
                            <a href="/"> <img src="../img/741411.png" alt="" class="header-inner-image"> <span>Автовокзал</span> </a>
                        </div>
                        <div class="hamburger" @click="NoScroll(), mobileMenuOpen = true">
                            <svg  height="16" viewBox="0 0 25 16" width="25" fill="#0275fe" xmlns="http://www.w3.org/2000/svg" ><path d="M0 1a1 1 0 0 1 1-1h23a1 1 0 0 1 0 2H1a1 1 0 0 1-1-1zm0 7a1 1 0 0 0 1 1h23a1 1 0 0 0 0-2H1a1 1 0 0 0-1 1zm0 7a1 1 0 0 0 1 1h23a1 1 0 0 0 0-2H1a1 1 0 0 0-1 1z" fill-rule="evenodd"></path></svg>
                        </div>
                    </div>
                    <ul class="header__links" >
                        <li class="header__link">
                            <a href="" @click="" @click.prevent="windowOpen = 1">
                                <img src="../img/headphones.png" alt="">
                                <span>Служба поддержки</span>
                            </a>
                            <transition name="anim-window">
                                 <nav class="header__links__window" v-show="windowOpen == 1" @mouseenter="windowOpen = 1" @mouseleave="windowOpen = 0">
                                    <a href="tel:8 (800) 700-42-12" class="header__links__window__phone-link">8 (800) 700-42-12</a>
                                    <router-link to="/Faq" class="header__links__window__faq-link">
                                       Вопросы и ответы
                                    </router-link>
                                </nav>
                            </transition>
                        </li>
                        <li class="header__link" >
                            <a href="" @click.prevent="windowOpen = 2" >
                                <img src="../img/login_man.png" alt="">
                                <span>Личный кабинет</span>
                            </a>
                            <transition name="anim-window">
                                <nav @mouseenter="windowOpen = 2" @mouseleave="windowOpen = 0" class="header__links__window" v-show="windowOpen == 2">
                                    <router-link to="/account" class="header__links__window__myRace-link">Мои поездки</router-link>
                                    <router-link to="" class="header__links__window__exit-link" >выйти из аккаунта</router-link>
                                </nav>
                            </transition>
                        </li>
                    </ul>
                    <transition name="mob-menu">
                        <MobailMenu v-if="mobileMenuOpen" @closeMobMenu="Scroll(), mobileMenuOpen = false"/>
                    </transition>
                </div>
                <div class="main">
                    <h1 class="main__main">
                        Автовокзал Санкт-Петербурга
                    </h1>
                    <p class="main__main-p">
                        Билеты на автобус онлайн
                    </p>
                </div>
                
                <form @submit.prevent="findRaces" class="main__table">
                    <div class="main__table-table">
                        <ul v-if="dispatchPointEmpty" class="hint">
                            <li class="hint__title">
                                <span>Популярные направления:</span>
                            </li>
                            <li @mousedown="fillDispatch" data-id="73707" data-name="Псков">
                                <strong data-id="73707" data-name="Псков">Псков, </strong>
                                <span data-id="73707" data-name="Псков">Псковская обл,</span>
                            </li>
                        </ul>
                        <ul v-if="dispatchPointFilled" class="hint">
                            <template v-for="el in filteredDispatchPoints" :key="el.id">
                                <li @mousedown="fillDispatch" :data-id="el.id" :data-name="el.name">
                                    <strong :data-id="el.id" :data-name="el.name">{{el.name}}, </strong>
                                    <span :data-id="el.id" :data-name="el.name">{{el.region}}, {{el.details}}</span>
                                </li>
                            </template>
                        </ul>
                        <p class="">Откуда</p>
                        <input :data-id="dispatchEl.id" :data-name="dispatchEl.name" @focus="dispatchFocus" @blur="dispatchBlur" v-model="dispatchText" class="main__table-input-1" type="text">
                    </div>
                    <div class="main__table-table">
                        <ul v-if="arrivalPointEmpty" class="hint">
                            <li class="hint__title">
                                <span>Популярные направления:</span>
                            </li>
                            <li @mousedown="fillArrival" data-id="1770608" data-name="58-й километр (Орд. шоссе)">
                                <strong data-id="1770608" data-name="58-й километр (Орд. шоссе)">58-й километр (Орд. шоссе), </strong>
                                <span data-id="1770608" data-name="58-й километр (Орд. шоссе)">Ордынский район, от Новосибирск ЖД</span>
                            </li>
                        </ul>
                        <ul v-if="arrivalPointFilled" class="hint">
                            <template v-for="el in filteredArrivalPoints" :key="el.id">
                                <li @mousedown="fillArrival" :data-id="el.id" :data-name="el.name">
                                    <strong :data-id="el.id" :data-name="el.name">{{el.name}}, </strong>
                                    <span :data-id="el.id" :data-name="el.name">{{el.region}}, {{el.details}}</span>
                                </li>
                            </template>
                        </ul>
                        <p class="">Куда</p>
                        <input @click="arrivalFocus" @blur="arrivalBlur" v-model="arrivalText" :disabled="arrivalPointDisabled" class="main__table-input" list="OWNER" :placeholder="arrivalPointDisabled ? 'Укажите Откуда' : ''" type="text">
                    </div>
                    <div class="main__table-table">
                        <p class="">Дата поездки</p>
                        <input class="main__table-date" type="date" :min="dateNew" :max="toMonth"  v-model="date">
                        <!-- <p class="" style="position: absolute;
                            bottom: 0;
                            font-size: 17px;
                            color: black;
                            background: white;
                            padding: 10px;
                            padding-left: 0;
                            width: 95px;
                            height: 20px;">
                            {{date}}
                        </p> -->
                    </div>
                    <!-- <div class="main__table-table">
                        <p class="">Пассажиры</p>
                        <input class="main__table-input" type="text">
                    </div> -->
                    <div class="main__table-button">
                        <button type="submit" class="main__button" :disabled="disabledButton">
                            Найти билет
                        </button>
                    </div>
                </form>
                <div class="main__another__date">
                    <!-- <router-link class="main__another__date-fix" v-if="tommorow" :to="{name: 'Races', params: {dispatch_id: dispatchEl0.id, dispatch_name: dispatchEl0.name, arrival_id: arrivalEl0.id, arrival_name: arrivalEl0.name, date: tommorow}}">Завтра</router-link> -->
                    <!-- <router-link class="main__another__date-fix" v-if="tommorow" :to="{name: 'Races', params: {dispatch_id: 12, dispatch_name: 'Москва', arrival_id: 87, arrival_name: 'jkklasdf', date: '1234'}}">Завтра</router-link> -->
                </div>
            </div>
        </div>     
    </template>
    
    <script>
    import MobailMenu from './MobailMenu.vue';
    import { RouterLink } from 'vue-router';
    import router from '../router'
    import axios, {isCancel, AxiosError} from 'axios';
    export default{
        components: { RouterLink, MobailMenu },
        props: {
            dispatchEl0: {
                type: Object,
                default: function () {
                    return {
                        id: null,
                        name: null
                    };
                }
            },
            arrivalEl0: {
                type: Object,
                default: function () {
                    return {
                        id: null,
                        name: null
                    };
                }
            },
            date0: {
                type: String,
                default: ""
            },
            // tommorow: {
            //     type: String,
            //     default: ''
            // },
            // afterTommorow: {
            //     type: String,
            //     default: ''
            // },
        },
        emits: ["changeRaces"],
        data() {
            return {
                    date: this.date0,
                    dispatchPointEmpty: false,
                    dispatchPointFilled: false,
                    dispatchText: this.dispatchEl0.name,
                    arrivalPointEmpty: false,
                    arrivalPointFilled: false,
                    arrivalText: this.arrivalEl0.name,
                    // arrivalPointDisabled: true,
                    dispatchData: [
                    // {
                    //     id: 80153,
                    //     name: "Новосибирск",
                    //     region: "Новосибирская обл",
                    //     details: null,
                    //     address: null,
                    //     latitude: null,
                    //     longitude: null,
                    //     okato: "50401000000",
                    //     place: true
                    // },
                    // {
                    //     id: 1774915,
                    //     name: "Энск",
                    //     region: "Новосибирская обл",
                    //     details: "Новосибирск г",
                    //     address: null,
                    //     latitude: null,
                    //     longitude: null,
                    //     okato: "",
                    //     place: true
                    // },
                    // {
                    //     id: 73707,
                    //     name: "Псков",
                    //     region: "Псковская обл",
                    //     details: null,
                    //     address: null,
                    //     latitude: null,
                    //     longitude: null,
                    //     okato: "58401000000",
                    //     place: true
                    // }
                    ],
                    arrivalData: [
                    // {
                    //     id: 1770608,
                    //     name: "58-й километр (Орд. шоссе)",
                    //     region: "Ордынский район",
                    //     details: "от Новосибирск ЖД",
                    //     address: null,
                    //     latitude: null,
                    //     longitude: null,
                    //     okato: null,
                    //     place: false
                    // },
                    // {
                    //     id: 1770915,
                    //     name: "58-й километр (Орд. шоссе)",
                    //     region: "Ордынский район",
                    //     details: "от Новосибирск ЮЗ",
                    //     address: null,
                    //     latitude: null,
                    //     longitude: null,
                    //     okato: null,
                    //     place: false
                    // },
                    // {
                    //     id: 1770610,
                    //     name: "Автосервис (Тальменка)",
                    //     region: "Алтайский край",
                    //     details: "от Новосибирск ЖД",
                    //     address: null,
                    //     latitude: null,
                    //     longitude: null,
                    //     okato: null,
                    //     place: false
                    // }
                    ],
                    dispatchEl: this.dispatchEl0,
                    arrivalEl: this.arrivalEl0,
                    windowOpen: 0,
                    mobileMenuOpen: false, 
                    dateNew: "",
                    toMonth: "",
                };
        },
        methods: {
            
            NoScroll() {
                document.body.style.overflow = 'hidden';
            },
            Scroll() {
                document.body.style.overflow = 'auto';
            },
            dispatchFocus() {
                if (!this.dispatchText) {
                    this.dispatchPointEmpty = true;
                    this.dispatchPointFilled = false;
                }
                else {
                    this.dispatchPointEmpty = false;
                    this.dispatchPointFilled = true;
                }
            },
            dispatchBlur() {
                this.dispatchPointEmpty = this.dispatchPointFilled = this.arrivalPointEmpty = this.arrivalPointFilled = false;
                if (this.dispatchText != this.dispatchEl.name && this.dispatchEl.name) {
                    this.dispatchText = this.dispatchEl.name;
                }
            },
            async fillDispatch(event) {
                this.dispatchEl.id = event.target.dataset.id;
                this.dispatchEl.name = event.target.dataset.name;
                this.dispatchText = this.dispatchEl.name;
                this.arrivalEl.id = null;
                this.arrivalEl.name = null;
                this.arrivalText = "";
                this.arrivalBlur();
                this.getArrivalData();
            },
            async getArrivalData() {
                const promise = axios
                    .get("http://alternative/api/arrival_points/" + this.dispatchEl.id)
                    .then(response => (this.arrivalData = JSON.parse(response.data)));
                await promise;
            },
            arrivalFocus() {
                if (!this.arrivalText) {
                    this.arrivalPointEmpty = true;
                    this.arrivalPointFilled = false;
                }
                else {
                    this.arrivalPointEmpty = false;
                    this.arrivalPointFilled = true;
                }
            },
            arrivalBlur() {
                this.arrivalPointEmpty = this.arrivalPointFilled = this.arrivalPointEmpty = this.arrivalPointFilled = false;
                if (this.arrivalText != this.arrivalEl.name && this.arrivalEl.name) {
                    this.arrivalText = this.arrivalEl.name;
                }
            },
            fillArrival(event) {
                this.arrivalEl.id = event.target.dataset.id;
                this.arrivalEl.name = event.target.dataset.name;
                this.arrivalText = this.arrivalEl.name;
            },
            findRaces() {
                this.$emit("changeRaces", this.date, this.dispatchEl.id, this.arrivalEl.id);
                router.push({ name: "Races", params: { dispatch_id: this.dispatchEl.id, dispatch_name: this.dispatchEl.name, arrival_id: this.arrivalEl.id, arrival_name: this.arrivalEl.name, date: this.date } });
            },
            testFunction() {
                console.log("helloWorld!");
            }
        },
        watch: {
            dispatchText(newDispatchText, oldDispatchText) {
                if (!newDispatchText) {
                    this.dispatchPointEmpty = true;
                    this.dispatchPointFilled = false;
                }
                else {
                    this.dispatchPointEmpty = false;
                    this.dispatchPointFilled = true;
                }
                if (this.dispatchText == this.dispatchEl.name) {
                    this.dispatchBlur();
                }
            },
            arrivalText(newArrivalText, oldArrivalText) {
                if (!newArrivalText) {
                    this.arrivalPointEmpty = true;
                    this.arrivalPointFilled = false;
                }
                else {
                    this.arrivalPointEmpty = false;
                    this.arrivalPointFilled = true;
                }
                if (this.arrivalText == this.arrivalEl.name) {
                    this.arrivalBlur();
                }
            },
            // date(newDate){
            //     console.log(newDate)
            //     router.push({ name: 'Races', params: { dispatch_id: '123', arrival_id: 'tsdfg', date: '12341234' } })
            // }
        },
        computed: {
            filteredDispatchPoints() {
                return this.dispatchData.filter(el => {
                    return el.name && el.name.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
                        || el.region && el.region.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
                        || el.details && el.details.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1;
                });
            },
            filteredArrivalPoints() {
                return this.arrivalData.filter(el => {
                    return el.name && el.name.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
                        || el.region && el.region.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
                        || el.details && el.details.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1;
                });
            },
            disabledButton() {
                return !this.dispatchEl.id || !this.arrivalEl.id || !this.date;
            },
            arrivalPointDisabled() {
                return !this.dispatchEl.name && !this.dispatchEl.id;
            }
        },
        mounted() {
            axios
                .get("http://alternative/api/dispatch_points")
                .then(response => (this.dispatchData = response.data));
            if (this.dispatchEl.name && this.dispatchEl.id) {
                this.getArrivalData();
            }
                // дата
               var dateNewGet = new Date();
               this.dateNew = dateNewGet.getFullYear()+ "-" + (dateNewGet.getMonth() + 1 > 9? dateNewGet.getMonth() + 1 : "0" + (dateNewGet.getMonth()+ 1)) + "-" + dateNewGet.getDate()  ;
                var toMonthGet = new Date()
                this.toMonth = toMonthGet.getFullYear()+ "-" + (toMonthGet.getMonth() + 2 == 12? 1 : (toMonthGet.getMonth() + 2 > 9 ? toMonthGet.getMonth() + 2 : "0"+(toMonthGet.getMonth() + 2))) + "-" + toMonthGet.getDate()  ;
                console.log( this.dateNew + " " + this.toMonth )
        },
    }
    </script>
      <style>
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
          top: 40px;
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