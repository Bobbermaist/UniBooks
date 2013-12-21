<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Book extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Book_model');
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->helper('form');
		$this->load->config('form_data');

		$this->load->view('template/head');
		$this->load->view('template/body');

		$this->session->set_userdata(array('action' => 'book/search_result'));
		$view_data = $this->config->item('book_search_data');
		$view_data = array(
			'input_type' => array(
     		'name'      => 'book_search',
     		'maxlength' => '255'
    	),
    	'redirect'			=> 'book/search',
    	'title' 				=> 'Ricerca un libro',
    	'submit_name'		=> 'search',
    	'submit_value'	=> 'Cerca'
		);
		$this->load->view('form/single', $view_data);
		$this->load->view('template/coda');
	}

	public function search()
	{
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('table');
		$this->load->view('template/head');
		$this->load->view('template/body');

		$search_key = $this->input->post('book_search');
		if( $search_key )
		{
			$this->Book_model->setISBN($search_key);
				/* In alcuni casi manca l'ISBN ai dati di google
				 * se l'ha inserito l'utente utilizzo quello 
				 * Es: 8817868833 */
			if( $this->Book_model->issetISBN() )
				$this->session->set_userdata(array('ISBN' => $this->Book_model->getISBN()));
			$google_data = $this->Book_model->google_fetch($search_key);
			$this->session->set_userdata(array('google_data' => $google_data));
			$books_data = $this->Book_model->gdata_to_table($google_data);
			$this->load->view('form/book_select', array('books_data' => $books_data));
		}
		elseif( $google_data = $this->session->userdata('google_data') )
			$this->load->view('form/book_select', array('books_data' => $this->Book_model->gdata_to_table($google_data)));
		else
			redirect('book');

		$this->load->view('template/coda');
	}

	public function select_result()
	{
		$this->load->helper('url');
		$google_data = $this->session->userdata('google_data');
		$book_select = $this->input->post('book_select');
		if( $google_data )
		{
			$this->Book_model->set_info($google_data, $book_select);
			print_r($this->Book_model->get_info());
			if( $book_id = $this->Book_model->insert($this->session->userdata('ISBN')) )
			{
				$this->session->unset_userdata('ISBN');
				$this->session->unset_userdata('google_data');
				$this->session->set_userdata(array('book_id' => $book_id));
				if( $action = $this->session->userdata('action') )
					redirect($action);
				else
					redirect('book/search_result');
			}
		}
	}

	public function search_result()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');

		if( $book_info = $this->Book_model->get($this->session->userdata('book_id')) )
			$this->load->view('book', $book_info);
		else
			$this->load->view('paragraphs', array('p' => 'Session data non presenti'));
		$this->load->view('template/coda');
	}

}

/* End of file book.php */
/* Location: ./application/controllers/book.php */ 