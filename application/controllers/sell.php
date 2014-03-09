 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Sell extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_restrict_area(USER_RIGHTS, 'sell');
	}

	public function index()
	{
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
		if( $this->form_validation->run() )
		{
			$this->session->set_userdata(array('price' => $this->input->post('price')));
			redirect('sell/complete');
		}
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

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/single', $view_data);
		$this->load->view('template/coda');
	}

	public function complete()
	{
		$this->load->model('Book_model');
		$this->load->model('Sell_model');
		$book_info = $this->Book_model->get($this->session->userdata('book_id'));
		$user_id = $this->session->userdata('ID');
		$book_id = $this->session->userdata('book_id');
		$book_price = $this->session->userdata('price');
		if( $this->Sell_model->insert($user_id, $book_id, $book_price) )
			$view_data = array('p' => 'Vendita creata con successo');
		else
			$view_data = array('p' => 'Hai gi&agrave; messo in vendita questo libro');
		
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('book', $book_info);
		$this->load->view('template/coda');
	}

	public function delete()
	{
		$this->load->model('Sell_model');
		$user_id = $this->session->userdata('ID');
		if( $post = $this->input->post() )
		{
			$this->Sell_model->delete($user_id, $post['book_id']);
			$view_data = array('p' => 'Vendita eliminata correttamente');
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