<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Book extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Book_model');
	}

	public function index()
	{
		$this->load->helper('form');

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/book_search');
		$this->load->view('template/coda');
	}

	public function search($search_key = NULL)
	{
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->library('table');
		$this->load->view('template/head');
		$this->load->view('template/body');

		$search_key = $search_key ? $search_key : $this->input->post('book_search');
		if( $search_key )
		{
			$this->Book_model->setISBN($search_key);
			$google_data = $this->Book_model->google_fetch($search_key);
			$books_data = $this->Book_model->gdata_to_table($google_data);
			$this->session->set_flashdata('google_data', $google_data);
			$view_data = array('books_data' => $books_data);
			$this->load->view('form/book_select', $view_data);
		}
		else
			redirect('book');

		$this->load->view('template/coda');
	}

	public function select_result()
	{
		$this->load->library('session');
		$this->load->view('template/head');
		$this->load->view('template/body');
		
		$google_data = $this->session->flashdata('google_data');
		$book_select = $this->input->post('book_select');
		if(  $google_data )
		{
			print_r($google_data);
			$this->Book_model->set_info($google_data, $book_select);
		}
		else
			echo '<p>Flash data non presenti</p>';
		$this->load->view('template/coda');
	}
}

/* End of file book.php */
/* Location: ./application/controllers/book.php */ 