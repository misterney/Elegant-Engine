<?
class Model_Admin extends Model {	

	function __construct(){
		parent::__construct();
		$this->libraries->load('session');
		$this->libraries->load('html');
	}
	public function getData()
	{
		$this->data["html"]["title"] = "ElegantCMS";
		
		$menu = [
			"style" => "teal",
			"items" => [
				$this->data["html"]["title"] => [
					"type" => "header",
					"href" => "/",
					
				],
				"Главная" => [
					"type" => "active",
					"href" => "/",
				],
			],
		];

		if(SES::is_set("Logged", "1")){
			$menu["items"]["Выход"] = ["href" => ENGINE_URL.'/auth/logout',"pos"  => "right"];	
		} else {
			$menu["items"]["Вход"] = ["href" => ENGINE_URL.'/main/login',	"pos"  => "right"];
		}
		$this->data["html"]["menu"] = $menu;
		return $this->data;
	}
}