<template>
<HeaderMain @changeRaces="changeRaces0" :arrivalEl0="arrivalEl" :dispatchEl0="dispatchEl" :date0="date"/>
<div>
    RACES <pre>{{ races }}</pre>>
    <div class="menu" style="margin-top: 50px;">
		<div class="container">
            <div class="menu__intro">
				<p>Отправление и прибытие по местному времени</p>
				<h4>Расписание автобусов {{dispatchEl.name}} — {{arrivalEl.name}}</h4>
				<!-- <div class="menu__inro-sort">
					<div class="inro-sort__button">
                	<button>
                		Время отправления
                	</button>
            	</div>
            	<div class="inro-sort__button">
                	<button>
                		Время в пути
                	</button>
            	</div>
            	<div class="inro-sort__button">
                	<button>
                		Время прибытия
                	</button>
            	</div>
            	<div class="inro-sort__button">
                	<button>
                		Стоимость
                	</button>
            	</div>
            	<div class="inro-sort__button">
                	<button>
                		Популярность
                	</button>
            	</div>
				</div> -->
			</div>
            <template v-for="race in races">
                <RaceCard :race="race"/> 
            </template>
              
		</div>
	</div>


</div>
<Footer/>
</template>


<script>
import HeaderMain from '../components/HeaderMain.vue';
import Footer from '../components/Footer.vue';
import RaceCard from '../components/RaceCard.vue';
import router from '../router';
import axios from 'axios';
import * as dayjs from 'dayjs'

export default {
    components: {
        HeaderMain,
        Footer,
        RaceCard
    },
    data(){
        return{
            arrivalEl: {
                id: this.$route.params['arrival_id'],
                name: this.$route.params['arrival_name']
            },
            dispatchEl: {
                id: this.$route.params['dispatch_id'],
                name: this.$route.params['dispatch_name']
            },
            date: this.$route.params['date'],
            races: [],
            months: [
                'янв.', 'февр.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сент.', 'окт.', 'ноябр.', 'дек.', 
            ]
        }
    },
    mounted(){
        // console.log(~~(1/2))
        this.changeRaces0(this.date, this.dispatchEl.id, this.arrivalEl.id);
    },
    methods: {
        async changeRaces0(date, dispatch_id, arrival_id){
            const promise = axios
                .get('http://localhost:8000/api/races/'+date+'/?dispatch_point_id='+dispatch_id+'&arrival_point_id='+arrival_id)
                .then(response => (
                    this.races = response.data
                ));
            await promise
            // console.log(this.races)
            this.races.forEach(race => {
                race.section = 'route'
                race.details_menu = false
                // console.log(dayjs(race.dispatchDate))
                // race.dispatchDateObj = dayjs(race.dispatchDate)
                // race.arrivalObj = dayjs(race.arrivalDate)
                race.dispatchDay = dayjs(race.dispatchDate).format('D')+' '+this.months[dayjs(race.dispatchDate).format('M')]
                race.arrivalDay = dayjs(race.arrivalDate).format('D')+' '+this.months[dayjs(race.arrivalDate).format('M')]
                
                race.dispatchTime = dayjs(race.dispatchDate).format('HH:mm')
                race.arrivalTime = dayjs(race.arrivalDate).format('HH:mm')
                // difference = dayjs(race.arrivalDate).diff(dayjs(race.dispatchDate))/1000
                
                // race.difference = ''
                // if(~~(difference / 3600) != 0){
                //     race.difference = difference -(difference / 3600)+'ч.'
                // }
                
            });
        }
    }
    
}
</script>