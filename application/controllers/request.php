 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Request extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_restrict_area(USER_RIGHTS, 'request');
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
		$this->load->model('Sell_model');

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

		if($this->Sell_model->insert() === TRUE)
		{
			$this->_set_view('generic', array('p'	=> 'Vendita creata con successo'));
		}
		else
		{
			$this->_set_view('generic', array('p'	=> 'Hai gi&agrave; messo in vendita questo libro'));
		}
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
		$this->load->model('Request_model');
		$user_id = $this->session->userdata('ID');
		if( $post = $this->input->post() )
		{
			$this->Request_model->delete($user_id, $post['book_id']);
			$view_data = array('p' => 'Annuncio eliminato correttamente');
		}
		else
			$view_data = array('p' => 'Errore');

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('template/coda');
	}
}

/* End of file sell.php */
/* Location: ./application/controllers/sell.php */  
