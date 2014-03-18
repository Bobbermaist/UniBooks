<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package UniBooks
 * @author Emiliano Bovetti
 * @since Version 1.0
 */

/**
 * UniBooks User class.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class User extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_restrict_area(USER_RIGHTS);
	}

	public function index()
	{
				/* Test variabili sessione */
		$this->_set_view('generic', array('p'	=> 'Hey, <b>' . $this->User_model->get_user_name() . '</b>!'));
		$this->_set_view('generic', array('p'	=> 'Il tuo ID utente &egrave; <b>' . $this->User_model->get_id() . '</b>'));
		$this->_set_view('generic', array('p'	=> 'Il tuo indirizzo email &egrave; <b>' . $this->User_model->get_email() . '</b>'));
		
		if ($this->User_model->get_rights() === USER_RIGHTS)
		{
			$this->_set_view('generic', array(
				'p'	=> 'Il tuo account ha normali permessi utente',
			));
		}
		elseif ($this->User_model->get_rights() === ADMIN_RIGHTS)
		{
			$this->_set_view('generic', array(
				'p'	=> 'Il tuo &egrave; un account amministratore',
			));
		}
		// else{ who the fuck are you? }
		
		$this->_set_view('generic', array(
			'div'		=> anchor('user/settings', 'Modifica') . ' le informazioni',
			'class'	=> 'test',
		));
		$this->_set_view('generic', array('p'	=> 'Visualizza i tuoi ' . anchor('user/sells', 'annunci')));
		$this->_set_view('generic', array('p'	=> 'Visualizza le ' . anchor('user/requests', 'richieste')));

		$this->_set_view('generic', array('p'	=> anchor('user/logout', 'logout')));

		$this->_view();
	}

	public function settings($confirm_code = NULL)
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

		if ($confirm_code !== NULL)
		{
			$this->_update_email($confirm_code);
		}
		elseif ($this->input->post('email') === FALSE)
		{
			$this->_ask_for_modify_email();
		}
		else
		{
			$this->_modify_email();
		}

		if ($this->input->post('old_password') === FALSE)
		{
			$this->_modify_password();
		}
		else
		{
			$this->_update_password();
		}

		$this->_set_view('generic', array(
			'p'	=> anchor('user/logout', 'logout'),
		));

		$this->_view();
	}

	private function _modify_user_name()
	{
		$this->_set_view('form/single_field', array(
			'action'			=> 'user/settings',
			'label'				=> 'Modifica nome utente',
			'submit_name'	=> 'modify_user_name',
			'submit_value'=> 'Modifica',
			'input'				=> array(
					'name'				=> 'user_name',
					'maxlength'		=> '20',
					'id'					=> 'modify_user_name',
					'value'				=> $this->User_model->get_user_name(),
			),
		));
	}

	private function _update_user_name()
	{
		$this->_try('User_model', 'update_user_name', $this->input->post('user_name'));
		$this->_set_message('user_name_update');
	}

	private function _ask_for_modify_email()
	{
		$this->_set_view('form/single_field', array(
			'action'				=> 'user/settings',
			'label'					=> 'Modifica email',
			'submit_name'		=> 'modify_email',
			'submit_value'	=> 'Modifica',
			'input'					=> array(
						'name'			=> 'email',
						'maxlength'	=> '64',
						'id'				=> 'modify_email',
						'value'			=> $this->User_model->get_email(),
			),
		));
	}

	private function _modify_email()
	{
		$this->_try('User_model', 'ask_for_update_email', $this->input->post('email'));
		$this->_set_message('email_update_request');
		if ($this->exception_code === NO_EXCEPTIONS)
		{
			$this->_send_confirm();
		}
	}

	private function _send_confirm()
	{
		$this->load->library('email');

		$this->email->from('reset@unibooks.it');
		$this->email->to($this->User_model->get_tmp_email());
		$this->email->subject('Conferma email');

		$email_data = array(
			'user_name'	=> $this->User_model->get_user_name(),
			'link'			=> $this->User_model->get_confirm_link('user/settings', FALSE),
		);
		$this->email->message( $this->load->view('email/confirm', $email_data, TRUE) );
		$this->email->send();
		echo $this->email->print_debugger();
	}

	private function _update_email($confirm_code)
	{
		$this->_try('User_model', 'update_email', $confirm_code);
		$this->_set_message('email_update');
	}

	private function _modify_password()
	{
		$this->_set_view('form/modify_password', array(
			'old_password'	=> array(
					'name'				=> 'old_password',
					'maxlength'		=> '64',
			),
			'new_password'	=> array(
					'name'				=> 'new_password',
					'maxlength'		=> '64',
			),
			'passconf'	=> array(
				'name'			=> 'passconf',
				'maxlength'	=> '64',
			),
		));
	}

	private function _update_password()
	{
		$this->_try('User_model', 'update_password', $this->input->post('old_password'), $this->input->post('new_password'));
		$this->_set_message('password_update');
	}

	public function logout()
	{
		$this->User_model->logout();
	}
}

// END User class

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
