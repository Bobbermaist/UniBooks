<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index($code = NULL)
	{
		$this->load->model('Book_model');
		$isbn = str_replace(array('-', ' '), '', $code);
		$this->Book_model->search($isbn);
		print_r($this->Book_model->get_array());
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
