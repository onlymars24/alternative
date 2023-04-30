<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'

export default{
    name: "Main",
    components: {
        HeaderMain,
        Footer,
    },
    data() {
        return {}
    },
    methods: {
        
    },
    watch: {
        
    },
    computed: {

    }
}
</script>

<template>
    <HeaderMain/>
    <div class="choice">
        <div class="container">
            <div class="choice__inner">
                <div class="choice__fact">
                    <div class="choice__image">
                        <img src="../img/clock.png" alt="" class="choice__img">
                    </div>
                    <div class="choice__text">
                        <h5 class="choice__text-main">
                            Lorem ipsum dolor sit.
                        </h5>
                        <p class="choice__text-pass">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque corporis, libero impedit excepturi corrupti optio aliquam odio quia nihil illo.
                        </p>
                    </div>
                </div>
        
                <div class="choice__fact">
                    <div class="choice__image">
                        <img src="../img/road.png" alt="" class="choice__img">
                    </div>
                    <div class="choice__text">
                        <h5 class="choice__text-main">
                            Lorem ipsum dolor sit.
                        </h5>
                        <p class="choice__text-pass">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque corporis, libero impedit excepturi corrupti optio aliquam odio quia nihil illo.
                        </p>
                    </div>
                </div>
        
                <div class="choice__fact">
                    <div class="choice__image">
                        <img src="../img/cards.png" alt="" class="choice__img">
                    </div>
                    <div class="choice__text">
                        <h5 class="choice__text-main">
                            Lorem ipsum dolor sit.
                        </h5>
                        <p class="choice__text-pass">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque corporis, libero impedit excepturi corrupti optio aliquam odio quia nihil illo.
                        </p>
                    </div>
                </div>
        
                <div class="choice__fact">
                    <div class="choice__image">
                        <img src="../img/tickets.png" alt="" class="choice__img">
                    </div>
                    <div class="choice__text">
                        <h5 class="choice__text-main">
                            Lorem ipsum dolor sit.
                        </h5>
                        <p class="choice__text-pass">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque corporis, libero impedit excepturi corrupti optio aliquam odio quia nihil illo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="whychoice">
        <div class="container">
            <div class="whychoice__inner">
                <div class="whychoice__main">
                    <h3 class="whychoice__main-text">
                        Почему выбирают Lorem
                    </h3>
                </div>
                <div class="whychoice__facts">
                    <div class="whychoice__facts-facts">
                        <h2 class="whychoice__facts-main__text">
                            Lorem ipsum dolor sit.
                        </h2>
                        <p class="whychoice__facts-text">
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Itaque, consequuntur in! Exercitationem, ut odit.
                        </p>
                        <p class="whychoice__facts-text">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Nisi eius eveniet corrupti fuga ea quis minima voluptates doloremque, nesciunt vel, sunt laudantium voluptatibus reprehenderit ut culpa vero, consequuntur nemo? Minus
                        </p>
                    </div>
                    <div class="whychoice__facts-facts">
                        <h2 class="whychoice__facts-main__text">
                            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ratione?
                        </h2>
                        <p class="whychoice__facts-text">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia, nulla. Et incidunt nisi cupiditate labore eum nulla placeat eaque consequatur tempora corrupti iusto voluptatibus dolores eos, ipsum ratione rem accusantium praesentium expedita inventore perspiciatis quasi, explicabo vitae voluptas. Ab, debitis!
                        </p>
                        <p class="whychoice__facts-text">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facere eius perferendis dolor, reprehenderit doloremque blanditiis?
                        </p>
                    </div>
                    <div class="whychoice__facts-facts-1">
                        <h2 class="whychoice__facts-main__text">
                            Lorem ipsum dolor sit amet.
                        </h2>
                        <p class="whychoice__facts-text">
                            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ea repellat voluptatem excepturi accusamus molestiae assumenda doloremque obcaecati eius! Illo, atque?
                        </p>
                        <p class="whychoice__facts-text_">
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Cumque nesciunt ullam quia molestiae!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="about">
        <div class="container">
            <div class="about__inner">
                <div class="about__info">
                    <div class="about__info-main">
                        <!-- <a href="">Страны</a> -->
                    </div>
                    <div class="about__info-text">
                        <a href="">Беларусь</a>
                        <br>
                        <a href="">Польша</a>
                        <br>
                        <a href="">Литва</a>
                        <br>
                        <a href="">Казахстан</a>
                        <br>
                        <a href="">Германия</a>
                        <br>
                        <a href="">Армения</a>
                    </div>
                </div>
                <div class="about__info">
                    <div class="about__info-main">
                        <!-- <a href="">Расписание</a> -->
                    </div>
                    <div class="about__info-text">
                        <a href="">Краснодар</a>
                        <br>
                        <a href="">Москва</a>
                        <br>
                        <a href="">Нижний Новгород</a>
                        <br>
                        <a href="">Ростов-на-Дону</a>
                        <br>
                        <a href="">Санкт-Петербург</a>
                        <br>
                        <a href="">Волгоград</a>
                    </div>
                </div>
                <div class="about__info">
                    <div class="about__info-main">
                        <!-- <a href="">Международные</a> -->
                    </div>
                    <div class="about__info-text">
                        <a href="">Москва → Минск</a>
                        <br>
                        <a href="">Минск → Москва</a>
                        <br>
                        <a href="">Москва → Тблиси</a>
                        <br>
                        <a href="">Москва → Ереван</a>
                        <br>
                        <a href="">Москва → Берлин</a>
                        <br>
                        <a href="">Москва → Вильнюс</a>
                        <br>
                    </div>
                </div>
                <div class="about__info">
                    <div class="about__info-main">
                        <!-- <a href="">Внутренние</a> -->
                    </div>
                    <div class="about__info-text">
                        <a href="">Москва → Санкт-Петербург</a>
                        <br>
                        <a href="">Санкт-Петербург → Москва</a>
                        <br>
                        <a href="">Москва → Ростов-на-Дону</a>
                        <br>
                        <a href="">Москва → Пенза</a>
                        <br>
                        <a href="">Москва → Чебоксары</a>
                        <br>
                        <a href="">Москва → Биробиджан</a>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <hr class="bef__footer">
    <Footer/>
</template>

<style>
    /* @import url('https://fonts.gstatic.com'); */
    @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet');
    .hint{
        list-style-type: none;
        max-height: 242px;
        width: 400px;
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
            min-width: 100%;
            max-width: 100%;
        }
    }
</style>