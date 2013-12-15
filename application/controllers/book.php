<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Book extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->helper('form');
		$post = $this->input->post('book_search');

		$this->load->view('template/head');
		$this->load->view('template/body');

		if( $post ) $this->search($post);
		else $this->load->view('form/book_search');
		
		$this->load->view('template/coda');
	}

	private function search($search_key)
	{
		$this->load->model('Book_model');
		$this->Book_model->setISBN($search_key);
		$google_book_data = $this->Book_model->google_fetch($search_key);
		$this->Book_model->set_info($google_book_data, 0);
		print_r($this->Book_model->info);
		//print_r($google_book_data);
		//$this->select_result($google_book_data);
	}

	private function select_result($google_data)
	{
		$this->load->model('Book_model');
		$this->load->library('table');
		$books_data = $this->Book_model->gdata_to_table_array($google_data);
		if( ! $books_data )
			echo '<p>La ricerca non ha prodotto risultati</p>';
		else
		{
			$this->table->set_heading('Titolo', 'Autori', 'Anno di pubblicazione', 'ISBN', 'Pagine', 'Materia', 'Lingua');
			foreach ( $books_data as $book )
				$this->table->add_row($book);
			echo $this->table->generate();
		}
	}
}

/* End of file book.php */
/* Location: ./application/controllers/book.php */ 