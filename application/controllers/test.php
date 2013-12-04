<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function index()
	{
		$this->load->view('head');
		$this->load->view('body');

		$this->load->library('My_books');
		$book = new My_books;
		$data = array(
			'title' => 'Critica del giudizio',
			'author' => 'Immanuel Kant',
			'publisher' => 'De agostini'
		);
		print_r($book->get($data));

		$this->load->view('coda');
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
