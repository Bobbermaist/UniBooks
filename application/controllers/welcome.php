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
		
		$view_data = array('p' => array(
			'Benvenuto : )',
			'Puoi registrarti qui: '.anchor('user/registration', 'Registrazione utente', 'title="registrazione"'),
			'Puoi effettuare il reset della tua password qui: '.anchor('user/reset', 'Reset password', 'title="reset"'),
			'Puoi fare il log in qui: '.anchor('user/login', 'Log in', 'title="login"'),
			'Puoi cercare un libro tramite le API di google books qui: '.anchor('book', 'Ricerca libro', 'title="search"'),
			'Una volta effettuato il log in metti in vendita un libro: '.anchor('sell', 'Vendi', 'title="sell"'),
			'Inserisci una richiesta per un libro: '.anchor('request', 'Richiedi', 'title="request"'),
			'Gestisci il tuo account, modificando nome utente, email, password <br>
				e visualizza gli annunci inseriti: '.anchor('account', 'Account'),
			'Gli amministratori possono accedere ad un\'area riservata: '.anchor('admin', 'Admin area', 'title="admin"'),
			'Esegui current migration: '.anchor('migration', 'Current migration'),
			'Drop database: '.anchor('migration/down', 'Drop all'),
			'Test controller: '.anchor('test', 'Test')
		));
		$this->load->view('paragraphs', $view_data);
		
		$this->load->view('template/coda');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */