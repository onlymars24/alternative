<!-- eslint-disable vuejs-accessibility/click-events-have-key-events -->
<!-- eslint-disable max-len -->
<template>
<div class="background-close" @click="$emit('CloseWindow')"></div>
<div class="popup-container">
    <div class="closeWindow" @click="$emit('CloseWindow')">✖</div>
    <!-- <Seat v-if="this.content==1"></Seat> -->
    <div v-if="this.content==2">{{ UserAgreement }}</div>
</div>
</template>
<script scoped>
import UserAgreement from '../data/UserAgreement';
import Seat from '../components/Seat.vue';

export default
{
  props: ['content'],
  components: { Seat },
  computed: {
    UserAgreement() {
      return UserAgreement;
    },
  },
};
</script>
<style>

.popup-container
{
    position: fixed;
    top: 50%;
     left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    background-color: white;
    max-width: 800px;
    width: 100%;
    padding: 20px;
    z-index: 10;
    border-radius: 20px;
    max-height: 80vh;
    overflow: auto;
}
.popup-container::-webkit-scrollbar {
  width: 0;
}
.closeWindow
{
    cursor: pointer;
    color: var(--blue);
    position: relative;
    text-align: end;
}
.background-close
{
    position: fixed;
    top: 0px;
    left: 0px;
 z-index: 9;
 width: 100vw;
 height: 100vh;
 background-color: rgba(0, 0, 0, 0.521);
}
</style>
