<?
class Libraries {
	protected static $_instanse;
	private function __construct(){
	}
	private function __clone(){
	}
	public static function GetInstanse(){
		if(null === self::$_instanse){
			self::$_instanse = new self();
		}
		return self::$_instanse;
	}
	public function load($name){
		$file = ENGINE_PATH_LIBRARIES.$name.'.class.php';
		$library = include_once($file);
		return $library;
	}
} 