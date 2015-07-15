<?
class Controller_Main extends Controller {
	public $model;
	public $view;

	function __construct(){
		$this->model = new Model_Main();
		$this->view  = new View();
	}
	function action_index()
	{
		$data = $this->model->getData();
		$this->view->render('main/index', 		$data);
	}
	function action_login()
	{
		$data = $this->model->getData();
		$this->view->render('main/login', '_default.php', $data);
	}
	function action_register()
	{
		$data = $this->model->getData();
		$this->view->render('main/register', '_default.php', $data);
	}
} 