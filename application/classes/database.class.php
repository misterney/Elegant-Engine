<?
class DataBase {
	protected static $_instanse;
	private $data;
	public $db;

	private function __construct(){}
	private function __clone(){}
	private function __wakeup(){}

	public static function GetInstanse(){
		if(null === self::$_instanse){
			self::$_instanse = new self();
		}
		return self::$_instanse;
	}
	public function connect(){
		$this->data = Settings::load('database');
		if(in_array($this->data["driver"], $this->data["drivers"])){
			switch($this->data["driver"]){
				case "pdo":
					return new DataBase_pdo($this->data);
					break;
				default:
					return new DataBase_mysql($this->data);
					break;
			}
		} else {
			echo "Неизвестный тип обработчика БД";
		}
	}	
}