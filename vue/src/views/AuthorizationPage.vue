<!-- eslint-disable vuejs-accessibility/label-has-for -->
<!-- eslint-disable max-len -->
<template>
    <div class="container">
        <div class="login-container">
            <div class="login-block">
                <a class="go-back" href="">&#8592; Вернуться назад</a>
                <h2>Войти или зарегистрироваться</h2>
                <div class="row">
                    <div class="col-6 possible-block">
                        <p class="possible">В личном кабинете вы можете:</p>
                        <ul class="all-possible">
                            <li class="possible-item">Выкупить забронированный билет</li>
                            <li class="possible-item">Посмотреть информацию о рейсе</li>
                            <li class="possible-item">Скачать купленный билет</li>
                            <li class="possible-item">Вернуть билет</li>
                            <li class="possible-item">Оставить отзыв о поездке</li>
                        </ul>
                    </div>
                    <form action="" class="col-6 block-form" >
                        <div class="tel" v-if="stepLog==1">
                            <label for="tel" class="form-label label-gray">Телефон</label>
                            <input type="text" class="form-control inp-gray" value="+7" id="tel">
                            <button class="btn btn-primary btn-code" @click="stepLog=2" >Выслать код в СМС</button>
                        </div>
                        <div class="code" v-else-if="stepLog==2">
                            <p class="code__tel">Введите код, отправленный на номер <strong>+79034569869</strong></p>
                            <label for="tel" class="form-label label-gray">Код подтверждения</label>
                            <input type="text" class="form-control inp-gray"  id="code">
                            <span class="approval">Вы предоставляете и подтверждаете <a href="">согласие на обработку персональных данных.</a> </span>
                            <button class="btn btn-primary btn-code">Подтвердить</button>
                            <button class="btn-code btn__new-code">Повторное СМС</button>
                        </div>
                     </form>
                </div>
            </div>
        </div>
    </div>
    <div class="decor">
        <span class="bus-stop-decor"></span><span class="ticket-decor"></span>
    </div>
</template>
<script>
export default {
  data() {
    return {
      stepLog: 1,
    };
  },
};
</script>
<style scoped>
.bus-stop-decor
{
    background-image: url('/public/img/bus-stop.svg');
    background-position: left bottom;
    background-size: contain;
    background-repeat: no-repeat;
    height: 100%;
    width: 50%;
    margin-left: 160px;
    transform: scale(1.2);
}
.ticket-decor
{
    background-image: url('/public/img/ticket.svg');
    background-position: right bottom;
    background-size: contain;
    background-repeat: no-repeat;
    height: 100%;
    width: 50%;
    margin-right: 140px;
}
.decor
{
    display: flex;
    position: absolute;
    opacity: 0.1;
    top: 65vh;
    width: 100vw;
    height: 30vh;
}
.btn__new-code
{
    background-color: transparent;
}
.code__tel
{
    font-size: 14px;
}
.login-block
{
    position: fixed;
    top: 50%;
     left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    max-width: 800px;
    width: 100%;
    padding: 10px;
    z-index: 10;
    border-radius: 10px;
    max-height: 80vh;
    padding: 30px 50px 50px 50px;
    box-shadow: rgb(0 0 0 / 10%) 0px 20px 90px 0px;

}
.approval
{
    font-size: 14px;
}
.possible
{
    color: var(--gray);
    margin: 0px;
}
.possible-item
{
    position: relative;
    padding-left: 22px;
    margin: 0;
}

.go-back
{
    position: relative;
    top: -60px;
    right: 40px;
}
.btn-code
{
    width: 100%;
    padding: 12px;
}
.possible-item::before
{
    content: "";
    background-image: url('/public/img/check.svg');
    height: 20px;
    width: 20px;
    position: absolute;
    background-position: center center;
    display: block;
    background-size: contain;
    left: 0px;

}
@media (max-width: 1000px)
{
    .decor
    {
        display: none;
    }
}
@media (max-width: 768px)
{
    .possible-block
    {
        font-size: 13px;
    }
}
@media (max-width: 580px) {
.possible-block
{
    position: absolute;
    top: 320px;
    display: block;
    width: 100%;
}
.block-form
{
    display: block;
    width: 100%;
}
}

</style>
