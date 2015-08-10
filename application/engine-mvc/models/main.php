<?
class Model_Main extends Model {	
	
	function __construct(){
		parent::__construct();

		$this->libraries->load('system/session');
	}

	public function getData()
	{
		$this->data["html"]["title"] = "ElegantEngine";
		$this->data['routes'] = ENGINE_ROUTES;
		return $this->data;
	}
}