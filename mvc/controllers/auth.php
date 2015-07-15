<?
class Controller_Auth extends Controller {
	public $model;
	public $view;

	function __construct(){
		$this->model = new Model_Auth();
		$this->view  = new View();
	}
	function action_login()
	{
		$this->model->login();
	}
	function action_register()
	{
		$this->model->register();
	}
	function action_logout()
	{
		$this->model->logout();
	}
	function action_info()
	{
		$this->model->info();
	}
} 