<template>
  <div class="container">
    <Header/>
  </div>
    <div class="container">
    <h1 style="text-align: center; margin: 30px 0;">Новость</h1>
    <div class="new">
    <div class="new-wrapper">
      <div>
        <h4 class="header">Перевозка багажа</h4>
        <p> <strong>Пассажир имеет право</strong> провозить с собой в автобусах междугородных маршрутов:</p>
        <ul>
          <li>бесплатно - одно место ручной клади, длина, высота и ширина которого в сумме не должна превышать 120 см., в том числе мелких животных и птиц в клетках, одну пару лыж в чехле, детские санки, детскую коляску;</li>
          <li>за плату в багажном отделении транспортного средства багаж в количестве не более двух мест, длина, ширина и высота каждого из которых в сумме не превышают 180 см.</li>
        </ul>
        <p> <strong>Запрещается перевозить:</strong></p>
        <ul>
          <li>зловонные и опасные (легковоспламеняющиеся, взрывчатые, токсичные, коррозионные и др.) вещества;</li>
          <li>холодное и огнестрельное оружие без чехлов и упаковки;</li>
          <li>предметы и вещи, загрязняющие подвижной состав или одежду пассажиров;</li>
        </ul>
        <p>Тара и упаковка должны обеспечивать целостность и сохранность багажа в течение всего периода перевозки.</p>
        <p> <strong>Перевозчик вправе:</strong></p>
        <ol>
          <li>устанавливать нормы перевозки багажа, провоза ручной клади, в том числе бесплатно, большего размера или в большем количестве</li>
          <li>отказать пассажиру в принятии багажа для перевозки, провозе ручной клади, если свойства или упаковка вещей, входящих в состав багажа, ручной клади, не отвечают требованиям, установленным правилами перевозок пассажиров;</li>
          <li>отказать пассажиру в провозе ручной клади, если ее размещение в транспортном средстве будет препятствовать входу пассажиров в транспортное средство, выходу пассажиров из транспортного средства.</li>
        </ol>
        <p> <strong>категорически запрещается:</strong></p>
        <ul>
          <li>перевозить багаж, если его не сопровождает владелец;</li>
          <li>брать у граждан коробки, сумки, бандероли, любые другие предметы для перевозки в автобусе, в целях передачи их в других городах.</li>
        </ul>
      </div>
    </div>
    </div>
  </div>
  <hr class="bef__footer">
    <Footer/>
</template>
<script>
  import AccordionItem from '../components/AccordionItem.vue';
  import Header from '../components/Header.vue';
  import Footer from '../components/Footer.vue'

  export default
  {
    components:{AccordionItem, Header, Footer},
    data(){
      return{

      }
    }
  }
</script>
<style scoped>

/* Accordion */
.input-accord {
    position: absolute;
    opacity: 0;
    z-index: -1;
}
.new-wrapper {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
    margin:0 auto;
    background-color: white;
    padding: 40px;
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