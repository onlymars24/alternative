<template>
      <div class="menu__ticket-low">
            <div class="ticket-low__ins">
              <!-- <div class="ticket-low__ins-up">
<a href="">Маршрут</a>
<a href="">Перевозчик</a>
<a href="">Автобус</a>
<a href="">Условия</a>
</div> -->
              <div class="ins-down-second__ticket-second">
                <!-- <p>Бренд: ИП Пыльнов Игорь Анатольевич</p> -->
                <!-- <p>Перевозчик: ИП Пыльнов Игорь Анатольевич</p> -->
                <!-- <p>Адрес: Россия, Ставропольский кр., г. Ставрополь</p> -->
                <!-- <p>ОГРН: 311265129400063</p> -->
                <!-- <p>Время работы: Пн-Пт 10:00-17:00 (по местному времени)</p> -->
                <p>Организация перевозчика: {{ race.carrier }}</p>
                <p>{{ race.carrierInn }}</p>
                <p>{{ race.carrierPhone }}</p>
              </div>
            </div>
          </div>
</template>
<script>
export default
{
  props: ['race'],
};
</script>
