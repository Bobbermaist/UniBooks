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
		$this->load->config('form_data');
		$this->load->helper('form');
		$user = $this->session->all_userdata();
		if( ! isset($user['ID']) )
			$this->load->view('form/login', $this->config->item('login_data'));
		else
		{		/* Test variabili sessione */
			$this->load->view('par', array('par' => 'Hey, <b>'.$user['user_name'].'!</b>'));
			$this->load->view('par', array('par' => 'Il tuo ID utente &egrave; <b>'.$user['ID'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo indirizzo email &egrave; <b>'.$user['email'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo session_id &egrave; <b>'.$user['session_id'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo ip_address &egrave; <b>'.$user['ip_address'].'</b>'));
			$this->load->view('par', array('par' => 'Il tuo user_agent &egrave; <b>'.$user['user_agent'].'</b>'));
			$this->load->view('par', array('par' => 'La tua last_activity &egrave; <b>'.$user['last_activity'].'</b>'));
			if( $user['rights'] == 0 )
				$this->load->view('par', array('par' => 'Il tuo account ha normali permessi utente'));
			elseif( $user['rights'] == 1 )
				$this->load->view('par', array('par' => 'Il tuo &egrave; un account amministratore'));
		}
		$this->load->view('template/coda');
	}

	public function registration()
	{
			/* Load */
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->config('form_data');
		$this->load->database();
		$this->load->view('template/head');
		$this->load->view('template/body');

		$valid = FALSE;
		$signup_data = $this->config->item('signup_data');
		//$this->form_validation->set_rules($this->config->item('signup_rules'));
		if( ($post = $this->input->post()) )
		{
			$valid = $this->form_validation->run();
			$signup_data['user_name_data']['value'] = $post['user_name'];
			$signup_data['email_data']['value'] = $post['email'];
			//$signup_data['pass_data']['value'] = $post['pass'];
			//$signup_data['passconf_data']['value'] = $post['passconf'];
		}
		if( ! $valid )
			$this->load->view('form/registration', $signup_data);
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
		$this->load->config('form_data');
		$this->load->view('template/head');
		$this->load->view('template/body');

		$msg = '';
		$reset_data = $this->config->item('reset_data');
		$input = $this->input->post('user_or_email');
		$reset_data['reset_form_data']['value'] = $input;
		$user = $this->User_model->select_where('email', $input);
		if( ! $user )
			$user = $this->User_model->select_where('user_name', $input);
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
		elseif( $input )
			$msg = 'I parametri inseriti non corrispondono a nessun utente';
		$this->load->view('form/reset', $reset_data);
		$this->load->view('par', array('par' => $msg));
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
		$this->load->config('form_data');
		$this->load->view('template/head');
		$this->load->view('template/body');
		$user = $this->User_model->select_where('ID', $ID);
		if( $user != NULL AND $user->rights > -1 AND strcmp($activation_key, $user->activation_key) == 0 )
		{
			$reset_data = $this->config->item('new_password_data');
			$reset_data['ID'] = $user->ID;
			$reset_data['activation_key'] = $user->activation_key;
			$this->load->view('form/new_password', $reset_data);
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
		$this->load->config('form_data');
		$this->load->database();

		$valid = FALSE;
		$post = $this->input->post();
		//$this->form_validation->set_rules($this->config->item('login_rules'));
		$valid = $this->form_validation->run();
		$user = $this->User_model->select_where('user_name', $post['user_name']);
		if( $valid AND $user != NULL AND strcmp($user->pass, sha1($post['pass'])) == 0 )
		{
			$session = array(
				'ID'					=> $user->ID,
				'rights'			=> $user->rights,
				'user_name'		=> $user->user_name,
				'email'				=> $user->email
			);
			$this->session->set_userdata($session);
			redirect('user');
		}
		$login_data = $this->config->item('login_data');
		$login_data['user_name']['value'] = $post['user_name'];

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/login', $login_data);
		$this->load->view('validation_errors');
		$this->load->view('template/coda');
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
