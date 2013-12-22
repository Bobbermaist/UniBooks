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
		if( $this->Book_model->setISBN($isbn) === TRUE )
		{
			$par = array(
				$this->Book_model->get_country(),
				$this->Book_model->get_publisher()
			);
			$this->load->view('paragraphs', array('p' => $par));
		}
		else
			$this->load->view('paragraphs', array('p' => 'ISBN non valido'));
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
