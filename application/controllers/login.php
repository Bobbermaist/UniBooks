<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->_set_view('form/login', array(
  		'user_name' => array(
  			'name'			=> 'user_name',
  			'maxlength'	=> '20',
  			'value'			=> $this->input->post('user_name'),
  		),
  		'password' => array(
  			'name'			=> 'password',
  			'maxlength'	=> '64',
  		),
  	));

		if ($this->form_validation->run() === TRUE)
		{
			$this->User_model->user_name($this->input->post('user_name'));
			if ($this->User_model->login($this->input->post('password')) === TRUE)
			{
				$redirect = $this->session->userdata('redirect');
				if ($redirect === FALSE)
				{
					redirect('user');
				}
				else
				{
					$this->session->unset_userdata('redirect');
					redirect($redirect);
				}
			}
			else
			{
				$this->_set_view('generic', array(
					'p'		=> 'Errore nel login',
					'id'	=> 'error',
				));
			}
		}

		$this->_view();
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */ 
