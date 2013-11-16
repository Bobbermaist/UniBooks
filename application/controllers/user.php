<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	private $id, $user_name, $rights;

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function registration()
	{
		if( $this->input->post('registration') ) {
		//if( $data = $this->input->post(NULL, TRUE) ) {
			$this->load->config('validation');
			$this->load->library('form_validation');
			$this->form_validation->set_rules($reg_form);
			if($this->form_validation->run() == FALSE)
			$this->load->model('User_model');
			print_r($data);
		}
		else
			$this->load->view('head');
			$this->load->view('body');
			$this->load->view('registration');
			$this->load->view('coda');
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
