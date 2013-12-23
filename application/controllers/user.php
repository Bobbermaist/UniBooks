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
		$this->load->config('form_data');
		$this->load->helper('form');
		$user = $this->session->all_userdata();
		if( ! isset($user['ID']) )
		{
			$view_name = 'form/login';
			$view_data = $this->config->item('login_data');
		}
		else
		{		/* Test variabili sessione */
			$view_name = 'paragraphs';
			$view_data = array('p' => array(
				'Hey, <b>'.$user['user_name'].'!</b>',
				'Il tuo ID utente &egrave; <b>'.$user['ID'].'</b>',
				'Il tuo indirizzo email &egrave; <b>'.$user['email'].'</b>',
				'Il tuo session_id &egrave; <b>'.$user['session_id'].'</b>',
				'Il tuo ip_address &egrave; <b>'.$user['ip_address'].'</b>',
				'Il tuo user_agent &egrave; <b>'.$user['user_agent'].'</b>',
				'La tua last_activity &egrave; <b>'.$user['last_activity'].'</b>'
			));
			if( $user['rights'] == 0 )
				array_push($view_data['p'], 'Il tuo account ha normali permessi utente');
			elseif( $user['rights'] == 1 )
				array_push($view_data['p'], 'Il tuo &egrave; un account amministratore');
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view_name, $view_data);
		$this->load->view('template/coda');
	}

	public function registration()
	{
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->config('form_data');
		$this->load->database();
		$valid = FALSE;
		if( $post = $this->input->post() )
		{
			$valid = $this->form_validation->run();
			$signup_data['user_name_data']['value'] = $post['user_name'];
			$signup_data['email_data']['value'] = $post['email'];
		}
		if( ! $valid )
		{
			$view_name = 'form/registration';
			$view_data = $this->config->item('signup_data');
		}
		else
		{
			$user_data = $this->User_model->create_user_data($post);
			$user_data['ID'] = $this->User_model->insert_user($user_data);
			$this->send_activation($user_data);
			$view_name = 'paragraphs';
			$view_data = array('p' => 'Controlla la tua casella email per l\'attivazione account');
 		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view_name, $view_data);
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

	public function activation($ID = NULL, $activation_key = NULL)
	{
		$this->load->model('User_model');
		$user = $this->User_model->select_where('ID', $ID);
		if( ! $user )
			$view_data = array('p' => 'ID non presente nel database');
		else
		{
			if( $user->rights > -1 )
				$view_data = array('p' => 'Utente già registrato');
			elseif( strcmp($activation_key, $user->activation_key) == 0 )
			{
				$data = array('rights' => 0);
				$this->User_model->update_by_ID($user->ID, $data);
				$this->User_model->empty_activation_key($user->ID);
				$view_data = array('p' => 'Attivazione effettuata con successo');
			}
			else
				$view_data = array('p' => 'Activation key errata');
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('template/coda');
	}

	public function reset()
	{
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->config('form_data');

		$input = $this->input->post('user_or_email');
		$user = $this->User_model->select_where('email', $input);
		$user = ( ! $user ) ? $this->User_model->select_where('user_name', $input) : $user;
		$view_name = 'form/reset';
		$view_data = $this->config->item('reset_data');
		$view_data['reset_form_data']['value'] = $input;
		if( $user )
		{
			$user_data = array(
				'ID'						=> $user->ID,
				'user_name'			=> $user->user_name,
				'email'					=> $user->email,
				'confirm_code'	=> substr(md5(rand()),0,15)
			);
			if( $this->User_model->insert_tmp($user->ID, array('confirm_password' => $user_data['confirm_code'])) )
			{
				$this->send_reset($user_data);
				$view_name = 'paragraphs';
				$view_data = array('p' => 'Hey '.$user->user_name.' ti &egrave; stata inviata un\'email 
					con le istruzioni per effettuare il reset della password');
			}
			else
				$view_error = array('p' => 'C\'&egrave; una richiesta pendente di reset per questo account');
		}
		elseif( $input )
			$view_error = array('p' => 'I parametri inseriti non corrispondono a nessun utente');

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view_name, $view_data);
		if( isset($view_error) )
			$this->load->view('paragraphs', $view_error);
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
				'link' => site_url('user/choose_new_pass/'.$user_data['ID'].'/'.$user_data['confirm_code'])
		);
		$msg = $this->load->view('email/reset', $email_data, TRUE);
		$this->email->message($msg);
		$this->email->send();
		echo $this->email->print_debugger();
	}

	public function choose_new_pass($ID = NULL, $confirm_code = NULL)
	{
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->config('form_data');
		$user = $this->User_model->select_where('ID', $ID);
		if( $user AND $user->rights > -1 AND $this->User_model->check_tmp($user->ID, 'confirm_password', $confirm_code) )
		{
			$reset_data = $this->config->item('new_password_data');
			$reset_data['ID'] = $user->ID;
			$reset_data['confirm_code'] = $confirm_code;
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		if( isset($reset_data) )
			$this->load->view('form/reset_password', $reset_data);
		$this->load->view('template/coda');
	}

	public function reset_pass()
	{
		$this->load->model('User_model');
		$post = $this->input->post();
		$user = $this->User_model->select_where('ID', $post['ID']);
		if( $user AND $user->rights > -1 AND $this->User_model->check_tmp($user->ID, 'confirm_password', $post['confirm_code']) )
		{
			$data = $this->User_model->create_user_data(array('pass' => $post['pass']), FALSE);
			$this->User_model->update_by_ID($user->ID, $data);
			$this->User_model->empty_tmp($user->ID, 'confirm_password');
			$view_data = array('p' => 'La password &egrave; stata resettata con successo');
		}
		else
			$view_data = array('p' => 'Errore nel reset password');

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('template/coda');
	}

	public function login()
	{
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->config('form_data');
		
		$post = $this->input->post();
		$valid = $this->form_validation->run();
		$user = $this->User_model->select_where('user_name', $post['user_name']);
		$session_data = $this->User_model->login($user, $post['pass']);
		if( $valid AND $session_data )
		{
			$this->session->set_userdata($session_data);
			$redirect_path = $this->session->userdata('redirect') ? $this->session->userdata('redirect') : 'user';
			redirect($redirect_path);
		}
		$login_data = $this->config->item('login_data');
		$login_data['user_name']['value'] = $post['user_name'];

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/login', $login_data);
		$this->load->view('template/coda');
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
