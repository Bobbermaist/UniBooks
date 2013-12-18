 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Sell extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Book_model');
		$this->load->library('session');
		$this->load->helper('url');
		if( ! $this->session->userdata('ID') )
		{
			$this->session->set_userdata(array('redirect' => 'sell'));
			redirect('user');
		}
	}

	public function index()
	{
		$this->load->helper('form');
		$this->load->config('form_data');

		$this->load->view('template/head');
		$this->load->view('template/body');

		$this->session->set_userdata(array('action' => 'sell/choose_price'));
		$view_data = $this->config->item('book_search_data');
		$view_data = array(
			'input_type' => array(
     		'name'      => 'book_search',
     		'maxlength' => '255'
    	),
    	'redirect'			=> 'book/search',
    	'title' 				=> 'Cerca un libro da vendere',
    	'submit_name'		=> 'search',
    	'submit_value'	=> 'Cerca'
		);
		$this->load->view('form/single', $view_data);
		$this->load->view('template/coda');
	}

	public function choose_price()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		//$this->form_validation->set_rules('price', 'Price', 'regex_match[//]');
		if( $this->form_validation->run() )
		{
			$this->session->set_userdata(array('price' => $this->input->post('price')));
			redirect('sell/complete');
		}
		$this->load->view('template/head');
		$this->load->view('template/body');
		$view_data = array(
			'input_type' => array(
     		'name'      => 'price',
     		'maxlength' => '7'
    	),
    	'redirect'			=> 'sell/choose_price',
    	'title' 				=> 'Indica il prezzo di vendita',
    	'submit_name'		=> 'submit_price',
    	'submit_value'	=> 'Inserisci'
		);
		$this->load->view('form/single', $view_data);
		$this->load->view('template/coda');
	}

	public function complete()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');

		$book_info = $this->Book_model->get_book($this->session->userdata('book_id'));
		$user_id = $this->session->userdata('ID');
		$book_id = $this->session->userdata('book_id');
		$book_price = $this->session->userdata('price');
		$this->load->view('paragraphs', array('p' => array('Prezzo di vendita inserito:', $book_price)));
		if( $this->Book_model->create_sale($user_id, $book_id, $book_price) )
			$this->load->view('paragraphs', array('p' => 'Vendita creata con successo'));
		else
			$this->load->view('paragraphs', array('p' => 'Vendita gi&agrave; creata'));
		print_r($book_info);
		$this->load->view('template/coda');
	}
}

/* End of file sell.php */
/* Location: ./application/controllers/sell.php */ 