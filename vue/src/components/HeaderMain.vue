<template>
<div class="main__header">
        <div class="container">
            <Header :key="dispatchEl.name" :dispatchName="dispatchEl.name" :blackText="false"/>
            <div class="main">
                <h1 style="text-shadow: 1px 1px 5px black" v-if="isRaces && dispatchEl.name && arrivalEl.name" class="main__main">
                    Автобус {{dispatchEl.name}} - {{arrivalEl.name}}
                </h1>
                <h1 style="text-shadow: 1px 1px 5px black" v-else-if="page" class="main__main">
                    {{ page.name }}
                </h1>
                <h1 style="text-shadow: 1px 1px 5px black" v-else class="main__main">
                    Автовокзалы России
                </h1>
                <p style="text-shadow: 1px 1px 5px black" v-if="!isRaces" class="main__main-p">
                    Билеты на автобус онлайн
                </p>
            </div>
            <div class="main__table">
                <div class="main__table-table">
                    <!-- <ul v-if="dispatchPointEmpty" class="hint">
                        <li class="hint__title">
                            <span>Популярные направления:</span>
                        </li>
                        <li @mousedown="fillDispatch" data-id="73707" data-name="Псков">
                            <strong data-id="73707" data-name="Псков">Псков, </strong>
                            <span data-id="73707" data-name="Псков">Псковская обл,</span>
                        </li>
                    </ul> -->
                    <ul v-if="dispatchPointFilled" class="hint">
                        <template v-for="el in filteredDispatchPoints" :key="el.keyId">
                            <li v-if="el.hasOwnProperty('details')" @mousedown="fillDispatch" :data-id="el.keyId" :data-name="el.name">
                                <strong :data-id="el.keyId" :data-name="el.name">{{el.name}}{{el.region || el.details ? ', ' : '' }}</strong>
                                <span :data-id="el.keyId" :data-name="el.name">{{el.region}}{{(el.details && !el.region) || !el.details? '' : ', ' }}{{el.details}}</span>
                            </li>
                            <li v-else class="big__font-size" @mousedown="fillDispatch" :data-id="el.keyId" :data-name="el.name">
                                <strong class="big__font-size" :data-id="el.keyId" :data-name="el.name">{{el.name}}{{el.region || el.district ? ', ' : '' }}</strong>
                                <span style="font-size: 16px;"  :data-id="el.keyId" :data-name="el.name">{{el.region}}{{(el.district && !el.region) || !el.district? '' : ', ' }}{{el.district}}</span>
                            </li>
                        </template>
                    </ul>
                    <p class="">Откуда</p>
                    <input :data-id="dispatchEl.keyId" :data-name="dispatchEl.name" @focus="dispatchFocus" @blur="dispatchBlur" v-model="dispatchText" class="main__table-input-1" type="text" placeholder="Заполните откуда">
                </div>
                <div class="main__table-table">
                    <ul v-if="arrivalPointEmpty && popularPoints.length > 0" class="hint">
                        <li class="hint__title">
                            <span>Популярные направления:</span>
                        </li>
                        <!-- <li @mousedown="fillArrival" data-id="1770608" data-name="58-й километр (Орд. шоссе)">
                            <strong data-id="1770608" data-name="58-й километр (Орд. шоссе)">58-й километр (Орд. шоссе), </strong>
                            <span data-id="1770608" data-name="58-й километр (Орд. шоссе)">Ордынский район, от Новосибирск ЖД</span>
                        </li> -->
                        <template v-for="el in popularPoints">
                            <PopularPoint :id="el.arrival_point_id" :name="el.name" :region="el.region" :details="el.details" @fillArrival="fillArrival"/>
                        </template>
                        
                    </ul>
                    <ul v-if="arrivalPointFilled" class="hint">
                        <template v-for="el in filteredArrivalPoints" :key="el.keyId">
                            <li v-if="el.hasOwnProperty('details')" @mousedown="fillArrival" :data-id="el.keyId" :data-name="el.name">
                                <strong :data-id="el.keyId" :data-name="el.name">{{el.name}}{{el.region || el.details ? ', ' : '' }}</strong>
                                <span :data-id="el.keyId" :data-name="el.name">{{el.region}}{{(el.details && !el.region) || !el.details? '' : ', ' }}{{el.details}}</span>
                            </li>
                            <li v-else class="big__font-size" @mousedown="fillArrival" :data-id="el.keyId" :data-name="el.name">
                                <strong class="big__font-size" :data-id="el.keyId" :data-name="el.name">{{el.name}}{{el.region || el.district ? ', ' : '' }}</strong>
                                <span style="font-size: 16px;" :data-id="el.keyId" :data-name="el.name">{{el.region}}{{(el.district && !el.region) || !el.district? '' : ', ' }}{{el.district}}</span>
                            </li>
                        </template>
                    </ul>
                    <p class="">Куда</p>
                    <input @click="arrivalFocus" @blur="arrivalBlur" v-model="arrivalText" :disabled="arrivalPointDisabled" class="main__table-input" list="OWNER" placeholder="Заполните куда" type="text">
                </div>
                <div class="main__table-table">
                    <p class="">Дата поездки</p>
                    <input id="calendar" class="main__table-date" type="date" style="width: 100%;" :min="dateNew" :max="toMonth" v-model="date" placeholder="Дата поездки">
                </div>
                <div class="main__table-button">
                    <button v-if="disabledButton" @click="findRaces" type="button" class="main__button">
                        Найти билет
                    </button>
                    <div class="main__button-link" v-else>
                        <a style="width:100%;" :href="'/автобус/'+createSlug(dispatchEl.name)+'/'+createSlug(arrivalEl.name)+(date ? '?on='+date : '')" type="button" class="main__button" disabled>
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <p>Найти билет</p>
                            </div>
                        </a>
                    </div>


                </div>
            </div>
            <div v-if="dispatchEl.id && arrivalEl.id" class="main__another__date">
                <a :href="'/автобус/'+createSlug(dispatchEl.name)+'/'+createSlug(arrivalEl.name)+'?on='+dates.today" :class="{'main__another__date-fix__active': dates.today !=  $route.query.on, 'strong': dates.today ==  $route.query.on}" class="main__another__date-fix">Сегодня</a>
                <a :href="'/автобус/'+createSlug(dispatchEl.name)+'/'+createSlug(arrivalEl.name)+'?on='+dates.tomorrow" :class="{'main__another__date-fix__active': dates.tomorrow !=  $route.query.on, 'strong': dates.tomorrow ==  $route.query.on}" class="main__another__date-fix">Завтра</a>
                <a :href="'/автобус/'+createSlug(dispatchEl.name)+'/'+createSlug(arrivalEl.name)+'?on='+dates.afterTomorrow" :class="{'main__another__date-fix__active': dates.afterTomorrow !=  $route.query.on, 'strong': dates.afterTomorrow ==  $route.query.on}" class="main__another__date-fix">Послезавтра</a>
            </div>
            <div v-else class="main__another__date">
                <span :class="{'main__another__date-fix__active': dates.today !=  $route.query.on, 'strong': dates.today ==  $route.query.on}" class="main__another__date-fix">Сегодня</span>
                <span style="margin: 0 5px;" :class="{'main__another__date-fix__active': dates.tomorrow !=  $route.query.on, 'strong': dates.tomorrow ==  $route.query.on}" class="main__another__date-fix">Завтра</span>
                <span :class="{'main__another__date-fix__active': dates.afterTomorrow !=  $route.query.on, 'strong': dates.afterTomorrow ==  $route.query.on}" class="main__another__date-fix">Послезавтра</span>
            </div>
        </div>
    </div>
</template>

<script>
import router from '../router'
import axios, {isCancel, AxiosError} from 'axios';
import axiosClient from '../axios'
import Header from '../components/Header.vue'
import PopularPoint from '../components/PopularPoint.vue'
import dayjs from 'dayjs'
import { spread } from 'lodash';
export default{
        name: "HeaderMain",
        components: {
            Header,
            PopularPoint
        },
        props: {
            dispatchEl0: {
                type: Object,
                default: function () {
                    return {
                        id: null,
                        name: null,
                        slug: null
                    }
                }
            },
            arrivalEl0: {
                type: Object,
                default: function () {
                    return {
                        id: null,
                        name: null,
                        slug: null
                    }
                }
            },
            date0: {
                type: String,
                default: ''
            },
            isRaces: {

            },
            page: {},
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

            dispatchData: [],
            arrivalData: [],
            dates: {
                today: '0',
                tomorrow: '0',
                afterTomorrow: '0'
            },
            dispatchEl: this.dispatchEl0, 
            arrivalEl: this.arrivalEl0,
            dateNew: '',
            toMonth: '',
            popularPoints: []
        }
    },
    methods: {
        createSlug(text){
            return text.replace(/ /g, '_').replace(/['",.]/g, '').replace(/\//g, '-') 
        },
        logout(){
            localStorage.removeItem('authToken')
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
            // console.log(event.target.dataset)
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
            // console.log(this.dispatchEl, this.dispatchData)
            let tempPoint = this.dispatchData.filter(point => {
                return point.keyId == this.dispatchEl.id
            })[0]
            let pointType = tempPoint.hasOwnProperty('details') ? 'e' : 'k'
            let pointId = tempPoint.id
            // console.log('/arrival/points?pointType='+pointType+'&pointId='+pointId)
            const promise = axiosClient
            .get('/arrival/points?pointType='+pointType+'&pointId='+pointId)
            .then(response => {
                console.log(response)
                this.arrivalData = response.data.arrivalPoints
                // console.log(this.arrivalData)
                // // let tempPopularPoints = JSON.parse(tempPoint.popular_arrival_points)
                if(tempPoint.hasOwnProperty('details')){
                    this.popularPoints = this.arrivalData.filter(obj => JSON.parse(tempPoint.popular_arrival_points).includes(obj.arrival_point_id));
                }
                // console.log('popular points')
                // console.log(this.popularPoints)
            })
            .catch(error => {
                console.log(error)
            })
            await promise

            // console.log(tempPoint)
            // console.log('/arrival/points?pointType='+(tempPoint.hasOwnProperty('details') ? 'e' : 'k')+'&pointId='+this.dispatchEl.id)

            // this.arrivalData.forEach((arrival, ind) => {
            //     arrival.keyId = ind+1
            // })
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
            // console.log(event)
            this.arrivalEl.id = event.target.dataset.id
            this.arrivalEl.name = event.target.dataset.name
            this.arrivalText = this.arrivalEl.name
        },

        findRaces(){
            // this.$emit('changeRaces', this.date, this.dispatchEl.id, this.arrivalEl.id, this.dispatchEl.name, this.arrivalEl.name)
            // console.log(this.dispatchEl, this.arrivalEl, this.data)
            router.push({ name: 'Races', query: {on: this.date}, params: { dispatch_name: this.dispatchEl.name, arrival_name: this.arrivalEl.name } })
            // window.scrollTo(0, 600);
            // window.location.replace(window.location.origin + window.location.pathname);
        },
        otherDay(date){
            this.date = date
            // this.$emit('changeRaces', date, this.dispatchEl.id, this.arrivalEl.id, this.dispatchEl.name, this.arrivalEl.name)
            router.push({ name: 'Races', query: { on: this.date }, params: { dispatch_name: this.dispatchEl.name, arrival_name: this.arrivalEl.name } })
            
            // window.scrollTo(0, 600);
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
            // return this.dispatchData.filter(el => {
            //     return el.name && el.name.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
            //     || el.region && el.region.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
            //     || el.details && el.details.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
            // });
            let cities = this.dispatchData.filter(el => {
                return el.name && el.name.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
            });

            if(cities.length != 0){
                let wholeArr = this.dispatchData.filter(el => {
                    return el.name && el.name.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1 
                    || el.region && el.region.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
                    || el.details && el.details.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
                });
                cities.forEach(function(city, indCity) {
                    let indWhole = wholeArr.findIndex(el => city.name === el.name && city.region === el.region && city.details === el.details);
                    let temp = wholeArr[indWhole];
                    wholeArr[indWhole] = wholeArr[indCity]
                    wholeArr[indCity] = temp
                })
                return wholeArr
            }
            return this.dispatchData.filter(el => {
                return el.name && el.name.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1 
                || el.region && el.region.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
                || el.details && el.details.toUpperCase().indexOf(this.dispatchText.toUpperCase()) !== -1
            })
        },
        filteredArrivalPoints(){
            //  let temp = this.arrivalData.filter(el => {
            //     return el.name && el.name.toUpperCase().indexOf(this.arrivalText.toUpperCase(), 0) !== -1
            //     || el.region && el.region.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
            //     || el.details && el.details.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
            // });
            
            // console.log(temp)
            
            // return temp
            let cities = this.arrivalData.filter(el => {
                return el.name && el.name.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
            });

            if(cities.length != 0){
                let wholeArr = this.arrivalData.filter(el => {
                    return el.name && el.name.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1 
                    || el.region && el.region.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
                    || el.details && el.details.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
                });
                cities.forEach(function(city, indCity) {
                    let indWhole = wholeArr.findIndex(el => city.name === el.name && city.region === el.region && city.details === el.details);
                    let temp = wholeArr[indWhole];
                    wholeArr[indWhole] = wholeArr[indCity]
                    wholeArr[indCity] = temp
                })
                return wholeArr
            }
            return this.arrivalData.filter(el => {
                return el.name && el.name.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1 
                || el.region && el.region.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
                || el.details && el.details.toUpperCase().indexOf(this.arrivalText.toUpperCase()) !== -1
            })
        },
        disabledButton(){
            return !this.dispatchEl.id || !this.arrivalEl.id || !this.date;
        },
        arrivalPointDisabled(){
            return !this.dispatchEl.name && !this.dispatchEl.id;
        },
    },
    async mounted(){
        let dash = 'G'
        console.log(dash)
        
        console.log(dash.toLowerCase())
        console.log(this.createSlug('No,vosib.ir\'sk\" AB/'))
        this.dates.today = dayjs().format('YYYY-MM-DD')
        if(this.date == ''){
            this.date = this.dates.today
        }
        this.dates.tomorrow = dayjs().add(1, 'day').format('YYYY-MM-DD')
        this.dates.afterTomorrow = dayjs().add(2, 'day').format('YYYY-MM-DD')
        if(localStorage.getItem('authToken')){
            this.auth = true
        }
        const promise = axiosClient
        .get('/dispatch/points')
        .then(response => {
            // console.log(response)
            this.dispatchData = response.data.dispatchPoints
        });
        await promise
        // this.dispatchData.forEach((dispatch, ind) => {
        //     dispatch.keyId = ind+1
        // })
        // console.log(this.dispatchData)
        // console.log('this.busStationDispatchPointId '+this.busStationDispatchPointId)
        console.log(this.station)
        console.log(this.page)
        if(this.page){
            let tempDispatch = null
            console.log(this.page)

            if(this.page.kladr){
                tempDispatch = this.dispatchData.filter(point => {
                    return point.slug == this.page.kladr.slug
                })[0] 
            }
            else if(this.page.station && this.page.station.dispatch_point){
                tempDispatch = this.dispatchData.filter(point => {
                    return point.slug == this.page.station.dispatch_point.slug
                })[0]            
            }

            if(tempDispatch){
                this.dispatchEl.id = tempDispatch.keyId
                this.dispatchEl.name = tempDispatch.name
                this.dispatchText = this.dispatchEl.name
                this.arrivalEl.id = null
                this.arrivalEl.name = null
                this.arrivalText = ''
                this.arrivalBlur()
                this.getArrivalData()
            }

        }
        if(this.dispatchEl.name && this.dispatchEl.id){
            this.getArrivalData()
        }
        var dateNewGet = new Date();
        this.dateNew = dateNewGet.getFullYear()+ "-" + (dateNewGet.getMonth() + 1 > 9? dateNewGet.getMonth() + 1 : "0" + (dateNewGet.getMonth()+ 1)) + "-" + dateNewGet.getDate();
        this.toMonth = dateNewGet.getFullYear()+ "-" + (dateNewGet.getMonth() + 2 == 12? 1 : (dateNewGet.getMonth() + 2 > 9 ? dateNewGet.getMonth() + 2 : "0"+(dateNewGet.getMonth() + 2))) + "-" + dateNewGet.getDate();
    }
}
</script>
<style scoped>
.main__button{
    text-align: center;
}
.main__another__date{
  text-align: center;
  margin-top: 15px;
  margin-left: 325px;
}
.main__another__date .strong{
  font-weight: 500;
}
.main__another__date a{
  margin-right: 4px;
  color: white
}
.main__another__date-fix{
    text-decoration: dotted;
    color: white;
}
.main__another__date-fix__active{
    border-bottom: 1px white dashed;
}
.big__font-size{
    font-size: 20px;
}
@media (max-width: 992px)
{
    .main__another__date{
        text-align: center;
        margin-top: 15px;
        margin-left: 0px;
    }
}

@media (max-width: 768px)
{
    .main__header .main{
        margin-top: 70px;
    }
}


.hint{
        z-index: 2;
        list-style-type: none;
        max-height: 242px;
        /* width: 400px; */
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #fff;
        position: absolute;
        top: 115%;
        -webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
     -moz-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
      -ms-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
       -o-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
      box-shadow: 0px 0px 10px rgba(0,0,0,.8);
    }
    /* 
Firefox */
   .hint {
  scrollbar-width: thin;
  scrollbar-color: rgb(223, 223, 223) rgb(255, 255, 255);
  padding: 0px;
}


.hint::-webkit-scrollbar {
  height: 12px;
  width: 6px;

}
.hint::-webkit-scrollbar-track {
  background: rgb(255, 255, 255);

}
.hint::-webkit-scrollbar-thumb {
  background-color: rgb(223, 223, 223) ;
  border-radius: 5px;
  border: 3px solid rgb(223, 223, 223);
}

    li, ul{
        padding: 0;
        margin: 0;
    }
    .hint li{
        padding: 5px;
        font-size: 17px;
        cursor: pointer;
    }
    .hint li:hover{
        background-color: #DCDCDC;
    }
    .hint__title:hover{
        background-color: #fff;
    }
    .hint li span{
        font-size: 14px;
        color: grey;
    }
    .main__table{
        position: relative;
    }
    @media (max-width: 993px){
        .hint{
            top: 150px;
            /* min-width: 100%; */
            max-width: 100%;
        }
    }


</style>