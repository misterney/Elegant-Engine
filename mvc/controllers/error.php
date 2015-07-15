<?
class Controller_Error extends Controller {
	public $model;
	public $view;

	function __construct(){
		$this->model = new Model_Error();
		$this->view  = new View();
	}
	function action_404()
	{
		$data = $this->model->getData();
		$this->view->render('error/404', $data);
	}
} 