<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index($book_id)
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		
		$this->load->model('Book_model');
		$book = $this->Book_model->get_book($book_id);
		print_r($book);
		$this->Book_model->setISBN($book['ISBN']);
		if( $this->Book_model->issetISBN() )
			$this->load->view('paragraphs', array('p' => 'Codice corretto'));
		else
			$this->load->view('paragraphs', array('p' => 'Codice errato'));
		$this->load->view('template/coda');
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
