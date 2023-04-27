<template>
            <div class="row row-mobil">
                <div class="col-sm-6 possible-block">
                    <div class="login-head"><h2>Войти</h2><span class="head-link" @click="$emit('registrationSection')">&nbsp;Регистрация</span></div>
                    <p class="possible">В личном кабинете вы можете:</p>
                    <ul class="all-possible">
                        <li class="possible-item">Выкупить забронированный билет</li>
                        <li class="possible-item">Посмотреть информацию о рейсе</li>
                        <li class="possible-item">Скачать купленный билет</li>
                        <li class="possible-item">Вернуть билет</li>
                        <li class="possible-item">Оставить отзыв о поездке</li>
                    </ul>
                </div>
                <div class="col-sm-6 block-form" >
                    <div class="tel" >
                        <div v-if="wrongCredentialsMessage" class="alert alert-danger" role="alert">
                            {{ wrongCredentialsMessage }}
                        </div>
                        <label for="tel" class="form-label label-gray">Телефон</label>
                        <input @focus="$emit('putRedFromLoginAway')" type="text" class="form-control inp-gray phone__input" v-model="user.phone" maxlength="17" id="tel">
                        <label for="tel" class="form-label label-gray">Пароль</label>
                        <input @focus="$emit('putRedFromLoginAway')" type="password" class="form-control inp-gray" v-model="user.password" id="pas">
                        <button @click="login" class="btn btn-primary btn-code" >Войти</button>
                        <p style="margin-top: 10px;" class="possible">Забыли пароль? Можете его <span class="head-link" @click="$emit('resetSection')">&nbsp;сбросить</span></p>
                        <div v-if="loginLoading" class="text-center" style="margin-top: 10px;">
                            <div class="spinner-border" role="status"></div>
                        </div>
                    </div>
                 </div>
            </div>
</template>
<script>
import axios from 'axios';
import router from '../router'
import axiosClient from '../axios'

export default {
    emits: ["log", 'authenticateForForm', 'putRedFromLoginAway', 'authSelf', 'resetSection', 'registrationSection'], 
    data() {
        return {
            user: {
                phone: '',
                password: '',
            },
            wrongCredentialsMessage: '',
            loginLoading: false
        };
    },
    mounted(){
        [].forEach.call(document.querySelectorAll(".phone__input"), function (input) {
            var keyCode;
            function mask(event) {
            event.keyCode && (keyCode = event.keyCode);
            var pos = this.selectionStart;
            if (pos < 3) event.preventDefault();
            var matrix = "+7 (___) ___ ____",
                i = 0,
                def = matrix.replace(/\D/g, ""),
                val = this.value.replace(/\D/g, ""),
                new_value = matrix.replace(/[_\d]/g, function (a) {
                return i < val.length ? val.charAt(i++) || def.charAt(i) : a;
                });
            i = new_value.indexOf("_");
            if (i != -1) {
                i < 5 && (i = 3);
                new_value = new_value.slice(0, i);
            }
            var reg = matrix
                .substr(0, this.value.length)
                .replace(/_+/g, function (a) {
                return "\\d{1," + a.length + "}";
                })
                .replace(/[+()]/g, "\\$&");
            reg = new RegExp("^" + reg + "$");
            if (
                !reg.test(this.value) ||
                this.value.length < 5 ||
                (keyCode > 47 && keyCode < 58)
            )
                this.value = new_value;
            if (event.type == "blur" && this.value.length < 5) this.value = "";
            }
            input.addEventListener("input", mask, false);
            input.addEventListener("focus", mask, false);
            input.addEventListener("blur", mask, false);
            input.addEventListener("keydown", mask, false);
        });
    },
    methods: {
        async login(){
            this.loginLoading = true
            this.wrongCredentialsMessage = ''
            const promise = axiosClient
            .post('/login', this.user)
            .then(response => {
                localStorage.setItem('authToken', response.data.token)
                this.$emit('authSelf');
                this.$emit('authenticateForForm');
            })
            .catch(error => {
                if(error.response.status == 422){
                    this.wrongCredentialsMessage = error.response.data.message
                }
            })
            await promise
            this.loginLoading = false
        }
    }
};
</script>