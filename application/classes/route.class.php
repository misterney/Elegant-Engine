<?
if(!defined('SEAL_CHECK')) die('403 Foribbden... Please go to the <a href="../../">home page</a>');

class Route {
	function __construct(){
		$settings = Settings::load('router');

		$this->default_controller = $settings['route_default_controller'];
		$this->default_action	  = $settings['route_default_action'];
	



		$this->routes = explode('/', $_SERVER['REQUEST_URI']);
		array_shift($this->routes);
		


		$root_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
		$url_pieces = explode('/', str_replace($root_url, '', ENGINE_URL));

		$this->routes = $this->sortArrayOfValue(array_diff($this->routes, $url_pieces), count($url_pieces));

		if(current($this->routes) == "system"){
			array_shift($this->routes);
			Debug::log('Запущен системный маршрутизатор...');
			$this->ProcessorEmbeddedFunctionality();
			define('ENGINE_ROUTES', $this->routes);
		} else {
			Debug::log('Запущен стандартный маршрутизатор...');

		if (count($this->routes) >= 3 AND $this->routes[2] != "") {
			Debug::log('Запуск обработчика входных данных');
			$this->decodeGetData($this->routes[2]);	

		} else {
			Debug::log('Входные данные остуствуют');
			unset($this->routes[2]);
			unset($_GET);
		}

		if(!empty($this->routes[0])){
			
			$this->controller_name 	= $this->routes[0];
		} else {
			$this->controller_name 	= $this->default_controller;
		}

		

		if(!empty($this->routes[1])){
			$this->action_name 		= $this->routes[1];
		} else {
			$this->action_name 		= $this->default_action;
		}


		$this->model_file 			= $this->controller_name;		
		$this->controller_file 			= $this->controller_name;	
		$this->action_file 			= $this->action_name;	
		
		$this->model_name 			= 'Model_' 		. $this->controller_name;
		$this->controller_name			= 'Controller_'		. $this->controller_name;
		$this->action_name 			= 'action_' 		. $this->action_name;
		

		$this->Run();
		define('ENGINE_ROUTES', $this->routes);
		}
	}
	
	function Run(){
		$model_file 	= strtolower($this->model_file).'.php';
		$model_path 	= VENDOR_MVC_M  .  $model_file;

		if(file_exists($model_path)){
			include_once $model_path;
		}
		
		$controller_file 	= strtolower($this->controller_file).'.php';
		$controller_path 	= VENDOR_MVC_C  .  $controller_file;

		if(file_exists($controller_path)){
				include_once $controller_path;

				$controller 	= new $this->controller_name;
				$action 	= $this->action_name;
				switch($controller->state){
				case "active":
					if(method_exists($controller, $action)){
						$controller->$action();
					} else {
							$this->Error("404");
					}
					break;
				case "inactive":
					$this->Error("405");
					break;
				case "private":
					$this->Error("403");
					break;
				}
		} else {	
				Debug::log('Отсутсвует файл контроллера: '. $controller_file.'<br>Путь: '.$controller_path.'<br>Вывод ошибки: 404');
				$this->Error("404");
		}



	}
	public function ProcessorEmbeddedFunctionality(){
		
		if (count($this->routes) >= 3 AND $this->routes[2] != "") {
			Debug::log('Запуск обработчика входных данных');
			$this->decodeGetData($this->routes[2]);	

		} else {
			Debug::log('Входные данные остуствуют');
			unset($this->routes[2]);
			unset($_GET);
		}
		if(!empty($this->routes[0])){
			
			$this->controller_name 	= $this->routes[0];
		} else {
			$this->controller_name 	= $this->default_controller;
		}

		

		if(!empty($this->routes[1])){
			$this->action_name 		= $this->routes[1];
		} else {
			$this->action_name 		= $this->default_action;
		}


		$this->model_file 			= $this->controller_name;		
		$this->controller_file 			= $this->controller_name;	
		$this->action_file 			= $this->action_name;	
		
		$this->model_name 			= 'Model_' 		. $this->controller_name;
		$this->controller_name			= 'Controller_'		. $this->controller_name;
		$this->action_name 			= 'action_' 		. $this->action_name;
		
		

		$model_file 	= strtolower($this->model_file).'.php';
		$model_path 	= ENGINE_MVC_M  .  $model_file;

		if(file_exists($model_path)){
			include_once $model_path;
		}
		
		$controller_file 	= strtolower($this->controller_file).'.php';
		$controller_path 	= ENGINE_MVC_C  .  $controller_file;

		if(file_exists($controller_path)){
				include_once $controller_path;

				$controller 	= new $this->controller_name;
				$action 	= $this->action_name;

				if(method_exists($controller, $action)){
					$controller->$action();
				} else {
						$this->Error("404");
				}
		} else {
				Debug::log('Отсутсвует файл контроллера: '. $controller_file.'<br>Путь: '.$controller_path.'<br>Вывод ошибки: 404');
				$this->Error("404");
		}

	}
	public function Error($error){
		$controller_file 	= 'error.php';
		$controller_path 	= ENGINE_MVC_C  .  $controller_file;

		if(file_exists($controller_path)){
			include_once($controller_path);
		} else {
			Debug::log('Не найден контроллер вывода ошибок... Завершение работы Движка');
			exit();
		}

		$model_file 	= $controller_file;
		$model_path 	= ENGINE_MVC_M  .  $model_file;
		if(file_exists($model_path)){
			include_once($model_path);
		} else {
			Debug::log('Не найдена модель вывода ошибок... Завершение работы Движка');
			exit();
		}


		$controller 		= new Controller_Error;
		$action 		= 'action_'.$error;
		$controller->$action();
	}

	function sortArrayOfValue($array, $value){
		$count = count($array);
		for($i=0; $i<$count; $i++){
			$array[$i] = $this->decodeUTF8orHTML($array[$i+$value]);
			unset($array[$i+$value]);
		}
		return $array;
	}
	function decodeGetData($array){
		$get = array();
		$explode = explode('&', $array);

		foreach($explode as $key=>$value){
			if(preg_match('/\=/', $value)){
				$pieces = explode('=', $value);
				if($pieces[0] == "") $pieces[0] = $key+1;
				if($pieces[1] == "") $pieces[1] = "null";
				$get[$pieces[0]] = $pieces[1];
			} else {
				$get[$value] = "null";
			}
		}
		$_GET = $get;
		print_r($_GET);
	}

	function decodeUTF8orHTML($string){
		return $this->ru2Lat(urldecode($string));
	}

	function ru2Lat($string){
		$lat=array(
		     "Щ", "Ш", "Ч","Ц", "Ю", "Я", "Ж","А","Б","В",
		     "Г","Д","Е","Ё","З","И","Й","К","Л","М","Н",
		     "О","П","Р","С","Т","У","Ф","Х","Ь","Ы","Ъ",
		     "Э","Є", "Ї","І",
		     "щ", "ш", "ч","ц", "ю", "я", "ж","а","б","в",
		     "г","д","е","ё","з","и","й","к","л","м","н",
		     "о","п","р","с","т","у","ф","х","ь","ы","ъ",
		     "э","є", "ї","і"
		);
		$cyr=array(
		     "Shch","Sh","Ch","C","Yu","Ya","J","A","B","V",
		     "G","D","E","E","Z","I","y","K","L","M","N",
		     "O","P","R","S","T","U","F","H","", 
		     "Y","" ,"E","E","Yi","I",
		     "sh","sh","ch","c","yu","ya","j","a","b","v",
		     "g","d","e","e","z","i","y","k","l","m","n",
		     "o","p","r","s","t","u","f","h",
		     "", "y","" ,"e","e","yi","i"
		);
		$string = str_replace($lat,$cyr,$string);
		$string = str_replace("_"," ",$string);
		return($string);
	}
	public static function getCurrentRoute(){
		$routes = explode('/', $_SERVER['REQUEST_URI']);
		array_shift($routes);
		$root_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
		$url_pieces = explode('/', str_replace($root_url, '', ENGINE_URL));
		$route_diff = array_diff($routes, $url_pieces);

		$count = count($route_diff);
		for($i=0; $i<$count; $i++){
			$array[$i] = urldecode(($route_diff[$i+count($url_pieces)]));
			unset($route_diff[$i+count($url_pieces)]);
		}
		return $array;
	}





}


