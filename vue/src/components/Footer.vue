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
                        <a href="/faq">Вопросы и ответы</a>
                        <br>
                        <a href="/return/conditions">Условия возврата</a>
                        <br>
                        <a href="" @click.prevent="openFeedbackWindow = true">Контакты</a>
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
                <div style="margin: 20px 0;" class="footer__info-about">
                    <!-- <div id="vk_groups"></div>
                    <div id='vk_community_messages'></div> -->
                </div>
            </div>
            <div class="copyright__text">
                <p>© 2023, OOO АЛЬТЕРНАТИВА. Все права защищены. АЛЬТЕРНАТИВА — Онлайн сервис продажи билетов</p>
                <p>
                    ИНН 7017479680<br/>
                    КПП 701701001<br/>
                    ОГРН 1207000012526<br/>
                    634049, г. Томск, Иркутский тр-т, д. 27, к. 2, оф. 17<br/>
                    <a href="mailto:info@rosvokzaly.ru">info@rosvokzaly.ru</a>
                </p>                
            </div>           
            <div class="cookies__text">
                <p>Мы используем информацию, зарегистрированную в <a :href="baseUrl+'/agreement/privacypolicy.pdf'" target="_blank">файлах «cookies»</a>, в частности, в рекламных и статистических целях, а также для того, чтобы адаптировать наши сайты к индивидуальным потребностям Пользователей. Вы можете изменить настройки касающиеся «cookies» в вашем браузере. Изменение настроек может ограничить функциональность сайта.</p>
            </div>   
                     
        </div>
        
        <PopupWindow v-if="openFeedbackWindow" @CloseFeedbackWindow="openFeedbackWindow = false" :content="7"/>
    </footer>

    <!-- <div class="copyright">
        <div class="container">

        </div>
    </div> -->
</template>

<script>
import axiosClient from '../axios';
import PopupWindow from '../components/PopupWindow.vue';
	export default{
        components: { PopupWindow, },
		data(){
			return{
				baseUrl: '',
                openFeedbackWindow: false,
                groupID: 223652237,
			}
		},
    computed:{

    },
    methods: {

    },
    async mounted(){
		this.baseUrl = import.meta.env.VITE_API_BASE_URL

        try {
            // VK.Widgets.CommunityMessages("vk_community_messages", this.groupID, {
            //     tooltipButtonText: "Есть вопрос?",
            //     expanded: "0",
            //     widgetPosition: "left"
            // });

            //Подключение виджета сообщества
            // VK.init({
            //     apiId: this.groupID,
            //     onlyWidgets: true
            // });
            // VK.Widgets.Group("vk_groups", 
            //     {
            //         mode: 3, color1: "FFFFFF", color2: "000000", color3: "5181B8"
            //     }, 
            //     this.groupID
            // );            
        }
        catch (e) {
            // console.log(e)
        }
    }
	}
</script>