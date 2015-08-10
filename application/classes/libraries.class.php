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
		if(preg_match('/\//', $name)) $name = explode('/', $name);

		if($name[0] == "system"){
			$name = implode('/', $name);
			$file = VENDOR_LIBRARIES.$name.'.library.php';
			if(file_exists($file)){
				$library = include_once($file);
				return $library;
			} else {
				Debug::log('Отсутствует подключаемая библиотека: '. $name);
			}
		} else {
			$file = VENDOR_LIBRARIES.$name.'.library.php';
			if(file_exists($file)){
				$library = include_once($file);
				return $library;
			} else {
				Debug::log('Отсутствует подключаемая библиотека: '. $name);
			}
		}
	}
} 