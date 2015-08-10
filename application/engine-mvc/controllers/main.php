<?
class Controller_Main extends Controller {
	public $model;
	public $view;

	function __construct(){
		parent::__construct();
		$this->model = new Model_Main();
		$this->view  = new View();

	}
	function action_index()
	{
		$data = $this->model->getData();
		$this->view->render('system/main/index', 		$data);
	}
	function action_login()
	{
		$data = $this->model->getData();
		$this->view->render('system/main/login', 		$data);
	}
	function action_recovery()
	{
		$data = $this->model->getData();
		$this->view->render('system/main/recovery', 		$data);
	}
	function action_logout()
	{
		$data = $this->model->getData();
		$this->view->render('system/main/logout', 		$data);
	}

}