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

		$this->session->set_userdata(array('action' => 'sell/search_result'));
		$view_data = $this->config->item('book_search_data');
		$view_data['title'] = 'Vendi un libro';
		$this->load->view('form/book', $view_data);
		$this->load->view('template/coda');
	}

	public function search_result()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');

		$book_info = $this->Book_model->get_book($this->session->userdata('book_id'));
		$user_id = $this->session->userdata('ID');
		$book_id = $this->session->userdata('book_id');
		if( $this->Book_model->create_sale($user_id, $book_id) )
			$this->load->view('paragraphs', array('p' => 'Vendita creata con successo'));
		else
			$this->load->view('paragraphs', array('p' => 'Vendita gi&agrave; creata'));
		print_r($book_info);
		$this->load->view('template/coda');
	}
}

/* End of file sell.php */
/* Location: ./application/controllers/sell.php */ 