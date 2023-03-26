<template>
  <div>
    <ul>
      <li class="person-window">
        <div class="person-btn">
          <div>Зубенко Михаил Петрович</div>
          <div class="person__all-button">
            <button class="btn btn-secondary" @click="openInputs=true">Редактировать</button>
            <button class="btn btn-danger">Удалить</button>
          </div>
        </div>
        <Transition name="animation">
          <form action="" v-if="openInputs">
          <div class="form__all-input">
         
          <div class="left-input all-input-item">
            <label for="">Фамилия</label>
            <!--  -->
            <input
              type="text"
              class="form-control is-invalid"
              name="name"
              id="inputName"
              placeholder="Иванов"
              maxlength="60"
              required
            />
            <!--  -->
            <!--  -->
            <label for="">Имя</label>
            <input
              type="text"
              class="form-control is-invalid"
              name="name"
              id="inputName"
              placeholder="Иван"
              maxlength="60"
             
              required
            />
            <!--  -->
            <!--  -->
            <label for="">Отчество</label>
            <input
              type="text"
              class="form-control is-invalid"
              name="name"
              id="inputName"
              placeholder="Иванович"
              maxlength="60"
              
              required
            />
            <!--  -->
            <!--  -->
            <label for="inputRegion" class="form-label">Пол</label>
            <select
              class="form-select form-control"
              name="region"
              id="inputRegion"
              maxlength="60"
              
              required
            >
              <option data-gender="M" value="Мужчина">Мужчина</option>
              <option data-gender="F" value="Женщина">Женщина</option>
            </select>
            <!--  -->
            <!--  -->
            <label for="">Дата рождения</label>
            <input
              type="date"
              class="form-control is-invalid"
              name="name"
              id="inputName"
              placeholder="Иван"
              maxlength="60"
              
              required
            />
            <!--  -->
          </div>
          <div class="right-input all-input-item">
            <!--  -->
            <label for="inputRegion" class="form-label">Документы</label>
            <select
              class="form-select form-control"
              name="region"
              id="inputRegion"
              maxlength="60"
              
              required
            >
              <option
             
              >
                
              </option>
            </select>
            <!--  -->
            <!--  -->
            <label for="inputRegion" class="form-label">Тип билета</label>
            <select
              class="form-select form-control"
              name="region"
              id="inputRegion"
              maxlength="60"
              
              required
            >
              <option
                
                
               
              >
                
              </option>
            </select>
            <!--  -->
            <!--  -->
            <label for="inputRegion" class="form-label">Гражданство</label>
            <select
              class="form-select form-control"
              name="region"
              id="inputRegion"
              maxlength="60"
             
              required
            >
              <option
              
              >
            
              </option>
            </select>
            <!--  -->
            <!--  -->
            <label for="inputdoc" class="form-label">Номер документа</label>
            <input
              type="text"
              class="form-control"
              name="doc"
              id="inputdoc"
              maxlength="10"
              
              required
              placeholder="серия и номер: 10 цифр"
            />
            <!--  -->
            <!--  -->
            <label for="inputdoc" class="form-label">Серия документа</label>
            <input
              type="text"
              class="form-control"
              name="dc"
              id="inputdoc"
              maxlength="10"
              required
              
              placeholder="серия и номер: 10 цифр"
            />
          </div>
          
          </div>
          <button class="btn btn-primary" @click="openInputs=false">сохранить</button>
        </form>
        </Transition>
      </li>
    </ul>
    <div class="personal-account__content-empty">
        <h2> "У вас пока нет сохранённых пользователей" </h2>
        </div>
  </div>
</template>
<script>
export default
{
  data()
  {
    return{
      openInputs: false,
    }
  }
}
</script>
<style>
.person-window {

  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  margin-bottom: 25px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}
.person-btn
{
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.person__all-button *:nth-child(1)
{
  margin-right: 50px;
}
</style>
