<?
class Controller_Cpanel extends Controller {
	public $model;
	public $view;

	function __construct(){
		parent::__construct();
		$this->model = new Model_Cpanel();
		$this->view  = new View();

	}
	function action_index()
	{
		$data = $this->model->getData();
		$this->view->render('system/cpanel/index', 		$data);
	}


}