<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable max-len -->
<!-- eslint-disable vue/multi-word-component-names -->
<template>
      <div class="discr-bus">
        <h5>Выберите место на схеме автобуса</h5>
        <p>
          Автобус: Автобус 51 место (Баг:102)
        </p>
        <span class="gfg_tooltip"
            >!<span class="gfg_text"
              >Перевозчик имеет право заменить транспортное средство, в случае его не исправности,
              аварии и других аналогичных ситуациях.</span
            ></span
          >
        <div class="block-info-seat">
          <p><span class="seat-item example  " ></span> - свободно</p>
          <p><span class="example seat-item seat-item-busy "></span> - занято</p>
        </div>
      </div>

      <div class="center-bus-body">
        <div class="bus-body">
          <div class="all-seat">
            <!-- <button class="seat-item seat-active">1</button>
            <button class="seat-item seat-item-busy">2</button>              
            <button class="seat-item seat-item-none-item"></button> -->
            <div class="line-seat" v-for="n in columnsAmount">
              <template v-for="seat in seats[n-1]">
                <button @click="chooseSeat($event)" :data-name="seat.name" :data-code="seat.code" class="seat-item" :class="{'seat-item-busy': !seat.code}">{{ seat.name }}</button>
                <button v-if="seat.name % 4 == 2" class="seat-item seat-item-none-item"></button>
              </template>
            </div>
          </div>
        </div>
      </div>
      <button @click="passToForm" class="but-go">Продолжить</button>
</template>
<style>
body {
  background-color: #f5f5f5;
}
.gfg_tooltip {
  position: relative;
  display: inline-block;
  color: black;
  height: 25px;
  width: 25px;
  text-align: center;
  display: inline-block;
  font-size: 16px;
  border-radius: 50%;
  border: 1px solid rgb(196, 196, 196);
  font-weight: bold;
  color: rgb(196, 196, 196);
}
.gfg_tooltip .gfg_text {
  visibility: hidden;
  width: 300px;
  background-color: rgba(49, 49, 49, 0.808);
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  font-size: 14px;
  z-index: 1;
  top: -100px;
  left: -140px;
}
.example
{
    display: inline-block;
    width: 20px !important;
    height: 20px !important;
    border-radius: 5px;
    position: relative;
    top: 10px;
    margin-left: 10px !important;
}
.gfg_tooltip:hover .gfg_text {
  visibility: visible;
}
.discr-bus {
  text-align: center;
}
.block-info-seat {
  display: flex;
  justify-content: center;
}
.bus-body {
  border: 4px solid rgb(204, 204, 204);
  display: inline-block;
  border-radius: 20px;
  padding-right: 40px;
  white-space: nowrap;
  position: relative;
  margin: 0px auto;
  background-color: #fff;
}
.bus-body::after {
  content: "";
  width: 30px;
  height: 30px;
  border: 3px solid rgb(184, 184, 184);
  border-radius: 50%;
  position: absolute;
  right: 2px;
  top: 20px;
}
.seat-item {
  width: 35px;
  height: 35px;
  border: 2px solid var(--blue);
  border-radius: 5px;
  display: inline-block;
  margin: 4px;
  color: var(--blue);
  background-color: white;
}
.seat-item-busy {
  border: 2px solid rgb(161, 161, 161);
  color: rgb(161, 161, 161);
  cursor: default !important;
}
.seat-item-none-item {
  opacity: 0;
  cursor: default !important;
}
.seat-active {
  color: white;
  background-color: var(--blue);
}
.all-seat {
    display: flex;
    flex-direction: row-reverse;
  padding: 10px;
  height: 100%;
}
.center-bus-body {
  display: flex;
  justify-content: center;
}
.window-bus {
  margin: 0px auto;
  width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
  background-color: #fff;
  padding: 100px;
  border-radius: 30px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}
.line-seat
{
    display: flex;
    flex-direction: column;
}
@media (max-width: 700px) {
.bus-body
{
    padding: 40px 0px 0px 0px;
}
.bus-body::after
{
    top: 5px;
    left: 20px;
}
  .line-seat
{
    display: flex;
    flex-direction: row;
}
.all-seat
{
    flex-direction: column;
}

  .window-bus {
    padding: 10px;
  }
}
</style>

<script>
  import router from '../router'
  export default {
  props: ['seats', 'columnsAmount', 'race'],
  data(){
    return {
      num: -2019,
      chosenSeats: [],
      loadingRace: true
    }
  },
  computed:{

  },
  methods:{
    chooseSeat(event){
      if(!event.target.classList.contains('seat-active')){
        this.doChairActive(event.target)
      }
      else{
        this.doChairInactive(event.target)
      }
      // console.log(this.chosenSeats)
    },
    doChairActive(btn){
      if(this.chosenSeats.length >= 5){
        return
      }
      btn.classList.add('seat-active')
      this.chosenSeats.push(
        {
          name: btn.dataset.name,
          code: btn.dataset.code
        }
      )
    },
    doChairInactive(btn){
      btn.classList.remove('seat-active')
      this.chosenSeats = this.chosenSeats.filter(el => {
        return el.name != btn.dataset.name && el.code != btn.dataset.code;
      })
    },
    passToForm(){
      sessionStorage['chosenSeats'] = JSON.stringify(this.chosenSeats)
      router.push({ name: 'Form', params: { race_id: this.race.race.uid} })
    }
  },
  mounted(){

  },
  watch: {
    
  }


}
</script>