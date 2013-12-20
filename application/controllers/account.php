<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
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
		$this->load->config('form_data');
		$this->load->helper('form');
		$user = $this->session->all_userdata();
		$form_data = $this->config->item('account_change_data');
		$form_data['user_name_data']['value'] = $user['user_name'];
		$form_data['email_data']['value'] = $user['email'];

		$this->load->view('form/change_data', $form_data);
		$this->load->view('template/coda');
	}

	public function change()
	{
		$this->load->model('User_model');
		$this->load->view('template/head');
		$this->load->view('template/body');
		$user = $this->session->all_userdata();
		$view_data['p'] = array();
		if( $post = $this->input->post() )
		{
			$user_data = $this->User_model->create_user_data($post, FALSE);
			$this->User_model->update_by_ID($user['ID'], $user_data);
		}
		$this->load->view('template/coda');
	}
}

/* End of file account.php */
/* Location: ./application/controllers/account.php */ 
 
