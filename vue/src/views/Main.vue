<script>
export default{
    name: "Main",
    data() {
        return {
            dispatchPointEmpty: false,
            dispatchPointFilled: false,
            arrivalPointEmpty: false,
            arrivalPointFilled: false,
            arrivalText: '',
            dispatchText: '',
            arrivalData: {
                
            },
            dispatchData: {

            },
            arrivalEl: {
                
            },
            dispatchEl: {
                id: null,
                text: 
            },
        }
    },
    methods: {
        dispatchFocus(){
            if(!dispatchText){
                this.dispatchPointEmpty = true
                this.dispatchPointFilled = false
            }
            else{
                this.dispatchPointEmpty = false
                this.dispatchPointFilled = true
            }
        },
        blur(){
            this.dispatchPointEmpty = this.dispatchPointFilled = this.arrivalPointEmpty = this.arrivalPointFilled = false
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
        }
    }
}
</script>

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
                    Билеты на автобус онлайн {{test123}}
                </p>
            </div>
            
            <div class="main__table">
                <div class="main__table-table">
                    <ul v-if="dispatchPointEmpty" class="hint">
                        <li class="hint__title">
                            <span>Популярные направления:</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                    </ul>
                    <ul v-if="dispatchPointFilled" class="hint">
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                    </ul>
                    <p class="">Откуда</p>
                    <input @focus="dispatchFocus" @blur="blur" v-model="dispatchText" class="main__table-input-1" type="text">
                </div>
                <div class="main__table-table">
                    <ul v-if="arrivalPointEmpty" class="hint">
                        <li class="hint__title">
                            <span>Популярные направления:</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                    </ul>
                    <ul v-if="arrivalPointFilled" class="hint">
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                        <li>
                            <strong>Краснодар, </strong>
                            <span>какой-то там район, какая-то станция</span>
                        </li>
                    </ul>
                    <p class="">Куда</p>
                    <input v-model="arrivalText" disabled class="main__table-input" list="OWNER" placeholder="Укажите Откуда" type="text">
                </div>
                <div class="main__table-table">
                    <p class="">Дата поездки</p>
                    <input class="main__table-date" type="date">
                </div>
                <div class="main__table-table">
                    <p class="">Пассажиры</p>
                    <input class="main__table-input" type="text">
                </div>
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

    <footer class="footer">
        <div class="container">
            <div class="partners">
                <div class="partners__logo">
                
                </div>
                <div class="partners__logo">
                    
                </div>
                <div class="partners__logo">
                    
                </div>
                <div class="partners__logo">
                    
                </div>
                <div class="partners__logo">
                    
                </div>
                <div class="partners__logo">
                    
                </div>
                <div class="partners__logo">
                    
                </div>
            </div>
            <div class="footer__info">
                <div class="footer__info-about">
                    <div class="footer__info-about__main">
                        <p>Информация</p>
                    </div>
                    <div class="footer__info-about__pass">
                        <a href="">О нас</a>
                        <br>
                        <a href="">Вопросы и ответы</a>
                        <br>
                        <a href="">Вернуть билет</a>
                        <br>
                        <a href="">Блог</a>
                        <br>
                        <a href="">Контакты</a>
                    </div>
                </div>
                <div class="footer__info-about">
                    <div class="footer__info-about__main">
                        <p>Партнёрам</p>
                    </div>
                    <div class="footer__info-about__pass">
                        <a href="">Перевозчикам</a>
                        <br>
                        <a href="">Агентам</a>
                        <br>
                        <a href="">Партнерская программа</a>
                        <br>
                        <a href="">Автобусные перевозчики</a>
                    </div>
                </div>
                <div class="footer__info-about">
                    <div class="footer__info-about__main">
                        <p>Документы</p>
                    </div>
                    <div class="footer__info-about__pass">
                        <a href="">Договор оферты</a>
                        <br>
                        <a href="">Политика конфиденциальности</a>
                    </div>
                </div>
                <div class="footer__info-about"></div>
            </div>
            <div class="cookies__text">
                <p>Мы используем информацию, зарегистрированную в <a href="">файлах «cookies»</a>, в частности, в рекламных и статистических целях, а также для того, чтобы адаптировать наши сайты к индивидуальным потребностям Пользователей. Вы можете изменить настройки касающиеся «cookies» в вашем браузере. Изменение настроек может ограничить функциональность сайта.</p>
            </div>            
        </div>
    </footer>

    <div class="copyright">
        <div class="container">
            <div class="copyright__text">
                <p>© 2023, OOO «Lorem». Все права защищены. «Lorem» — Онлайн сервис продажи lorem'ов</p>
            </div>
        </div>
    </div>
</template>

<style>
    @import url('https://fonts.gstatic.com');
    @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet');
    .hint{
        list-style-type: none;
        height: 242px;
        width: 400px;
        overflow-y: scroll;
        background-color: #fff;
        position: absolute;
        top: 115%;
        -webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
     -moz-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
      -ms-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
       -o-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
      box-shadow: 0px 0px 10px rgba(0,0,0,.8);
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
</style>