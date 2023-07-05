<template>
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
                        <!-- <a href="">Блог</a>
                        <br> -->
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
                        <a :href="baseUrl+'/agreement/offercontract.pdf'">Договор оферты</a>
                        <br>
                        <a :href="baseUrl+'/agreement/privacypolicy.pdf'">Политика конфиденциальности</a>
                    </div>
                </div>
                <div class="footer__info-about"></div>
            </div>
            <div class="cookies__text">
                <p>Мы используем информацию, зарегистрированную в <a :href="baseUrl+'/agreement/privacypolicy.pdf'">файлах «cookies»</a>, в частности, в рекламных и статистических целях, а также для того, чтобы адаптировать наши сайты к индивидуальным потребностям Пользователей. Вы можете изменить настройки касающиеся «cookies» в вашем браузере. Изменение настроек может ограничить функциональность сайта.</p>
            </div>            
        </div>
    </footer>

    <div class="copyright">
        <div class="container">
            <div class="copyright__text">
                <p>© 2023, OOO АЛЬТЕРНАТИВА. Все права защищены. АЛЬТЕРНАТИВА — Онлайн сервис продажи билетов</p>
            </div>
        </div>
    </div>
</template>

<script>
import axiosClient from '../axios';
	export default{
		data(){
			return{
				baseUrl: ''
			}
		},
    computed:{

    },
    methods: {

    },
    mounted(){
		this.baseUrl = import.meta.env.VITE_API_BASE_URL
    }
	}
</script>