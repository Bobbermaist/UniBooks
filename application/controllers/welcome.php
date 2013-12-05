<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		//$this->load->library('session');
	}

	public function index()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		
		$this->load->view('par', array('par' => 'Benvenuto : )'));
		$this->load->view('par', array('par' => 'Puoi registrarti qui: '.
			anchor('user/registration', 'Registrazione utente', 'title="registrazione"')));
		$this->load->view('par', array('par' => 'Puoi effettuare il reset della tua password qui: '.
			anchor('user/reset', 'Reset password', 'title="reset"')));
		$this->load->view('par', array('par' => 'Puoi fare il log in qui: '.
			anchor('user/login', 'Log in', 'title="login"')));
		$this->load->view('par', array('par' => 'Puoi cercare un libro tramite ISBN o altre informazioni qui: '.
			anchor('book/index', 'Ricerca libro', 'title="search"')));
		$this->load->view('par', array('par' => 'Esegui test: '.
			anchor('test', 'Test controller', 'title="test"')));

		$this->load->view('template/coda');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */