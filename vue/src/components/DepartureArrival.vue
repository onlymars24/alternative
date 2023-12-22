<!-- eslint-disable max-len -->
<template>
          <div class="place__of__departure">
            <div class="inf-race__date-and-time">
                <p class="inf-race__time">{{dispatchTime}}</p>
                <p class="inf-race__date">{{dispatchDay}}</p>
            </div>
            <div class="inf-race__place punktir">
                <h6 class="inf-race__city">{{ race.race.dispatchStationName }}</h6><p style="" class="discr"><br></p>
            </div>
            </div>
      <div class="place-of-arrival"><div class="inf-race__date-and-time">
        <p class="inf-race__time">{{arrivalTime}}</p>
        <p class="inf-race__date">{{arrivalDay}}</p></div><div class="inf-race__place">
        <h6 class="inf-race__city">{{ race.race.arrivalStationName }}</h6><p style=""><br></p>
        </div></div>
</template>
<script>
import dayjs from 'dayjs'
export default
{
    props: ['race'],
    data(){
        return{
            dispatchDay: '',
            arrivalDay: '',
            dispatchTime: '',
            arrivalTime: '',
            months: [
                '', 'янв.', 'февр.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сент.', 'окт.', 'ноябр.', 'дек.', 
            ],
        }
    },
    mounted(){
        this.dispatchDay = this.race.race.dispatchDate ? dayjs(this.race.race.dispatchDate).format('D')+' '+this.months[dayjs(this.race.race.dispatchDate).format('M')] : ''
        this.arrivalDay = this.race.race.arrivalDate ? dayjs(this.race.race.arrivalDate).format('D')+' '+this.months[dayjs(this.race.race.arrivalDate).format('M')] : ''
        this.dispatchTime = this.race.race.dispatchDate ? dayjs(this.race.race.dispatchDate).format('HH:mm') : ''
        this.arrivalTime = this.race.race.arrivalDate ? dayjs(this.race.race.arrivalDate).format('HH:mm') : ''
    }
};
</script>