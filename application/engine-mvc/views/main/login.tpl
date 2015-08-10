{* + ../header.tpl *}
<br>
<div class="ui grid">
  <div class="five wide column"></div>
  <div class="six wide column">

    <div class="ui green segment">
      <center>Чтобы войти в админ панель - авторизируйтесь</center>
    </div>
    <div class="ui center aligned green segment">
      <h4 class="ui header">Авторизация</h4>
      <div class="ui divider"></div>
      <h3>Эл. почта</h3>
      <div class="ui input focus">
        <input type="text" placeholder="mail@mail.ru">
      </div>

      <h3>Пароль</h3>
      <div class="ui input focus">
        <input type="password" placeholder="*********">
      </div>
      <br><br>
      <button class="ui green basic button">Войти</button>
    </div>
    <div class="ui green segment">
      <center>Для восстановления пароля перейдите по <a href="{*=ENGINE_URL*}/system/main/recovery">ссылке</a></center>
    </div>
  </div>
  <div class="five wide column"></div>
</div>
