<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Book extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Book_model');
	}

	public function index()
	{
		$this->load->helper('form');
		$this->_set_view('form/single_field', array(
			'action'				=> 'book/search',
			'label'					=> 'Cerca un libro (ISBN)',
			'submit_name'		=> 'search_book',
			'submit_value'	=> 'Cerca',
			'input'					=> array(
     		'name'      => 'search_key',
     		'id'				=> 'search_book',
     		'maxlength' => '255',
    	),
		));
		$this->_view();
	}

	public function search()
	{
		$this->_post_required('search_key');

		if ($this->Book_model->search($this->input->post('search_key')) === FALSE)
		{
			show_error($this->lang->line("error_search_failed"));
		}
		else
		{
			$this->User_model->add_userdata('book_found', $this->Book_model->get_id());
			$action = $this->User_model->userdata('search_action');
			if ($action === FALSE)
			{
				redirect('book/result');
			}
			else
			{
				redirect($action);
			}
		}
	}

	public function result()
	{
		$book_id = $this->User_model->userdata('book_found');
		$this->Book_model->set_id($book_id);
		$this->_set_view('book', $this->Book_model->get_array());
		$this->_view();
	}
}

/* End of file book.php */
/* Location: ./application/controllers/book.php */ 