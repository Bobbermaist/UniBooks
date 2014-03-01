<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->logged !== TRUE)
		{
			$this->session->set_userdata(array('redirect' => 'user'));
			redirect('login');
		}
	}

	public function index()
	{
				/* Test variabili sessione */
		$this->_set_view('generic', array(
			'p'	=> 'Hey, <b>' . $this->User_model->user_name() . '</b>!',
		));
		$this->_set_view('generic', array(
			'p'	=> 'Il tuo ID utente &egrave; <b>' . $this->User_model->id() . '</b>',
		));
		$this->_set_view('generic', array(
			'p'	=> 'Il tuo indirizzo email &egrave; <b>' . $this->User_model->email() . '</b>',
		));
		$this->_set_view('generic', array(
			'p'	=> ($this->User_model->rights() === 1)
						? 'Il tuo &egrave; un account amministratore'
						: 'Il tuo account ha normali permessi utente',
		));
		
		$this->_set_view('generic', array(
			'div'		=> anchor('user/settings', 'Modifica') . ' le informazioni',
			'class'	=> 'test',
		));
		$this->_set_view('generic', array(
			'p'	=> 'Visualizza i tuoi ' . anchor('user/sells', 'annunci'),
		));
		$this->_set_view('generic', array(
			'p'	=> 'Visualizza le ' . anchor('user/requests', 'richieste'),
		));

		$this->_view();
	}

	public function settings()
	{
		$this->load->helper('form');

		if ($this->input->post('user_name') === FALSE)
		{
			$this->_modify_user_name();
		}
		else
		{
			$this->_update_user_name();
		}
	}

	private function _modify_user_name()
	{
		$this->_set_view('form/single_field', array(
			'action'			 => 'user/settings',
			'label'				 => 'Modifica nome utente',
			'submit_name'	 => 'modify_user_name',
			'submit_value' => 'Modifica',
			'input'				 => array(
					'name'				=> 'user_name',
					'maxlength'		=> '20',
					'id'					=> 'modify_user_name',
					'value'				=> $this->User_model->user_name(),
			),
		));
		$this->_view();
	}

	private function _update_user_name()
	{
		if ($this->User_model->update_user_name($this->input->post('user_name')))
		{
			$this->_set_view('generic', array(
				'p' => 'User name modificato in: ' . $this->User_model->user_name(),
			));
		}
		else
		{
			$this->_set_view('generic', array(
				'p'		=> 'User name non valido o gi&agrave; in uso',
				'id'	=> 'error',
			));
		}

		$this->_view();
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
