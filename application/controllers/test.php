<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('url');
	}

	public function index($int = 1)
	{
		$this->load->model('Book_model');
		$book = $this->Book_model->get($int);
		print_r($book);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
