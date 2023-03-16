<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable max-len -->
<!-- eslint-disable vue/multi-word-component-names -->
<template>
      <div class="discr-bus">
        <h5>Выберите место на схеме автобуса</h5>
        <p>
          Автобус: Автобус 51 место (Баг:102)
        </p>
        <span class="gfg_tooltip"
            >!<span class="gfg_text"
              >Перевозчик имеет право заменить транспортное средство, в случае его не исправности,
              аварии и других аналогичных ситуациях.</span
            ></span
          >
        <div class="block-info-seat">
          <p><span class="seat-item example  " ></span> - свободно</p>
          <p><span class="example seat-item seat-item-busy "></span> - занято</p>
        </div>
      </div>

      <div class="center-bus-body">
        <div class="bus-body">
          <div class="all-seat">
            <div class="line-seat">
            <button class="seat-item">11</button>
            <button class="seat-item">2</button>
            <button class="seat-item seat-item-none-item">3</button>
            <button class="seat-item seat-item-none-item">4</button>
            <button class="seat-item">5</button>
            <button class="seat-item">6</button>
            <button class="seat-item">7</button>
            <button class="seat-item">8</button>
            </div>
            <div class="line-seat">
            <button class="seat-item">21</button>
            <button class="seat-item">2</button>
            <button class="seat-item">3</button>
            <button class="seat-item ">4</button>
            <button class="seat-item">5</button>
            <button class="seat-item">6</button>
            <button class="seat-item">7</button>
            <button class="seat-item">8</button>
            </div>
            <div class="line-seat">
            <button class="seat-item">31</button>
            <button class="seat-item">2</button>
            <button class="seat-item seat-item-none-item">3</button>
            <button class="seat-item seat-item-none-item">4</button>
            <button class="seat-item">5</button>
            <button class="seat-item">6</button>
            <button class="seat-item">7</button>
            <button class="seat-item">8</button>
            </div>
            <div class="line-seat">
            <button class="seat-item">31</button>
            <button class="seat-item">2</button>
            <button class="seat-item seat-item-none-item">3</button>
            <button class="seat-item seat-item-none-item">4</button>
            <button class="seat-item">5</button>
            <button class="seat-item">6</button>
            <button class="seat-item">7</button>
            <button class="seat-item">8</button>
            </div>
            <div class="line-seat">
            <button class="seat-item">31</button>
            <button class="seat-item">2</button>
            <button class="seat-item ">3</button>
            <button class="seat-item ">4</button>
            <button class="seat-item">5</button>
            <button class="seat-item">6</button>
            <button class="seat-item">7</button>
            <button class="seat-item">8</button>
            </div>
          </div>
        </div>
      </div>
      <button class="but-go">Продолжить</button>
</template>
<style>
body {
  background-color: #f5f5f5;
}
.gfg_tooltip {
  position: relative;
  display: inline-block;
  color: black;
  height: 25px;
  width: 25px;
  text-align: center;
  display: inline-block;
  font-size: 16px;
  border-radius: 50%;
  border: 1px solid rgb(196, 196, 196);
  font-weight: bold;
  color: rgb(196, 196, 196);
}
.gfg_tooltip .gfg_text {
  visibility: hidden;
  width: 300px;
  background-color: rgba(49, 49, 49, 0.808);
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  font-size: 14px;
  z-index: 1;
  top: -100px;
  left: -140px;
}
.example
{
    display: inline-block;
    width: 20px !important;
    height: 20px !important;
    border-radius: 5px;
    position: relative;
    top: 10px;
    margin-left: 10px !important;
}
.gfg_tooltip:hover .gfg_text {
  visibility: visible;
}
.discr-bus {
  text-align: center;
}
.block-info-seat {
  display: flex;
  justify-content: center;
}
.bus-body {
  border: 4px solid rgb(204, 204, 204);
  display: inline-block;
  border-radius: 20px;
  padding-right: 40px;
  white-space: nowrap;
  position: relative;
  margin: 0px auto;
  background-color: #fff;
}
.bus-body::after {
  content: "";
  width: 30px;
  height: 30px;
  border: 3px solid rgb(184, 184, 184);
  border-radius: 50%;
  position: absolute;
  right: 2px;
  top: 20px;
}
.seat-item {
  width: 35px;
  height: 35px;
  border: 2px solid var(--blue);
  border-radius: 5px;
  display: inline-block;
  margin: 4px;
  color: var(--blue);
}
.seat-item-busy {
  border: 2px solid rgb(161, 161, 161);
  color: rgb(161, 161, 161);
}
.seat-item-none-item {
  opacity: 0;
}
.seat-active {
  color: white;
  background-color: var(--blue);
}
.all-seat {
    display: flex;
    flex-direction: column;
  padding: 10px;
  height: 100%;
}
.center-bus-body {
  display: flex;
  justify-content: center;
}
.window-bus {
  margin: 0px auto;
  width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
  background-color: #fff;
  padding: 100px;
  border-radius: 30px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 15%);
}
.line-seat
{
    display: flex;
    flex-direction: row;
}
@media (max-width: 700px) {
.bus-body
{
    padding: 40px 0px 0px 0px;
}
.bus-body::after
{
    top: 5px;
    left: 20px;
}
  .line-seat
{
    display: flex;
    flex-direction: column-reverse;
}
.all-seat
{
    flex-direction: row;
}

  .window-bus {
    padding: 10px;
  }
}
</style>
