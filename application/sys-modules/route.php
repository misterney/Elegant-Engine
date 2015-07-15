<?
if(!defined('SEAL_CHECK')) die('403 Forbidden');
class Route {
	private $default_controller 		= 'Main';
	private $default_action		= 'index';

	private $pref_controller		= 'Controller_';
	private $pref_action		= 'action_';
	private $pref_model		= 'Model_';
	
	function __construct(){
		$this->routes = explode('/', $_SERVER['REQUEST_URI']);
		if (count($this->routes) > 3) {
			$this->HandleGETData($this->routes);
		}
		
		if(!empty($this->routes[1])){
			$this->controller_name = $this->routes[1];
		} else {
			$this->controller_name = $this->default_controller;
		}
		
		if(!empty($this->routes[2])){
			$this->action_name = $this->routes[2];
		} else {
			$this->action_name = $this->default_action;
		}
		$this->model_file 			= $this->controller_name;		
		$this->controller_file 		= $this->controller_name;	
		$this->action_file 			= $this->action_name;	
		
		$this->model_name 		= $this->pref_model 	. $this->controller_name;
		$this->controller_name		= $this->pref_controller	. $this->controller_name;
		$this->action_name 		= $this->pref_action 	. $this->action_name;
		
		$this->run();
	}
	
	function Run(){
		$model_file 	= strtolower($this->model_file).'.php';
		$model_path 	= ENGINE_PATH_MVC.'models/'.$model_file;

		if(file_exists($model_path)) 
			include_once $model_path;
		
		$controller_file 	= strtolower($this->controller_file).'.php';
		$controller_path 	= ENGINE_PATH_MVC.'controllers/'.$controller_file;

		if(file_exists($controller_path)) 
				include_once $controller_path;
			else
				header("Location: ../404");
		
		$controller 	= new $this->controller_name;
		$action 		= $this->action_name;

		if(method_exists($controller, $action))
				$controller->$action();
			else 
				header("Location: ../404");
	}
	function HandleGETData($routes){
		$count = count($routes);
		for($i=3; $i <= $count; $i = $i + 2){
			if(isset($routes[$i+1])){
				$key = $routes[$i];
			} 

			if(isset($routes[$i+1])){
				$value = $routes[$i+1];
				$_GET[$key] = $value;
			} else {
				$_GET[$key] = "";
			}
		}
	}
}


	