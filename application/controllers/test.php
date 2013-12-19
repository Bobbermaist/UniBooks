<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index($book_id = NULL)
	{
		if( ! $book_id )
			redirect('test/get_book/1');
		else
			redirect("test/get_book/$book_id");
	}

	public function get_book($book_id = NULL)
	{
		if( ! $book_id )
			redirect('test/get_book/1');
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->model('Book_model');

		$this->load->view('paragraphs', array('p' => "ID libro: <b>$book_id</b>"));
		if( $book_info = $this->Book_model->get_book($book_id) )
			$this->load->view('book', $book_info);
		else
			$this->load->view('paragraphs', array('p' => 'L\'ID inserito non ha prodotto nessun risultato'));
			/* Controllo codice ISBN */
		$this->Book_model->setISBN($book_info['ISBN']);
		if( $this->Book_model->issetISBN() )
			$this->load->view('paragraphs', array('p' => 'Codice ISBN corretto'));
		else
			$this->load->view('paragraphs', array('p' => 'Codice ISBN errato'));
		$this->load->view('template/coda');
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
