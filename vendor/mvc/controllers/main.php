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
		$this->view->render('main/index', 		$data);
	}
}