<!-- eslint-disable max-len -->
<!-- eslint-disable vuejs-accessibility/label-has-for -->
<template>
<div>
    <h3>Контактная информация</h3>
    <div class="row g-5">
        <div class="col-sm-6 ">
            <div class="contact-information__block ">
                <p>На электронную почту высылаем маршрутные квитанции</p>
                <label for="" class="label-gray">E-mail</label>
                <input type="text" class="form-control inp-gray" placeholder="Не заполнено">
                <div class="filled-email">
                <a href=""> &#9998; Изменить</a> <a href="">Отменить</a> <a href="">Сохранить</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class=" contact-information__block">
                <p>С помощью телефона мы сможем связаться с вами,
                     в случае отмены, переноса рейса или в других критических случаях</p>
                <label for="" class="label-gray">Телефон</label>
                <input type="text" class="form-control inp-gray" >
            </div>
        </div>
    </div>
    </div>
</template>
<style>
.filled-email
{
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
</style>
