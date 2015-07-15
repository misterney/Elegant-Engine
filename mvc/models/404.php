<?
class Model_404 extends Model {	
	
	function __construct(){
		parent::__construct();
		$this->libraries->load('session');
	}

	public function getData()
	{
		$this->data["html"]["title"] = "ElegantCMS";
	}
}