<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('User_model');
		if( ! $this->session->userdata('ID') )
		{
			$this->session->set_userdata(array('redirect' => 'account'));
			redirect('user/login');
		}
	}

	public function index()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');

		$user = $this->session->all_userdata();
		$view_data['p'] = array(
			'User name: ' . $user['user_name'] . ' ' . anchor('account/user_name', 'Modifica'),
			'Email: ' . $user['email'] . ' ' . anchor('account/email', 'Modifica'),
			anchor('account/password', 'Modifica password'),
			'Visualizza ' . anchor('account/sells', 'annunci'),
			'Visualizza ' . anchor('account/requests', 'richieste'),
		);
		$this->load->view('paragraphs', $view_data);
		$this->load->view('template/coda');
	}

	public function user_name()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->config('form_data');
		$user = $this->session->all_userdata();
		if( ! $post = $this->input->post() )
		{
			$form_data = $this->config->item('change_user_name_data');
			$form_data['input_type']['value'] = $user['user_name'];
			$this->load->view('form/single', $form_data);
		}
		else
		{
			$this->User_model->update_by_ID($user['ID'], $this->User_model->create_user_data($post, FALSE));
			$this->session->set_userdata(array('user_name' => $post['user_name']));
			$this->load->view('paragraphs', array('p' => 'User name modificato in: ' . $this->session->userdata('user_name')));
		}
		$this->load->view('template/coda');
	}

	public function email($user_id = NULL, $confirm_code = NULL)
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->config('form_data');
		$msg = '';
		$user = $this->session->all_userdata();
		if( $this->User_model->check_tmp($user_id, 'confirm_email', $confirm_code) )
		{
			$new_email = $this->User_model->get_tmp($user_id, 'tmp_email');
			$this->User_model->update_by_ID($user_id, array('email' => $new_email));
			$this->User_model->empty_tmp($user_id, array('tmp_email', 'confirm_email'));
			$msg = 'L\'indirizzo ' . $new_email . ' &egrave; stato confermato correttamente';
		}
		elseif( ! $post = $this->input->post() )
		{
			$form_data = $this->config->item('change_email_data');
			$form_data['input_type']['value'] = $user['email'];
			$this->load->view('form/single', $form_data);
		}
		else
		{
			$user_data = array(
				'tmp_email'			=> $post['email'],
				'confirm_email'	=> substr(md5(rand()),0,15)
			);
			if( $this->User_model->insert_tmp($user['ID'], $user_data) )
			{
				$this->send_confirm($user_data);
				$msg = '&Egrave; stata inviata un\'email di conferma all\'indirizzo indicato';
			}
			else
				$msg = 'C\'&egrave; gi&agrave; una richiesta per questo account';
		}
		$this->load->view('paragraphs', array('p' => $msg));
		$this->load->view('template/coda');
	}

	private function send_confirm($user_data)
	{
		$this->load->library('email');
		$this->email->from('reset@unibooks.it');
		$this->email->to($user_data['tmp_email']);
		$this->email->subject('Conferma email');
		$email_data = array(
			'link' => site_url('account/email/'.$this->session->userdata('ID').'/'.$user_data['confirm_email'])
		);
		$msg = $this->load->view('email/confirm', $email_data, TRUE);
		$this->email->message($msg);
		$this->email->send();
		echo $this->email->print_debugger();
	}

	public function password()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->library('form_validation');
		$this->load->config('form_data');
		$user = $this->session->all_userdata();
		$msg = '';
		if( $post = $this->input->post() AND $this->form_validation->run() )
		{
			if( $this->User_model->login($this->User_model->select_where('ID', $user['ID']), $post['old_pass']) )
			{
				$user_data = $this->User_model->create_user_data(array('pass' => $post['new_pass']), FALSE);
				$this->User_model->update_by_ID($user['ID'], $user_data);
				$msg = 'Password modificata correttamente';
			}
			else
				$msg = 'La password immessa non &egrave; corretta';
		}
		$this->load->view('form/new_password', $this->config->item('change_password_data'));
		$this->load->view('paragraphs', array('p' => $msg));
		$this->load->view('template/coda');
	}

	public function sells()
	{
		$this->load->model('Sell_model');
		$this->load->view('template/head');
		$this->load->view('template/body');

		$books = $this->Sell_model->get($this->session->userdata('ID'));
		if( $books )
		{
			$this->load->view('paragraphs', array('p' => 'Libri in vendita'));
			foreach($books as $book)
			{
				$this->load->view('book', $book);
				$this->load->view('paragraphs', array('p' => '€ ' . $book['price']));
			}
		}
		else
			$this->load->view('paragraphs', array('p' => 'Nessun libro in vendita'));

		$this->load->view('template/coda');
	}

	public function requests()
	{
		$this->load->model('Request_model');
		$this->load->view('template/head');
		$this->load->view('template/body');
		$books = $this->Request_model->get($this->session->userdata('ID'));
		if( $books )
		{
			$this->load->view('paragraphs', array('p' => 'Richieste inserite'));
			foreach($books as $book)
			{
				$this->load->view('book', $book);
			}
		}
		else
			$this->load->view('paragraphs', array('p' => 'Nessuna richiesta inserita'));

		$this->load->view('template/coda');
	}
}

/* End of file account.php */
/* Location: ./application/controllers/account.php */ 
 
