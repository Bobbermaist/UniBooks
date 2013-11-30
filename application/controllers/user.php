<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function registration()
	{
			/* Load */
		$this->load->model('User_model');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->config('form_validation');

		$valid = FALSE;
		$this->load->view('head');
		$this->load->view('body');
		if( ($post = $this->input->post()) )
			$valid = $this->form_validation->run('signup');
		if( ! $valid )
			$this->load->view('registration');
		else
		{
			$user_data = array(
				'user_name' => $post['user_name'],
				'pass' => sha1($post['pass']),
				'email' => $post['email'],
				'activation_key' => substr(md5(rand()),0,15),
				'registration_time' => date("Y-m-d H:i:s")
			);
			$this->User_model->insert_user( $user_data );
			$this->send_activation($user_data);
			echo 'succes!';
		}

		$this->load->view('coda');
	}

	private function send_activation($user_data)
	{
		$this->load->library('email');
		$this->email->from('registration@unibooks.it');
		$this->email->to('$post_data["email"]');
		$this->email->subject('Attivazione account');
		$email_data = array(
				'user_name' => $user_data['user_name'],
				'link' => site_url('user/activation/'.$user_data['user_name'].'/'.$user_data['activation_key'])
			);
		$msg = $this->load->view('signup_email', $email_data, TRUE);
		$this->email->message( $msg );
		$this->email->send();
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
