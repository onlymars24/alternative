<script setup>
</script>

<template>
  
  <router-view></router-view>

</template>

<style>
*{
  box-sizing: border-box;
}
:root{
  --blue: #2196F3;
}
.blue__link
{
  color: var(--blue);
  cursor: pointer;
}
body
{
  background-color: #f5f5f5 !important;
}
.but-go
{
    max-width: 350px;
    width: 100%;
    height: 50px;
    background-color: var(--blue);
    border-radius: 30px;
    color: white;
    font-weight: bold;
    font-size: 20px;
    margin-top: 40px;
}
/* headerCrumbs */
</style>
