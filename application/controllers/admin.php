<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		if( $this->session->userdata('rights') < 1 )
			redirect('user');
	}

	public function index()
	{
		$user = $this->session->all_userdata();
		
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', array('p' => 'Benvenuto, amministratore <b>'.$user['user_name'].'!</b>'));
		$this->load->view('template/coda');
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */ 
 
