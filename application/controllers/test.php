<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->model('Book_model');
		$isbn = str_replace('-', '', '85-359-0277-5');
		$this->Book_model->search($isbn);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
