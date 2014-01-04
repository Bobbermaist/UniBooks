<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Book extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Book_model');
		$this->load->library('session');
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->helper('form');
		$this->load->config('form_data');
		if ($search_key = $this->input->post('book_search'))
			redirect("book/search/$search_key");
		$view_data = $this->config->item('book_search_data');
		$view_data = array(
			'input_type' => array(
     		'name'      => 'book_search',
     		'maxlength' => '255'
    	),
    	'redirect'			=> 'book/index',
    	'title' 				=> 'Ricerca un libro',
    	'submit_name'		=> 'search',
    	'submit_value'	=> 'Cerca'
		);
		$this->session->set_userdata(array('action' => 'book/search_result'));

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/single', $view_data);
		$this->load->view('template/coda');
	}

	public function search($search_key = NULL, $page = 1)
	{
		$this->load->helper('form');
		$this->load->library('table');
		$this->load->library('pagination');

		$this->Book_model->setISBN($search_key);
		$this->Book_model->google_fetch(urldecode($search_key), $page);
		$this->Book_model->insert();
		$books_table = $this->Book_model->books_to_table();

		$config['base_url'] = site_url("book/search/$search_key");
		$config['use_page_numbers'] = TRUE;
		$config['total_rows'] = $this->Book_model->total_items;
		$config['per_page'] = MAX_RESULTS;
		$config["uri_segment"] = 4;
		$this->pagination->initialize($config);

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', array('p' => $this->pagination->create_links()));
		$this->load->view('form/book_select', array('books_data' => $books_table));
		$this->load->view('template/coda');
	}

	public function select_result()
	{
		if ($book_id = $this->input->post('book_id'))
			$this->session->set_userdata(array('book_id' => $book_id));
		
		if( $action = $this->session->userdata('action') )
			redirect($action);
		else
			redirect('book/search_result');
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