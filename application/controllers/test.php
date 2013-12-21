<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index($isbn = NULL)
	{
		$this->load->model('Book_model');
		$this->Book_model->setISBN($isbn);
		echo $this->Book_model->language_group();
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
