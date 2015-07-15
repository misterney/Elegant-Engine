<div class="ui stacked segment">
	<a class="ui ribbon teal label">Авторизация / Регистрация</a>
	<center><br>
	<form class="ui form" id="AuthForm">
		<div class="field">
         		<div class="ui labeled input">
				<div class="ui left icon input">
					<input type="text" placeholder="Ваш Логин" name="login">
					<i class="user icon"></i>
				</div>
			</div>
		</div>

		<div class="field">
         		<div class="ui labeled input">
				<div class="ui left icon input">
					<input type="password" placeholder="Ваш Пароль" name="password">
					<i class="lock icon"></i>
				</div>
			</div>
		</div>
		<div class="ui tiny buttons">
			<input type="button" value="Войти" class="ui teal button tiny" onclick="Authentication();">
			<div class="ui tiny green button" onclick="window.location='<?=ENGINE_URL?>/main/register';">Создать аккаунт</div>
		</div>
		<small><div id="auth_notice"></div></small>
	</form>
	<div id="AuthMessage"></div>
</center>
</div> 