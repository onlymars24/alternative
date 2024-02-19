<script>
import Footer from '../components/Footer.vue'
import HeaderMain from '../components/HeaderMain.vue'
import axiosClient from '../axios';
// import Captcha from 'https://smartcaptcha.yandexcloud.net/captcha.js'

export default{
    name: "Main",
    components: {
        HeaderMain,
        Footer,
    },
    data() {
        return {
            drawer: false,
            size: 'default',
            value1: '',
            shortcuts: [
                {
                    text: 'Today',
                    value: new Date(),
                },
                {
                    text: 'Yesterday',
                    value: () => {
                    const date = new Date()
                    date.setTime(date.getTime() - 3600 * 1000 * 24)
                    return date
                    },
                },
                {
                    text: 'A week ago',
                    value: () => {
                    const date = new Date()
                    date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
                    return date
                    },
                },
            ],
            disabledDate: (Date) => {
            return time.getTime() > Date.now()
            },
            groupID: 223652237,
            content: ''
            // innerDrawer: false
        }
    },
    methods: {

    },
    watch: {
        
    },
    computed: {

    },
    async mounted() {
        const promise = axiosClient
        .get('/page/main')
        .then(response => {
            console.log(response)
            this.content = response.data.pageMain
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        console.log('this.$route.query')

        // this.$router.push({ name: 'Main', query: { id: 456, name: 'Jane' }})
        // console.log(this.$route.query.name)
        // Подключение виджета сообщений

        VK.Widgets.CommunityMessages("vk_community_messages", this.groupID, {
            tooltipButtonText: "Есть вопрос?",
            expanded: "0",
            widgetPosition: "left"
        });

        // Подключение виджета сообщества

        VK.init({
            apiId: this.groupID,
            onlyWidgets: true
        });
        VK.Widgets.Group("vk_groups", 
            {
                mode: 3, color1: "FFFFFF", color2: "000000", color3: "5181B8"
            }, 
            this.groupID
        );
    }
    // created() {
    //   document.title = 'Главная';
    // }
}
</script>

<template>
    <!-- <div
    id="captcha-container"
    class="smart-captcha"
    data-sitekey="ysc1_RpgYw52BqBtzs8l44h84Uw8HjdhDBEeaPzDoQ772da43ab9e"
></div> -->
    <!-- <button style="margin-left: 16px" @click="drawer = true">
        open
    </button>

    <el-drawer v-model="drawer" title="I am the title" :with-header="false">
        <span>Hi there!</span>
    </el-drawer>
    <div class="block">
      <el-date-picker
        v-model="value1"
        type="date"
        placeholder="Pick a day"
        :size="size"
      />
    </div> -->
    <HeaderMain :isRaces="false"/>
    <!-- <div class="choice">
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
    </div> -->

    <div class="about">
        <div class="container">
            
            <div class="about__inner">
                <div v-html="content"></div>
                <!-- <div class="about__info">
                    <div class="about__info-main">
                    </div>
                    <div class="about__info-text">
                        <a target="_blank" href="/avtobus/Томск/Новосибирск/">Томск → Новосибирск</a>
                        <br>
                        <a target="_blank" href="/avtobus/Томск/Барнаул/">Томск → Барнаул</a>
                        <br>
                        <a target="_blank" href="/avtobus/Томск/Кемерово/">Томск → Кемерово</a>
                        <br>
                        <a target="_blank" href="/avtobus/Томск/Алейск/">Томск → Алейск</a>
                        <br>
                        <a target="_blank" href="/avtobus/Томск/Белово/">Томск → Белово</a>
                        <br>
                        <a target="_blank" href="/avtobus/Томск/Казачий/">Томск → Казачий</a>
                        <br>
                        <a target="_blank" href="/avtobus/Красноярск/Антропово/">Красноярск → Антропово</a>
                        <br>
                        <a target="_blank" href="/avtobus/Красноярск/Абан/">Красноярск → Абан</a>
                        <br>
                        <a target="_blank" href="/avtobus/Красноярск/Абаза/">Красноярск → Абаза</a>
                        <br>
                    </div>
                </div>
                <div class="about__info">
                    <div class="about__info-main">
                        
                    </div>
                    <div class="about__info-text">
                        <a target="_blank" href="/avtobus/Кемерово/Новосибирск/">Кемерово → Новосибирск</a>
                        <br>
                        <a target="_blank" href="/avtobus/Кемерово/Сокур/">Кемерово → Сокур</a>
                        <br>
                        <a target="_blank" href="/avtobus/Кемерово/Вороново/">Кемерово → Вороново</a>
                        <br>
                        <a target="_blank" href="/avtobus/Кемерово/Березовка/">Кемерово → Березовка</a>
                        <br>
                        <a target="_blank" href="/avtobus/Кемерово/Новая Балахонка/">Кемерово → Новая Балахонка</a>
                        <br>
                        <a target="_blank" href="/avtobus/Кемерово/Барановский/">Кемерово → Барановский</a>
                        <br>
                        <a target="_blank" href="/avtobus/Красноярск/Бакчет/">Красноярск → Бакчет</a>
                        <br>
                        <a target="_blank" href="/avtobus/Красноярск/Агинское/">Красноярск → Агинское</a>
                        <br>
                        <a target="_blank" href="/avtobus/Красноярск/Томск/">Красноярск → Томск</a>
                        <br>
                    </div>
                </div> -->

            <div class="about__info">
                <div class="about__info-main">
                    <!-- <a href="">Внутренние</a> -->
                </div>
                <div id='vk_community_messages'></div>
                <div class="about__info-text">
                    <div id="vk_groups"></div> 
                </div>
            </div>
            </div>

        </div>
    </div>
    
    <hr class="bef__footer">
    <Footer/>
</template>

<!-- <script>
import axiosClient from '../axios';

export default {
  data(){
    return {
      race: [],
      content: '',
      loading: true,
      content: ''
    }
  },
  async mounted(){
    const promise = axiosClient
    .get('/page/main')
    .then(response => {
        console.log(response)
    })
    .catch(error => {
        console.log(error)
    })
    await promise
  },
  methods: {

  }

}
</script> -->

<style>
    /* @import url('https://fonts.gstatic.com'); */
    @import url('https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet');
    .hint{
        z-index: 2;
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