<template>
  
          <div class="login-head"><h2>Регистрация</h2><span class="head-link" @click="$emit('log')">&nbsp;Войти</span></div>
          
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
                  <div class="tel" v-if="stepLog==1">
                      <label for="tel" class="form-label label-gray">Телефон</label>
                      <input type="text" class="form-control inp-gray" value="+7" id="tel">
                      <label for="tel" class="form-label label-gray">Пароль</label>
                      <input type="password" class="form-control inp-gray" id="pas">
                      <label for="tel" class="form-label label-gray">Повторите пароль</label>
                      <input type="password" class="form-control inp-gray" id="pas">
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