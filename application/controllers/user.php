<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	private $id, $user_name, $rights;

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function registration()
	{
		$this->load->view('head');
		$this->load->view('body');
		//if( $this->input->post('registration') ) {
		if( $this->input->post() ) {
			$this->load->library('form_validation');
			$this->load->config('form_validation');
			if ($this->form_validation->run('signup') == FALSE)
				echo 'error';
			else
				echo 'formsuccess';
		}
		$this->load->helper('form');
		$this->load->view('registration');
		$this->load->view('coda');
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
