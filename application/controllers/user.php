<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->helper('form');
		$user = $this->session->all_userdata();
		if( ! isset($user['ID']) )
			$this->load->view('form/login');
		else
		{		/* Test variabili sessione */
			$this->load->view('par', array('par' => 'Hey, <b>'.$user['user_name'].'!</b>'));
			$this->load->view('par', array('par' => 'Il tuo ID utente &egrave; <b>'.$user['ID'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo indirizzo email &egrave; <b>'.$user['email'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo session_id &egrave; <b>'.$user['session_id'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo ip_address &egrave; <b>'.$user['ip_address'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo user_agent &egrave; <b>'.$user['user_agent'].'</b>'));
			$this->load->view('par', array('par' => 'La tua last_activity &egrave; <b>'.$user['last_activity'].'</b>'));
		}
		$this->load->view('template/coda');
	}

	public function registration()
	{
			/* Load */
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->config('form_validation');
		$this->load->database();
		$this->load->view('template/head');
		$this->load->view('template/body');

		$valid = FALSE;
		if( ($post = $this->input->post()) )
			$valid = $this->form_validation->run('signup');
		if( ! $valid )
			$this->load->view('form/registration');
		else
		{
			$user_data = array(
				'user_name' => $post['user_name'],
				'pass' => sha1($post['pass']),
				'email' => $post['email'],
				'activation_key' => substr(md5(rand()),0,15),
				'registration_time' => date("Y-m-d H:i:s")
			);
			$user_data['ID'] = $this->User_model->insert_user($user_data);
			$this->send_activation($user_data);
		}

		$this->load->view('template/coda');
	}

	private function send_activation($user_data)
	{
		$this->load->library('email');
		$this->email->from('registration@unibooks.it');
		$this->email->to($user_data['email']);
		$this->email->subject('Attivazione account');
		$email_data = array(
				'user_name' => $user_data['user_name'],
				'link' => site_url('user/activation/'.$user_data['ID'].'/'.$user_data['activation_key'])
			);
		$msg = $this->load->view('email/signup', $email_data, TRUE);
		$this->email->message($msg);
		$this->email->send();
		echo $this->email->print_debugger();
	}

	public function activation($ID, $activation_key)
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->model('User_model');
		$user = $this->User_model->select_where('ID', $ID);
		if( $user == NULL )
		{
			$msg = 'ID non presente nel database';
		}
		else
		{
			if( $user->rights > -1 )
			{
				$msg = 'Utente già registrato';
			}
			elseif( strcmp($activation_key, $user->activation_key) == 0 )
			{
				$data = array('rights' => 0, 'activation_key' => '');
				$this->User_model->update_by_ID($user->ID, $data);
				$msg = 'Attivazione effettuata con successo';
			}
			else
				$msg = 'Activation key errata';
		}
		$this->load->view('par', array( 'par' => $msg ));
		$this->load->view('template/coda');
	}

	public function reset()
	{
			/* Load */
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->view('template/head');
		$this->load->view('template/body');

		$post = $this->input->post();
		if( ! $post )
			$this->load->view('form/reset');
		else
		{
			$user = $this->User_model->select_where('email', $post['user_or_email']);
			if( ! $user )
				$user = $this->User_model->select_where('user_name', $post['user_or_email']);
			if( $user )
			{
				$msg = 'Hey '.$user->user_name.' ti &egrave; stata inviata un\'email 
						con le istruzioni per effettuare il reset della password ;)';
				$user_data = array(
					'ID'				=> $user->ID,
					'user_name' => $user->user_name,
					'email'			=> $user->email,
					'activation_key'	=> substr(md5(rand()),0,15)
				);
				$this->User_model->update_by_ID($user->ID, array('activation_key' => $user_data['activation_key']));
				$this->send_reset($user_data);
			}
		}

		$this->load->view('template/coda');
	}

	private function send_reset($user_data)
	{
		$this->load->library('email');
		$this->email->from('reset@unibooks.it');
		$this->email->to($user_data['email']);
		$this->email->subject('Reset password');
		$email_data = array(
				'user_name' => $user_data['user_name'],
				'link' => site_url('user/choose_new_pass/'.$user_data['ID'].'/'.$user_data['activation_key'])
			);
		$msg = $this->load->view('email/reset', $email_data, TRUE);
		$this->email->message($msg);
		$this->email->send();
		echo $this->email->print_debugger();
	}

	public function choose_new_pass($ID, $activation_key)
	{
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->view('template/head');
		$this->load->view('template/body');
		$user = $this->User_model->select_where('ID', $ID);
		if( $user != NULL AND $user->rights > -1 AND strcmp($activation_key, $user->activation_key) == 0 )
		{
			$data = array( 'ID' => $user->ID, 'activation_key' => $user->activation_key );
			$this->load->view('form/new_password', $data);
		}
		$this->load->view('template/coda');
	}

	public function reset_pass()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->model('User_model');
		$post = $this->input->post();
		$user = $this->User_model->select_where('ID', $post['ID']);
		if( $user != NULL AND $user->rights > -1 AND strcmp($post['activation_key'], $user->activation_key) == 0 )
		{
			$data = array('pass' => sha1($post['pass']), 'activation_key' => '');
			$this->User_model->update_by_ID($user->ID, $data);
			$msg = 'La password &egrave; stata resettata con successo';
		}
		else
			$msg = 'Errore nel reset password';
		$data = array( 'par' => $msg );
		$this->load->view('par', $data);
		$this->load->view('template/coda');
	}

	public function login()
	{
			/* Load */
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->config('form_validation');
		$this->load->database();

		$valid = FALSE;
		$post = $this->input->post();
		$valid = $this->form_validation->run('login');
		$user = $this->User_model->select_where('user_name', $post['user_name']);
		if( $valid AND $user != NULL AND strcmp($user->pass, sha1($post['pass'])) == 0 )
		{
			$session = array(
				'ID'					=> $user->ID,
				'user_name'		=> $user->user_name,
				'email'				=> $user->email
			);
			$this->session->set_userdata($session);
			redirect('user/index');
		}
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/login');
		$this->load->view('validation_errors');
		$this->load->view('template/coda');
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
