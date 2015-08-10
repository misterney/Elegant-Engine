<?
class Controller {
	public $state;
	function __construct(){
		$this->view = new View();
		$this->state = 'active';
	}
	
	function action_index()
	{
	}

} 