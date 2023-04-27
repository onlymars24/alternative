<template>
    <div class="accordion" v-for="Accord in AccordItem" :key="Accord.id">
      <input class="input-accord" type="checkbox" name="radio-a" :id="Accord.id">
      <label class="accordion-label" :for="Accord.id">{{ Accord.header }}</label>
      <div class="accordion-content">
        <p>{{ Accord.text }}</p>
      </div>
  </div>
</template>
<script>
export default
{
  props: [ 'AccordItem' ],
}
</script>