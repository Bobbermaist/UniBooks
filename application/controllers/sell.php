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
 * UniBooks Sell class.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Sell extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_restrict_area(USER_RIGHTS, 'sell');
		$this->load->model('Sell_model');
	}

	public function index()
	{
		$this->User_model->add_userdata('search_action', 'sell/choose_price');

		$this->load->helper('form');
		$this->_set_view('form/single_field', array(
			'action'				=> 'book/search',
			'label'					=> 'Cerca un libro da vendere',
			'submit_name'		=> 'search_for_sell',
			'submit_value'	=> 'Cerca',
			'input'					=> array(
					'name'			=> 'search_key',
					'maxlength'	=> '255',
					'id'				=> 'search_for_sell',
			),
		));

		$this->_view();
	}

	public function choose_price()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		if ($this->form_validation->run() === TRUE)
		{
			$this->User_model->add_userdata('price', $this->input->post('price'));
			redirect('sell/complete');
		}
		$this->_set_view('form/single_field', array(
			'action'				=> 'sell/choose_price',
			'label'					=> 'Indica il prezzo di vendita',
			'submit_name'		=> 'submit_price',
			'submit_value'	=> 'Inserisci',
			'input'					=> array(
					'name'			=> 'price',
					'maxlength'	=> '7',
					'id'				=> 'submit_price',
			),
		));
		$this->_view();
	}

	public function complete()
	{
		$this->load->model('Book_model');

		$book_id = $this->User_model->userdata('book_found');
		$price = $this->User_model->userdata('price');
		if ($book_id !== FALSE AND $price !== FALSE)
		{
			$this->Sell_model->set_book_id($book_id);
			$this->Sell_model->set_price($price);
		}
		else
		{
			show_error('Errore nella creazione della vendita');
		}

		$this->User_model->del_userdata('price');
		$this->User_model->del_userdata('book_found');

		$this->_try('Sell_model', 'insert');
		$this->_set_message('sell_complete');

		$this->Book_model->set_id($book_id);
		$this->_set_view('book', $this->Book_model->get_array());

		$this->_view();
	}

	public function delete()
	{
		$this->Sell_model->set_book_id($this->input->post('book_id'));
		$this->_try('Sell_model', 'delete');
		$this->_set_message('sell_delete');

		$this->_view();
	}
}

// END Sell class

/* End of file sell.php */
/* Location: ./application/controllers/sell.php */ 