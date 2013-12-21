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
			anchor('account/password', 'Modifica password')
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

	public function email($user_id = NULL, $activation_key = NULL)
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->config('form_data');
		$user = $this->session->all_userdata();
		if( ! $post = $this->input->post() )
		{
			$form_data = $this->config->item('change_email_data');
			$form_data['input_type']['value'] = $user['email'];
			$this->load->view('form/single', $form_data);
		}
		else
		{
			$user_data = array(
				'tmp_email' => $post['email'],
				'activation_key' => substr(md5(rand()),0,15)
			);
			$this->User_model->update_by_ID($user['ID'], $user_data);
			$this->load->view('paragraphs', array('p' => '&Egrave; stata inviata un\'email di conferma all\'indirizzo indicato'));
		}
		$this->load->view('template/coda');
	}
}

/* End of file account.php */
/* Location: ./application/controllers/account.php */ 
 
