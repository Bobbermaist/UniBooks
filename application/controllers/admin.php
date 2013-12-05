<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

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
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->config('form_data');
		$this->load->helper('form');
		$user = $this->session->all_userdata();
		$this->load->view('par', array('par' => 'Benvenuto, amministratore <b>'.$user['user_name'].'!</b>'));
		$this->load->view('template/coda');
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */ 
 
