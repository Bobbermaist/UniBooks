<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('url');
	}

	public function index()
	{
		$this->load->model('Book_model');
		//$this->Book_model->setISBN('8840813608');
		//$this->Book_model->setISBN('8817868833');
		//$this->Book_model->setISBN('8838662812');
		//$this->Book_model->setISBN('8871924014');
		//$this->Book_model->setISBN('8865431139');
		$this->Book_model->setISBN('8845131837');
		$google = $this->Book_model->google_fetch();
		$book = $this->Book_model->get_book();
		echo "GOOGLE FETCH\n";
		print_r($google);
		echo "\n\n";
		echo "DATABASE FETCH\n";
		print_r($book);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
