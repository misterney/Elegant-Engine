<?											//Подключение к базе данных
class shop {
	private function clearing($string){
		$string = strip_tags(htmlspecialchars(htmlentities(stripslashes($string))));
		return $string;
	} 							//Очистка переменной от мусора
	public  function infoUser($login){
		global $database;
		$login 				= $this->clearing($login);
		$account["login"] 	= $login;
		
		//Проверка, имеется ли аккаунт
		$result = $database->select("select * from `authme` where `username` = '".$login."'");
		//Если имеется аккаунт, то:
		if($result) {
			$account["id"] 				= $result[0]["id"];
			$account["authmeExist"]		= 1;
			//Проверка покупал ли игрок привелегии
			$result = $database->select("select * from `ex_purchased` where `nickname` = '".$login."'");
			$haveItemImproves = 0;
			
		
			
			for($i=0; $i < count($result); $i++){
				$temp_item = $this->infoItem($result[$i]["item_id"]);
				if($temp_item[0]["improves"] == 1) {
					$haveItemImproves 	= 1;  
					$user_item 			= $temp_item[$i];	
				}
			}
			if($haveItemImproves == 1){
				//Заполнение данных об аккаунте
				$account["hasPrivilege"] 	= "1";
				$account["discount"] 		= $result[0]["discount_id"];
				$account["item"] 			= $user_item;
				
			} else $account["hasPrivilege"] = "0";
		//При отсутствуещем аккаунте:
		} else {
			//Заполнение данных об аккаунте
			$total = $database->select("SELECT COUNT(*) FROM authme");
			$account["login"] 			= $login;
			$account["id"] 				= $total[0];
			$account["authmeExist"] 	= 0;
		}
		return $account;
		unset($account);
		unset($result);
	}							//Получение информации о игроке
	public  function infoItem($item){
		global $database;
		$item = $this->clearing($item);
		return $database->select("select * from `ex_items` where `id` = '".$item."'");
	}								//Получение информации о услуге
	public  function infoDiscount($discount){
		global $database;
		$discount = $this->clearing($discount);
		$result = $database->select("select * from `ex_discounts` where `key` = '".$discount."'");
		unset($discount);
		if($result){
			$discount["key"] 		= $result[0]["key"];
			$discount["percent"]	= $result[0]["percent"]; 
			$discount["id"]			= $result[0]["id"]; 
			$discount["exist"]  	= 1;
			if($result[0]["used"] == 0){
				$discount["used"]	= 0;
			} else {
				$discount["used"]	= 1;
			}
		} else {
			$discount["exist"]  	= 0;
		}
		return $discount;
	}						//Получение информации о скидочном коде
	public  function getSumm($login, $item, $discount){
		global $database;
		//Очистка переменных
		$login 		= $this->clearing($login);
		$item 		= $this->clearing($item);
		$discount 	= $this->clearing($discount);
		
		$method = $this->selectMethod($login, $item, $discount);
		
		$account	= $this->infoUser($login);
		$item		= $this->infoItem($item);
		$discount	= $this->infoDiscount($discount);
		
		/*Сама обработка суммы по полученному методу
			4 - Докуп со скидкой
			3 - Покупка со скидкой
			2 - Докуп
			1 - Покупка
		*/
		switch($method) {
			case 1:
				return $item[0]["price"];
				break;
			case 2:
				$price = $item[0]["price"] - $account["item"]["price"];
				if($price <= 0) return "FALSE";
				else return $price;
			case 3:
				$price = $item[0]["price"] - ($item[0]["price"]*$discount["percent"])/100;
				if($price <= 0) return "FALSE";
				else return $price;
			case 4:
				$price = $item[0]["price"] - $account["item"]["price"];
				$price = $price - ($price * $discount["percent"])/100;
				if($price <= 0) return "FALSE";
				else return $price;
		}
	}			//Получение суммы
	public  function renderButton($price){
		$price = $this->clearing($price);
		if($price == "FALSE"){
		$output = '<button class="btn btn-success btn-lg pull-right wow bounceIn animated" onclick="check();" id="button" name="button" disabled="disabled">Куплено</button>';
		} else {
		$output = '<button class="btn btn-success btn-lg pull-right wow bounceIn animated" onclick="check();" id="button" name="button">Купить за '.$price.' рублей</button>';
		}
		return $output;
		unset($price);
		unset($output);
	}						//Рендер кнопки покупки
	public  function checkUser($login){
		//Очистка и получение информации о игроке
		$login 		= $this->clearing($login);
		$account 	= $this->infoUser($login);
		//Сама проверка игрока
		if($account["authmeExist"] == 1){
			if($account["hasPrivilege"]){
				$output =  "У игрока <strong>".$login."</strong><br> 
							Привелегия: <strong>".$account["item"]["name"]."</strong>";		
			} else {
				$output =  $login." - Обычный игрок";
			}
		} else {
			$output = "Данный игрок не зарегистрирован";
		}
		//Вывод
		return $output;
	}							//Получение информации о игроке (О услугах)
	public  function getOnline(){
		global $database, $settings;
			$online = $database->select("select * from ex_online where id = '1'");
			
			$total = $database->select("SELECT COUNT(*) FROM authme");
			//Собираем это все в кучу
			$output = array (
				"players"		=> $online[0]["online"],
				"maxplayers"		=> $online[0]["slots"],
				"total"		=> $total[0][0],
				"record"		=> $online[0]["max_online"],
				"status"		=> $online[0]["status"]
			);
			return $output;
	}									//Получение информации о онлайне
	public  function lastUser(){
		global $database;
		$user = $database->select("SELECT * FROM `authme` ORDER BY `id` DESC LIMIT 1");
		return $user[0]["username"];
		
	}									//Последний игрок
	public function lastBuy(){
		global $database;
		$user = $database->select("SELECT * FROM `ex_purchased` ORDER BY `id` DESC LIMIT 1");
		$item = $this->infoItem($user[0]["item_id"]);
		$output = "Посл. покупатель: <strong>". $user[0]["nickname"] ."</strong> <br> Купил: <strong>". $item[0]["name"]."</strong>";
		return $output;
	}
	public  function headerLocation($login, $item, $discount){
		global $settings;
		$account = array (
			"login" 	=> $login,
			"item" 		=> $item,
			"discount" 	=> $discount,
		);
		$price = $this->getSumm($login, $item, $discount);
		//Защита от отрицательной суммы
		if($price == "FALSE") die ("Ошибка");
		$infoUser 	= $this->infoUser($login);
		$item 		= $this->infoItem($item);
		
		$protection_code = md5($settings["protection_key"].$price.$account["login"].$account["item"]);
		
		$account["protection_code"] = $protection_code;
		
		//Выбор между платежными агрегаторами
		//--------------------UNITPAY-------------------
		if($settings["payment_agregator"] == "unitpay"){
		$location = 'https://unitpay.ru/pay/'.$settings["unitpay_id"].'?sum='.$price.'&account='.json_encode($account).'&desc=Покупка привилегии '.$item["name"].' игроку '.$login.' на сервере '.$settings["project_name"].' на сумму: '.$price.' рублей';
		}
		//--------------------WAYTOPAY------------------
		if ($settings["payment_agregator"] == "waytopay"){
		$location = 'https://waytopay.org/merchant/index/?MerchantId='.$settings["waytopay_id"].'&OutSum='.$price.'&InvId='.$infoUser["id"].'&account='.json_encode($account).'&InvDesc=Покупка привилегии '.$item[0]["name"].' игроку '.$login.' на сервере '.$settings["project_name"].' на сумму '.$price.' рублей';
		}
		//----------------------------------------------
		header('location: '.$location);
	}	//Создание ссылки и редирект
	public  function selectMethod($login, $item, $discount){
		global $database;
		//Получения данных о переменных
		$account 	= $this->infoUser($login);		
		$item 		= $this->infoItem($item);
		$discount 	= $this->infoDiscount($discount);
		
		/*	Выбор метода
			4 - Докуп со скидкой
			3 - Покупка со скидкой
			2 - Докуп
			1 - Покупка
		*/
		if($discount["exist"] != 0){
			if($discount["used"] != 1){
				$dCanUse = 1;
			} else {
				$dCanUse = 0;
			}
		} else {
			$dCanUse = 0;
		}
		
		
		
		if($dCanUse != 0) {
			if($account["authmeExist"] == 1){
				if($account["hasPrivilege"] == "1"){
					if($item[0]["improves"] == "1") {
						$method = 4;
					} else 	$method = 3;
				} else 	$method = 3;
			} else 	$method = 3;
		} else {
			if($account["authmeExist"] == 1){
				if($account["hasPrivilege"] == "1"){
					if($item[0]["improves"] == "1"){
						$method = 2;
					} else	$method = 1;
				} else	$method = 1;
			} else	$method = 1;
		}
		return $method;
	}		//Получение метода оплаты
	public  function giving($login, $item, $discount){
		global $settings, $database;
        $method 	= $this->selectMethod($login, $item, $discount);
        $item 		= $this->infoItem($item);
        $discount	= $this->infoDiscount($discount);
        
        switch($method) {
			case 1:
				$database->insert("insert into `ex_purchased` (`nickname`, `item_id`) values ('".$login."','".$item[0]["id"]."')");
				break;
			case 2:
				$database->insert("UPDATE `ex_purchased` SET `item_id` = '".$item[0]["id"]."' WHERE `nickname` = '".$login."'");
				break;
			case 3:
				$database->update("UPDATE `ex_discounts` SET `used` = '1' where `key` = '".$discount["key"]."'"	);
				$database->insert("insert into `ex_purchased` (`nickname`, `item_id`, `discount_id`) values ('".$login."','".$item[0]["id"]."', '".$discount["id"]."')");
				break;
			case 4:
				$database->update("UPDATE `ex_discounts` SET `used` = '1' where `key` = '".$discount["key"]."'");
				$database->insert("UPDATE `ex_purchased` SET `item_id` = '".$item[0]["id"]."' WHERE `nickname` = '".$login."'");
				break;
		}
	}			//Выдача услуги
	public  function getItems(){
		global $database;
		$output = $database->select("select * from ex_items");
		return($output);
	}									//Получение списка услуг
}

class events{
	public function getSumm($login, $item, $discount){
		global $functions;
		$summ = $functions->getSumm($login, $item, $discount);
		$discount = $functions->infoDiscount($discount);

		if($summ == "FALSE"){
			$message = '<button class="btn btn-success btn-lg wow bounceIn animated" onclick="check();" id="button" name="button">Куплено</button>';
		} else {
			if($discount["exist"] == 1){
				if($discount["used"] == 0){
					$message = '<button class="btn btn-success btn-lg wow bounceIn animated" onclick="check();" id="button" name="button">Купить за '.$summ.' руб. (-'.$discount["percent"].'%)</button>';
				} else {
					$message = '<button class="btn btn-success btn-lg wow bounceIn animated" onclick="check();" id="button" name="button">Купить за '.$summ.' руб. </button>';
				}
			} else {
					$message = '<button class="btn btn-success btn-lg wow bounceIn animated" onclick="check();" id="button" name="button">Купить за '.$summ.' руб. </button>';
			}
		}
		echo $message;
	}
	public function getOnline(){
		global $functions;
		$online 	= $functions->getOnline();
		$lastuser 	= $functions->lastUser();
	if($online["status"] == 1){	
	echo '<br><br><br>
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<h2 class="wow fadeInUp animated" style="font-size: 50px">'.$online['players'].'</h2>
				<h2 class="wow fadeInUp animated" style="font-size: 30px">Онлайн</h2>
			</div>
			<div class="col-md-3">
				<h2 class="wow fadeInUp animated" style="font-size: 50px">'.$online['maxplayers'].'</h2>
				<h2 class="wow fadeInUp animated" style="font-size: 30px">Слоты</h2>
			</div>
			<div class="col-md-3">
				<h2 class="wow fadeInUp animated" style="font-size: 50px">'.$online["total"].'</h2>
				<h2 class="wow fadeInUp animated" style="font-size: 30px">Игроков</h2>
			</div>
			<div class="col-md-3">
				<h2 class="wow fadeInUp animated" style="font-size: 50px">'.$online["record"].'</h2>
				<h2 class="wow fadeInUp animated" style="font-size: 30px">Рекорд</h2>
			</div>
	';
} else echo '<br><br><br><h2 class="wow fadeInDown animated" style="font-size: 50px">Сервер выключен</h2>';
echo '<br><br><br><center><h2 class="wow fadeInUp animated" style="font-size: 20px">Последний игрок: '.$lastuser.'</h2></center>';
		
		
		
	}
	public function redirect($login, $item, $discount){
		global $functions;
		$functions->headerLocation($login, $item, $discount);
	}
	public function checkUser($login){
		global $functions;
		echo $message = '<div class="well well-sm">'.$functions->checkUser($login).'</div>';
	}
	public function getListItems(){
		global $functions;
		$items = $functions->getItems();
		for($i=0; $i < count($items); $i++){
			echo '	
	<div class="row"><div class="col-md-3"></div>
	<div class="col-md-6">
		<div class="panel panel-primary">
    		<div class="panel-heading" role="tab" id="heading'.$items[$i]["id"].'">
    			<h4 class="panel-title">
    				<a data-toggle="collapse" data-parent="#accordion" 
    				href="#collapse'.$items[$i]["id"].'" 
    				aria-expanded="true" aria-controls="collapse'.$items[$i]["id"].'">'; 
					if ($items[$i]["improves"]== 1) echo "Привелегия: "; else "Услуга: ";
					echo '	<strong>'.$items[$i]["name"].'</strong></a></h4></div>
    				<div id="collapse'.$items[$i]["id"].'" class="panel-collapse collapse '; 
    				if($items[$i]["id"] == 1) echo 'in'; 
    				echo '" role="tabpanel" aria-labelledby="heading'.$items[$i]["id"].'">
					<div class="panel-body">'.$items[$i]["desc"].'<p class="bg-info">
						Стоимость: <strong> '.$items[$i]["price"].'</strong> рублей '; 
						if($items[$i]["improves"] == "1") echo '- <strong>Навсегда</strong>'; echo'</p>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3"></div>
	</div><br>';
		}
	}
	public function getListItemsFP(){
		global $functions;
		$items = $functions->getItems();
		for($i=0; $i < count($items); $i++){
			echo '<option value="'.$items[$i]["id"].'"><b>'.$items[$i]["name"].' (</b>'.$items[$i]["price"].' Руб.'; 
			if($items[$i]["improves"] == 1) echo '/навсегда )</option>'; else echo')</option>';
			
		}
	}
	public function lastBuy(){
		global $functions;
		echo $output = '<div class="alert alert-warning">'.$functions->lastBuy().'</div>';
	}
}


$functions 	= new functions();										//Инициализация класса функций
$events		= new events();											//Инициализация класса ивентов

