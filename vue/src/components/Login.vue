<template>
    <div class="login-block">
            <a class="go-back" href="">&#8592; Вернуться назад</a>
            <div class="login-head"><h2>Войти</h2><span class="head-link" @click="$emit('log')">&nbsp;Регистрация</span></div>
            <div class="row row-mobil">
                <div class="col-sm-6 possible-block">
                    <p class="possible">В личном кабинете вы можете:</p>
                    <ul class="all-possible">
                        <li class="possible-item">Выкупить забронированный билет</li>
                        <li class="possible-item">Посмотреть информацию о рейсе</li>
                        <li class="possible-item">Скачать купленный билет</li>
                        <li class="possible-item">Вернуть билет</li>
                        <li class="possible-item">Оставить отзыв о поездке</li>
                    </ul>
                </div>
                <form action="" class="col-sm-6 block-form" >
                    <div class="tel" >
                        <label for="tel" class="form-label label-gray">Телефон</label>
                        <input type="text" class="form-control inp-gray" value="+7" id="tel">
                        <label for="tel" class="form-label label-gray">Пароль</label>
                      <input type="password" class="form-control inp-gray" id="pas">
                        <button class="btn btn-primary btn-code" >Войти</button>
                    </div>
                 </form>
            </div>
        </div>
</template>
<script>
export default {
data() {
return {
};
},
};
</script>