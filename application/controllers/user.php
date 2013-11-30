<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	
	private $id, $user_name, $rights;

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function registration()
	{
		$valid = FALSE;
		$this->load->view('head');
		$this->load->view('body');
		$this->load->helper('form');
		if( ($post = $this->input->post()) )
		{
			$this->load->library('form_validation');
			$this->load->config('form_validation');
			$valid = $this->form_validation->run('signup');
		}
		if( ! $valid )
			$this->load->view('registration');
		else
		{
			$this->load->model('User_model');
			$this->User_model->insert_user( $post );
			echo 'succes!';
		}
		$this->load->view('coda');
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
