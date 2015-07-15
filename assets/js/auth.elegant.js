function Authentication() {
	jQuery.ajax({
		url:     "../auth/login", //Адрес подгружаемой страницы
		type:     "POST", //Тип запроса
		dataType: "html", //Тип данных
		data: jQuery("#AuthForm").serialize(), 
		success: function(response) { //Если все нормально
			if(response == "ALL_GOOD"){
				document.getElementById("AuthMessage").innerHTML = "<div class='ui bottom attached success message' id='AuthMessage'>Успешно</div>";
				location.reload();
			}
			if(response == "NO_PASSWORD"){
				document.getElementById("AuthMessage").innerHTML = "<div class='ui bottom attached warning message' id='AuthMessage'>Не введен пароль</div>";
			}
			if(response == "NO_LOGIN"){
				document.getElementById("AuthMessage").innerHTML = "<div class='ui bottom attached warning message' id='AuthMessage'>Не введен логин</div>";
			}
			if(response == "NOT_VALID_LOGIN_OR_PASSWORD"){
				document.getElementById("AuthMessage").innerHTML = "<div class='ui bottom attached warning message' id='AuthMessage'>Неправильный логин или пароль</div>";
			}

		},
		error: function(response) { //Если ошибка
			document.getElementById("AuthMessage").innerHTML = "Ошибка при отправке формы";
		}
	});
};




$.fn.form.settings.rules.regex = function(value, regex) {
	var expr = new RegExp(regex);
	return expr.test(value);
}
$.fn.form.settings.rules.checkLogin = function(value) {
	
	var data = "login="+value+ "&type=checkLogin";
	var ajaxResponse;

	jQuery.ajax({
		url:     "../auth/info",
              async: false, 
		type:     "POST",
		dataType: "html",
		data: data, 
		success: function(response) {
			ajaxResponse = response;
		},
		error: function(response) {
			alert('Ошибка');
		}
	    });
	return ajaxResponse;
}

$.fn.form.settings.rules.checkMail = function(value) {
	
	var data = "mail="+value+ "&type=checkMail";
	var ajaxResponse;

	jQuery.ajax({
		url:     "../auth/info",
              async: false, 
		type:     "POST",
		dataType: "html",
		data: data, 
		success: function(response) {
			ajaxResponse = response;
		},
		error: function(response) {
			alert('Ошибка');
		}
	    });
	return ajaxResponse;
}



var validationRules = {    
      login: {
          identifier: 'login',
          rules: [
            {
              type : 'empty',
              prompt : 'Вы не ввели никнейм'
            },
            {
              type : 'regex[^[a-zA-Z0-9_-]+$]',
              prompt : 'В вашем нике присутсвуют недопустимые символы'
            },
            {
              type : 'length[3]',
              prompt : 'Ваш никнейм слишком короткий'
            },
            {
              type : 'maxLength[16]',
              prompt : 'Ваш никнейм слишком длинный'
            },
            {
              type : 'checkLogin',
              prompt : 'Такой никнейм уже занят :('
            }
          ]
        },       
        pass: {
          identifier: 'pass',
          rules: [
            {
              type : 'empty',
              prompt : 'Вы не ввели пароль'
            },
            {
              type : 'length[4]',
              prompt : 'Ваш пароль слишком простой (меньше 4 символов)'
            },
            {
              type : 'maxLength[32]',
              prompt : 'Ваш пароль слишком длинный (макс. 32 символа)'
            }
          ]
        },
        checkPass: {
          identifier: 'checkPass',
          rules: [
            {
              type : 'match[pass]',
              prompt : 'Пароли не совпадают'
            },
            {
              type : 'empty',
              prompt : 'Вы не повторили пароль'
            }
          ]
        },
        mail: {
          identifier: 'mail',
          rules: [
            {
              type : 'empty',
              prompt : 'Вы не ввели почту'
            },
            {
              type : 'regex[^([a-zA-Z0-9_\.-]+)@([a-zA-Z0-9_\.-]+)\.([a-zA-Z\.]{2,6})$]',
              prompt : 'Неправильная электронная почта'
            },
            {
              type : 'checkMail',
              prompt : 'Такая почта уже существует :('
            }

          ]
        },
      };


   var extendRules = {
        on: 'blur',
        inline: 'true',
        onSuccess: function (event) {
	    jQuery.ajax({
		url:     "../auth/register", //Адрес подгружаемой страницы
		type:     "POST", //Тип запроса
		dataType: "html", //Тип данных
		data: jQuery("#RegisterForm").serialize(), 
		success: function(response) { //Если все нормально
			if(response == "ALL_GOOD"){
				location.reload();
			}
			if(response == "ERROR"){
				alert('Ошибка:' + response);
			}
			alert(response);
		},
		error: function(response) { //Если ошибка
			alert('Ошибка');
		}
	    });
            return false;
        },
        onFailure: function (event) {
            return false;
        }
      };


$(function() {
   $('#RegisterForm').form(validationRules, extendRules);
 });
function Registration(){
   $(function() {
       $('#RegisterForm').submit();
    });
}
