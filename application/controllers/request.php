<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package UniBooks
 * @author Emiliano Bovetti
 * @since Version 1.0
 */

/**
 * UniBooks Request class.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Request extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_restrict_area(USER_RIGHTS, 'request');
		$this->load->model('Request_model');
	}

	public function index()
	{
		$this->User_model->add_userdata('search_action', 'request/complete');

		$this->load->helper('form');
		$this->_set_view('form/single_field', array(
			'action'				=> 'book/search',
			'label'					=> 'Inserisci una richiesta per un libro',
			'submit_name'		=> 'search_for_request',
			'submit_value'	=> 'Cerca',
			'input'					=> array(
					'name'			=> 'search_key',
					'maxlength'	=> '255',
					'id'				=> 'search_for_request',
			),
		));

		$this->_view();
	}

	public function complete()
	{
		$this->load->model('Book_model');

		$book_id = $this->User_model->userdata('book_found');
		if ($book_id !== FALSE)
		{
			$this->Request_model->set_book_id($book_id);
		}
		else
		{
			show_error('Errore nell\'inserimento della richiesta');
		}

		$this->User_model->del_userdata('book_found');

		$this->_try('Request_model', 'insert');
		$this->_set_message('request_complete');

		$this->Book_model->set_id($book_id);
		$this->_set_view('book', $this->Book_model->get_array());

		$this->_view();
	}

	public function complete()
	{
		$this->load->model('Book_model');
		$this->load->model('Request_model');

		$book_id = $this->User_model->userdata('book_found');
		if( $this->Request_model->insert($user_id, $book_id) )
			$view_data = array('p' => 'Richiesta inserita con successo');
		else
			$view_data = array('p' => 'Hai gi&agrave; inserito una richiesta per questo libro');

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('book', $book_info);
		$this->load->view('template/coda');
	}

	public function delete()
	{
		$this->Request_model->set_book_id($this->input->post('book_id'));
		$this->_try('Request_model', 'delete');
		$this->_set_message('request_delete');

		$this->_view();
	}
}

// END Request class

/* End of file sell.php */
/* Location: ./application/controllers/sell.php */  
