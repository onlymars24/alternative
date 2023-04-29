<template>
    
<div class="main__header">
        <div class="container">
            <Header :blackText="false"/>

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
                    </div>  
                <div class="main__table-button">
                    <button type="submit" class="main__button" :disabled="disabledButton">
                        Найти билет
                    </button>
                </div>
            </form>
            <div v-if="dispatchEl.id && arrivalEl.id" class="main__another__date">
                <a href="" class="main__another__date-fix" @click="otherDay(dates.today)">Сегодня</a>
                <a href="" class="main__another__date-fix" @click="otherDay(dates.tomorrow)">Завтра</a>
                <a href="" class="main__another__date-fix" @click="otherDay(dates.afterTomorrow)">Послезавтра</a>
            </div>
        </div>
    </div>
</template>

<script>
import router from '../router'
import axios, {isCancel, AxiosError} from 'axios';
import axiosClient from '../axios'
import Header from '../components/Header.vue'
import * as dayjs from 'dayjs'
export default{
        components: {
            Header
        },
        props: {
            dispatchEl0: {
                type: Object,
                default: function () {
                    return {
                        id: null,
                        name: null
                    }
                }
            },
            arrivalEl0: {
                type: Object,
                default: function () {
                    return {
                        id: null,
                        name: null
                    }
                }
            },
            date0: {
                type: String,
                default: ''
            },
        },
        emits: ['changeRaces'],
        data() {
            return {
            auth: false,
            
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
            dates: {
                today: '0',
                tomorrow: '0',
                afterTomorrow: '0'
            },
            dispatchEl: this.dispatchEl0, 
            arrivalEl: this.arrivalEl0
        }
    },
    methods: {
        logout(){
            localStorage.removeItem('authToken')
            router.push({ name: 'Login'})
        },
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
        async fillDispatch(event){
            this.dispatchEl.id = event.target.dataset.id
            this.dispatchEl.name = event.target.dataset.name
            this.dispatchText = this.dispatchEl.name
            this.arrivalEl.id = null
            this.arrivalEl.name = null
            this.arrivalText = ''
            this.arrivalBlur()
            this.getArrivalData()
        },

        async getArrivalData(){
             const promise = axiosClient
                .get('/arrival_points/'+this.dispatchEl.id)
                .then(response => (this.arrivalData = JSON.parse(response.data)));
            await promise
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

        findRaces(){
            this.$emit('changeRaces', this.date, this.dispatchEl.id, this.arrivalEl.id)
            router.push({ name: 'Races', params: { dispatch_id: this.dispatchEl.id, dispatch_name: this.dispatchEl.name, arrival_id: this.arrivalEl.id, arrival_name: this.arrivalEl.name, date: this.date } })
        },
        otherDay(date){
            this.date = date
            this.$emit('changeRaces', date, this.dispatchEl.id, this.arrivalEl.id)
            router.push({ name: 'Races', params: { dispatch_id: this.dispatchEl.id, dispatch_name: this.dispatchEl.name, arrival_id: this.arrivalEl.id, arrival_name: this.arrivalEl.name, date: date } })
        }
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
        },
        disabledButton(){
            return !this.dispatchEl.id || !this.arrivalEl.id || !this.date;
        },
        arrivalPointDisabled(){
            return !this.dispatchEl.name && !this.dispatchEl.id;
        },
    },
    mounted() {
        this.dates.today = dayjs().format('YYYY-MM-DD')
        this.dates.tomorrow = dayjs().add(1, 'day').format('YYYY-MM-DD')
        this.dates.afterTomorrow = dayjs().add(2, 'day').format('YYYY-MM-DD')

        console.log(this.dates)
        if(localStorage.getItem('authToken')){
            this.auth = true
        }
        axiosClient
        .get('/dispatch_points')
        .then(response => (this.dispatchData = response.data));

        if(this.dispatchEl.name && this.dispatchEl.id){
            this.getArrivalData()
        }
     // дата
     var dateNewGet = new Date();
               this.dateNew = dateNewGet.getFullYear()+ "-" + (dateNewGet.getMonth() + 1 > 9? dateNewGet.getMonth() + 1 : "0" + (dateNewGet.getMonth()+ 1)) + "-" + dateNewGet.getDate()  ;
                var toMonthGet = new Date()
                this.toMonth = toMonthGet.getFullYear()+ "-" + (toMonthGet.getMonth() + 2 == 12? 1 : (toMonthGet.getMonth() + 2 > 9 ? toMonthGet.getMonth() + 2 : "0"+(toMonthGet.getMonth() + 2))) + "-" + toMonthGet.getDate()  ;
                console.log( this.dateNew + " " + this.toMonth )
    }
    
}
</script>