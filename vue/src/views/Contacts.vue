<template>
  <div class="container">
    <Header/>
  </div>
    <div class="container">
    <h1 style="text-align: center; margin: 30px 0;">Контакты</h1>
    <div class="faq__menu-and-accord">
    <div class="accordion-wrapper">
      <Feedback/>
    </div>
    </div>
  </div>
  <!-- <hr class="bef__footer"> -->
    <!-- <Footer/> -->
</template>
<script>
  import AccordionItem from '../components/AccordionItem.vue';
  import Header from '../components/Header.vue';
  import Footer from '../components/Footer.vue'
  import Feedback from '../components/Feedback.vue';

  export default
  {
    components:{AccordionItem, Header, Footer, Feedback},
    data(){
      return{
        activeMenu:1,
        AccordItem:{
          raceItem:[
             {
                id:1,
                header:'Какие рейсы есть?',
                text:'Любые есть' ,
                open: false
            },
            {
                id:2,
                header:'Какие рейсы есть?',
                text:'Любые есть' ,
                open: false
            },
            {
                id:3,
                header:'Какие рейсы есть?',
                text:'Любые есть' ,
                open: false
            },
          ],
          covid:[
            {
                id:4,
                header:'какой ковид есть',
                text:'Любой есть' ,
                open: false
            }
          ],
          td:[
            {
                id:5,
                header:'какой тд есть',
                text:'Любые есть' ,
                open: false
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
    width: 700px;
    margin:0 auto;
    background-color: white;
    padding: 40px;
}
.accordion-wrapper p, .accordion-wrapper li{
  margin-bottom: 10px;
}
.accordion-wrapper ul li{
  list-style-type: disc;
}
.accordion-wrapper ol li{
  list-style-type: decimal;
}
.accordion-wrapper ol, .accordion-wrapper ul{
  padding-left: 1rem;
}
.accordion-wrapper{
  list-style-type: disc;
}
.accordion {
    width: 100%;
    overflow: hidden;
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
    font-size: 17px;
    font-weight: 400;
}
.accordion-label::after {
    content: "\276F";
    width: 16px;
    height: 16px;
    text-align: center;
    color: var(--blue);

}
/* .accordion-wrapper .animation-enter-active {
  height: 100px !important;
  transition: all 1s ease !important;
  overflow: hidden !important;
}

.accordion-wrapper .animation-leave-active {
  height: 100px !important;
  transition: all 1s ease !important;
  overflow: hidden !important;
} */
.accordion-content {
    height: 40px;
    padding: 0 6px;
    background: white;
    overflow: auto;
    /* -webkit-transition: all 0.3s;
    transition: all 0.3s; */
}
.accordion-content p{
    margin: 0;
    font-size: 14px;
}
/* input:checked + .accordion-label::after {
    -webkit-transform: rotate(90deg);
    transform: rotate(90deg);
}
input:checked ~ .accordion-label {
   color: var(--blue);
}
input:checked ~ .accordion-content {
    max-height: 100vh;
    padding: 16px;
} */
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
  font-weight: bold;
}
.faq__menu__item 
{
  cursor: pointer;
}
@media (min-width: 320px) and (max-width: 768px){
  .faq__menu-and-accord{
    flex-direction: column;
  } 
  .accordion-wrapper{
    max-width: 100%;
    padding: 20px;
    margin-top: 15px;
  }
}
  
</style>