<?
class Controller_Error extends Controller {
	public $model;
	public $view;

	function __construct(){
		parent::__construct();
		$this->model = new Model_Error();
		$this->view  = new View();
	}
	function action_404()
	{
		$data = $this->model->getData();
		$this->view->render('system/error/404', $data);
	}
	function action_403()
	{
		$data = $this->model->getData();
		$this->view->render('system/error/403', $data);
	}
	function action_refresh()
	{
		$data = $this->model->getData();
		$this->view->render('system/error/refresh', $data);
	}
} 