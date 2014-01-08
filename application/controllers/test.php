<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->database();
		$this->load->model('Book_model');
		/*
		$this->Book_model->setISBN('8840813608');
		$this->Book_model->search();
		print_r($this->Book_model->results);
		*/

		$data = $this->Book_model->search('Immanuel Kant');
		print_r($this->Book_model->results);
		/*
		print_r( $this->Book_model->get(59) );
		*/
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
