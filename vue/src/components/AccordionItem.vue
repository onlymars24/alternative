<template>
    <div @click="Accord.open = !Accord.open" class="accordion" v-for="Accord in AccordItem" :key="Accord.id">
      <label class="accordion-label">{{ Accord.header }}</label>
      <Transition name="animation">
        <div class="accordion-content" v-if="Accord.open">
          <p>{{ Accord.text }}</p>
        </div>
      </Transition>
    </div>
</template>
<script>
export default
{
  props: [ 'AccordItem' ],
}
</script>