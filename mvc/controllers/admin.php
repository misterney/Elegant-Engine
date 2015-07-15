<?
class Controller_Admin extends Controller {
	public $model;
	public $view;

	function __construct(){
		$this->model = new Model_Admin();
		$this->view  = new View();
	}
	function action_index()
	{
		$data = $this->model->getData();
		$this->view->render('admin/index', $data);
	}
	function action_users()
	{
		$data = $this->model->getData();
		$this->view->render('admin/users', $data);
	}
	function action_services()
	{
		$data = $this->model->getData();
		$this->view->render('admin/services', $data);
	}

} 