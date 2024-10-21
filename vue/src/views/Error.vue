<template>
    
    <div style="height: 50vh; display: flex; justify-content: center; align-items: center;">
        <div class="container-center">
            <h1>Запрашиваемая страница не найдена</h1>
            <p>Возможно вы ошиблись адресом или страница перенесена. Чтобы найти подходящий рейс, воспользуйтесь формой поиска на главной странице.</p>
            <button @click="toMain" class="but-go">Перейти на главную</button>
            <div class="background"><h2>404</h2> </div>
        </div>
    </div>

</template>
<script>
import router from '../router'
export default
{
    methods: {
        toMain(){
            router.push({name: 'Main'})
        }
    }
};
</script>
<style>
body
{
  background-color: #f5f5f5 !important;
}
.container-center
{
    position: absolute;
    /* top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); */
    text-align: center;
    max-width: 800px;
    width: 100%;
    z-index: 10;
}
.background
{
    position: absolute;
    top: 50%;
     left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    max-width: 800px;
    width: 100%;
    z-index: -1;
    font-weight: bold;
    font-size: 400px;
    color: rgb(218, 218, 218);

}
.background h2
{
    font-size: 400px;
}
@media (max-width: 740px)
{
.background h2
{
    font-size: 300px;
}
}
@media (max-width: 400px)
{
.background h2
{
    font-size: 200px;

}
}
</style>
