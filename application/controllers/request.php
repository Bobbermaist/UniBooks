 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Request extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		if( ! $this->session->userdata('ID') )
		{
			$this->session->set_userdata(array('redirect' => 'request'));
			redirect('user/login');
		}
	}

	public function index()
	{
		$this->load->helper('form');
		$view_data = array(
			'input_type' => array(
     		'name'      => 'book_search',
     		'maxlength' => '255'
    	),
    	'redirect'			=> 'book/search',
    	'title' 				=> 'Inserisci una richiesta',
    	'submit_name'		=> 'search',
    	'submit_value'	=> 'Cerca'
		);
		$this->session->set_userdata(array('action' => 'request/complete'));

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/single', $view_data);
		$this->load->view('template/coda');
	}

	public function complete()
	{
		$this->load->model('Book_model');
		$this->load->model('Request_model');

		$book_info = $this->Book_model->get($this->session->userdata('book_id'));
		$user_id = $this->session->userdata('ID');
		$book_id = $this->session->userdata('book_id');
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
