<div class="ui stacked segment">
	<a class="ui ribbon teal label">Регистрация</a>
	<center><br>
	<form class="ui form" id="RegisterForm">
		<div class="ui middle aligned centered grid">
			<div class="six wide column">
					<label><strong>Придумайте никнейм</strong><br>От 3 до 16 символов</label>
			</div>
			<div class="ten wide column">
			<div class="field">
         			<div class="ui labeled input">
					<div class="ui left icon input">
						<input type="text" placeholder="Ваш Логин" name="login" id="login">
						<i class="user icon"></i>
					</div>
				</div>
			</div>
			</div>

			<div class="six wide column">
					<label><strong>Ваш пароль</strong><br>Будет использовать при входе</label>
			</div>
			<div class="ten wide column">
			<div class="field">
         			<div class="ui labeled input">
					<div class="ui left icon input">
						<input type="text" placeholder="Пример: <?=html::simplePass();?>" name="pass" id="pass">
						<i class="Lock icon"></i>
					</div>
				</div>
			</div>
			</div>


			<div class="six wide column">
					<label><strong>Повторите пароль</strong><br>Убедитесь, что все правильно</label>
			</div>
			<div class="ten wide column">
			<div class="field">
         			<div class="ui labeled input">
					<div class="ui left icon input">
						<input type="text" placeholder="Пример: <?=html::simplePass();?>" name="checkPass" id="checkPass">
						<i class="Lock icon"></i>
					</div>
				</div>
			</div>
			</div>


			<div class="six wide column">
					<label><strong>Ваша почта</strong><br>Сюда прийдет подтверждение</label>
			</div>
			<div class="ten wide column">
			<div class="field">
         			<div class="ui labeled input">
					<div class="ui left icon input">
						<input type="text" placeholder="Пример: example@mail.ru" name="mail" id="mail">
						<i class="mail icon"></i>
					</div>
				</div>
			</div>
			</div>

		</div>

 
		<br><br>

		<div class="ui tiny buttons">
			<div class="ui clear button">Очистить</div>
			<div class="ui teal button tiny" onclick="Registration();">Зарегистрироваться</div>
		</div>
	</form>

</center>
</div> 