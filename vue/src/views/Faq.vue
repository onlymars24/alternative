<template>
  <div class="container">
    <h1>Вопросы и ответы</h1>
    <div class="faq__menu-and-accord">
    <ul class="faq__menu">
      <li class="faq__menu__item " :class="{'faq__active-menu': activeMenu == 1}" @click="activeMenu = 1">Рейсы автобусы</li>
      <li class="faq__menu__item" :class="{'faq__active-menu': activeMenu == 2}" @click="activeMenu = 2">Кароновирус</li>
      <li class="faq__menu__item" :class="{'faq__active-menu': activeMenu == 3}" @click="activeMenu = 3">Тд</li>
    </ul>
    <div class="accordion-wrapper">
      <div v-show="activeMenu == 1">
        <h4 class="header">Рейсы и автобус</h4>
        <AccordionItem :AccordItem="AccordItem.raceItem" />
      </div>
      <div v-show="activeMenu == 2">
        <h4>Карона</h4>
        <AccordionItem :AccordItem="AccordItem.covid"/>
      </div>
      <div v-show="activeMenu == 3">
        <h4>Тд</h4>
        <AccordionItem :AccordItem="AccordItem.td"/>
      </div>
      
      
    </div>
    </div>
  </div>
</template>
<script>
  import AccordionItem from '../components/AccordionItem.vue';

  export default
  {
    components:{AccordionItem},
    data(){
      return{
        activeMenu:1,
        AccordItem:{
          raceItem:[
             {
              id:1,
            header:'какие рейсы есть',
            text:'Любые есть' ,
          },
          {
              id:2,
            header:'какие рейсы есть',
            text:'Любые есть' 
          },
          {
              id:3,
            header:'какие рейсы есть',
            text:'Любые есть' 
          },
          ],
          covid:[
            {
              id:4,
            header:'какой ковид есть',
            text:'Любой есть' ,
            }
          ],
          td:[
            {
              id:5,
            header:'какой тд есть',
            text:'Любые есть' ,
            }
          ]
         
       
        }
      }
    }
  }
</script>
<style>

/* Accordion */
.input-accord {
    position: absolute;
    opacity: 0;
    z-index: -1;
}
.accordion-wrapper {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
    width: 600px;
    margin:0 auto;
    background-color: white;
    padding: 40px;
}
.accordion {
    width: 100%;
    overflow: hidden;
    margin-bottom: 16px;
}
.accordion:last-child{
    margin-bottom: 0;
}
.accordion-label {
    display: flex;
    -webkit-box-pack: justify;
    justify-content: space-between;
    padding: 16px;
    padding-left: 0px;
    font-weight: bold;
    cursor: pointer;
    font-size: 20px;
    font-size: 16px;
    font-weight: 400;
}

.accordion-label:hover {
    
}
.accordion-label::after {
    content: "\276F";
    width: 16px;
    height: 16px;
    text-align: center;
    -webkit-transition: all 0.3s;
    transition: all 0.3s;
}
.accordion-content {
    max-height: 0;
    padding: 0 6px;
    background: white;
    -webkit-transition: all 0.3s;
    transition: all 0.3s;
}
.accordion-content p{
    margin: 0;
    font-size: 14px;
}
input:checked + .accordion-label::after {
    -webkit-transform: rotate(90deg);
    transform: rotate(90deg);
}
input:checked ~ .accordion-label {
   color: var(--blue);
}
input:checked ~ .accordion-content {
    max-height: 100vh;
    padding: 16px;
}
/* menu */
.faq__menu
{
  display: flex;
  flex-direction: column;
}
.faq__menu__item
{
  padding: 10px;
  border-left: 2px solid rgb(192, 192, 192);
}
.faq__menu-and-accord
{
  display: flex;
}
.faq__active-menu
{
  border-left: 2px solid var(--blue);
}
.faq__menu__item 
{
  cursor: pointer;
}
  
</style>