<?
class Controller_404 extends Controller {
	public $model;
	public $view;

	function __construct(){
		$this->model = new Model_404();
		$this->view  = new View();
	}
	function action_index()
	{
		$data = $this->model->getData();
		$this->view->render('404/index', 		$data);
	}
} 