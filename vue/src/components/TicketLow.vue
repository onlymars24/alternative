<template>
      <div class="menu__ticket-low">
            <div class="ticket-low__ins">
              <div class="ins-down-second__ticket-second">
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
