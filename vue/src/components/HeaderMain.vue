<template>
<div class="main__header">
        <div class="container">
            <div class="header__inner">
                <div class="header__logo">
                    <a href="index.html"> <img src="../img/741411.png" alt="" class="header-inner-image"> <span>Автовокзал</span> </a>
                </div>
                <ul class="header__links">
                    <li>
                        <a href="">
                            <img src="../img/headphones.png" alt="">
                            <span>Служба поддержки</span>
                        </a>
                    </li>
                    <li>
                        <a href="/login.html">
                            <img src="../img/login_man.png" alt="">
                            <span>Личный кабинет</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="main">
                <h1 class="main__main">
                    Автовокзал Санкт-Петербурга
                </h1>
                <p class="main__main-p">
                    Билеты на автобус онлайн
                </p>
            </div>
            
            <div class="main__table">
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
                    <input class="main__table-date" type="date" v-model="date">
                </div>
                <!-- <div class="main__table-table">
                    <p class="">Пассажиры</p>
                    <input class="main__table-input" type="text">
                </div> -->
                <div class="main__table-button">
                    <button class="main__button">
                        Найти билет
                    </button>
                </div>
            </div>
            <div class="main__another__date">
                <a href="" class="main__another__date-fix">Завтра</a>
                <a href="" class="main__another__date-fix">Послезавтра</a>
            </div>
        </div>
    </div>
</template>

<script>
export default{
        data() {
            return {
            date: '',

            dispatchPointEmpty: false,
            dispatchPointFilled: false,
            dispatchText: '',

            arrivalPointEmpty: false,
            arrivalPointFilled: false,
            arrivalText: '',
            arrivalPointDisabled: true,

            dispatchData: [
                {
                    id: 80153,
                    name: "Новосибирск",
                    region: "Новосибирская обл",
                    details: null,
                    address: null,
                    latitude: null,
                    longitude: null,
                    okato: "50401000000",
                    place: true
                },
                {
                    id: 1774915,
                    name: "Энск",
                    region: "Новосибирская обл",
                    details: "Новосибирск г",
                    address: null,
                    latitude: null,
                    longitude: null,
                    okato: "",
                    place: true
                },
                {
                    id: 73707,
                    name: "Псков",
                    region: "Псковская обл",
                    details: null,
                    address: null,
                    latitude: null,
                    longitude: null,
                    okato: "58401000000",
                    place: true
                }
            ],
            arrivalData: [
                {
                    id: 1770608,
                    name: "58-й километр (Орд. шоссе)",
                    region: "Ордынский район",
                    details: "от Новосибирск ЖД",
                    address: null,
                    latitude: null,
                    longitude: null,
                    okato: null,
                    place: false
                },
                {
                    id: 1770915,
                    name: "58-й километр (Орд. шоссе)",
                    region: "Ордынский район",
                    details: "от Новосибирск ЮЗ",
                    address: null,
                    latitude: null,
                    longitude: null,
                    okato: null,
                    place: false
                },
                {
                    id: 1770610,
                    name: "Автосервис (Тальменка)",
                    region: "Алтайский край",
                    details: "от Новосибирск ЖД",
                    address: null,
                    latitude: null,
                    longitude: null,
                    okato: null,
                    place: false
                }
            ],

            dispatchEl: {
                id: null,
                name: null
            }, 
            arrivalEl: {
                id: null,
                name: null
            }
        }
    },
    methods: {
        dispatchFocus(){
            if(!this.dispatchText){
                this.dispatchPointEmpty = true
                this.dispatchPointFilled = false
            }
            else{
                this.dispatchPointEmpty = false
                this.dispatchPointFilled = true
            }
        },
        dispatchBlur(){
            this.dispatchPointEmpty = this.dispatchPointFilled = this.arrivalPointEmpty = this.arrivalPointFilled = false
            if(this.dispatchText != this.dispatchEl.name && this.dispatchEl.name){
                this.dispatchText = this.dispatchEl.name
            }
        },
        fillDispatch(event){
            this.dispatchEl.id = event.target.dataset.id
            this.dispatchEl.name = event.target.dataset.name
            this.dispatchText = this.dispatchEl.name
            this.arrivalPointDisabled = false
            this.arrivalEl.id = null
            this.arrivalEl.name = null
            this.arrivalText = ''
            this.arrivalBlur()
        },

        arrivalFocus(){
            if(!this.arrivalText){
                this.arrivalPointEmpty = true
                this.arrivalPointFilled = false
            }
            else{
                this.arrivalPointEmpty = false
                this.arrivalPointFilled = true
            }
        },
        arrivalBlur(){
            this.arrivalPointEmpty = this.arrivalPointFilled = this.arrivalPointEmpty = this.arrivalPointFilled = false
            if(this.arrivalText != this.arrivalEl.name && this.arrivalEl.name){
                this.arrivalText = this.arrivalEl.name
            }
        },
        fillArrival(event){
            this.arrivalEl.id = event.target.dataset.id
            this.arrivalEl.name = event.target.dataset.name
            this.arrivalText = this.arrivalEl.name
        },
    },
    watch: {
        dispatchText(newDispatchText, oldDispatchText) {
            if(!newDispatchText){
                this.dispatchPointEmpty = true
                this.dispatchPointFilled = false
            }
            else{
                this.dispatchPointEmpty = false
                this.dispatchPointFilled = true
            }
            if(this.dispatchText == this.dispatchEl.name){
                this.dispatchBlur()
            }
        },
        arrivalText(newArrivalText, oldArrivalText) {
            if(!newArrivalText){
                this.arrivalPointEmpty = true
                this.arrivalPointFilled = false
            }
            else{
                this.arrivalPointEmpty = false
                this.arrivalPointFilled = true
            }
            if(this.arrivalText == this.arrivalEl.name){
                this.arrivalBlur()
            }
        },
        date(newDate){
            console.log(newDate)
        }
    },
    computed: {
        filteredDispatchPoints(){
            return this.dispatchData.filter(el => {
                return el.name && el.name.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
                || el.region && el.region.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
                || el.details && el.details.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
            });
        },
        filteredArrivalPoints(){
            return this.arrivalData.filter(el => {
                return el.name && el.name.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
                || el.region && el.region.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
                || el.details && el.details.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
            });
        }
    }
}
</script>