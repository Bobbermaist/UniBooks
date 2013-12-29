<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('User_model');
	}

	public function index()
	{
		$this->load->config('form_data');
		$this->load->helper('form');
		$user = $this->session->all_userdata();
		if ( ! isset($user['ID']))
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
				'La tua last_activity &egrave; <b>'.$user['last_activity'].'</b>',
				($user['rights'] === 1) ?
					'Il tuo &egrave; un account amministratore' :
					'Il tuo account ha normali permessi utente'
			));
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view_name, $view_data);
		$this->load->view('template/coda');
	}

	public function registration()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->config('form_data');
		$this->load->database();

		if ($post = $this->input->post())
		{
			$signup_data['user_name_data']['value'] = $post['user_name'];
			$signup_data['email_data']['value'] = $post['email'];
		}
		if ( ! $this->form_validation->run())
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
		$this->load->helper('security');

		$this->email->from('registration@unibooks.it');
		$this->email->to($user_data['email']);
		$this->email->subject('Attivazione account');
		$email_data = $this->User_model->create_email_data($user_data, 'user/activation');
		$this->email->message( $this->load->view('email/signup', $email_data, TRUE) );
		$this->email->send();
		echo $this->email->print_debugger();
	}

	public function activation($ID = NULL, $activation_key = NULL)
	{
		$this->User_model->select_where('ID', $ID);
		if ($this->User_model->activate($activation_key) === TRUE)
		{
			$view_data = array('p' => 'Attivazione effettuata con successo');
		}
		else
		{
			$view_data = array('p' => 'Impossibile attivare l\'account');
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('template/coda');
	}

	public function reset()
	{
		$this->load->helper('form');
		$this->load->config('form_data');

		$input = $this->input->post('user_or_email');
		$view_name = 'form/reset';
		$view_data = $this->config->item('reset_data');
		$view_data['reset_form_data']['value'] = $input;

		$this->User_model->select_where('email', $input);
		$this->User_model->select_where('user_name', $input);

		if ($reset_data = $this->User_model->reset_request())
		{
			$this->send_reset($reset_data);
			$view_name = 'paragraphs';
			$view_data = array('p' => 'Hey '.$reset_data['user_name'].' ti &egrave; stata inviata un\'email 
				con le istruzioni per effettuare il reset della password');
		}
		elseif ($input)
			$view_error = array('p' => 'I parametri inseriti non corrispondono a nessun utente 
				oppure c\'&egrave; una richiesta pendente di reset');

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view_name, $view_data);
		if (isset($view_error)) $this->load->view('paragraphs', $view_error);
		$this->load->view('template/coda');
	}

	private function send_reset($user_data)
	{
		$this->load->library('email');
		$this->email->from('reset@unibooks.it');
		$this->email->to($user_data['email']);
		$this->email->subject('Reset password');
		$email_data = $this->User_model->create_email_data($user_data, 'user/choose_new_pass');
		$this->email->message( $this->load->view('email/reset', $email_data, TRUE) );
		$this->email->send();
		echo $this->email->print_debugger();
	}

	public function choose_new_pass($ID = NULL, $confirm_code = NULL)
	{
		$this->load->helper('form');
		$this->load->config('form_data');

		$ID = intval($ID);
		$this->User_model->select_where('ID', $ID);
		if ($this->User_model->check_reset($confirm_code))
		{
			$view_name = 'form/reset_password';
			$view_data = $this->config->item('new_password_data');
			$view_data['ID'] = $ID;
			$view_data['confirm_code'] = $confirm_code;
		}
		else
		{
			$view_name = 'paragraphs';
			$view_data = array('p' => 'Errore');
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view_name, $view_data);
		$this->load->view('template/coda');
	}

	public function reset_pass()
	{
		$post = $this->input->post();
		$this->User_model->select_where('ID', $post['ID']);
		if ($this->User_model->reset($post['confirm_code'], $post['pass']))
		{
			$view_data = array('p' => 'La password &egrave; stata resettata con successo');
		}
		else
		{
			$view_data = array('p' => 'Errore nel reset password');
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('template/coda');
	}

	public function login()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->config('form_data');
		
		$post = $this->input->post();
		if ($this->form_validation->run())
		{
			$this->User_model->select_where('user_name', $post['user_name']);
			if ($this->User_model->login($post['pass']))
			{
				redirect($this->session->userdata('redirect') ?
					$this->session->userdata('redirect') :
					'user');
			}
			else
				$view_error = array('p' => 'Errore nel login');
		}
		$login_data = $this->config->item('login_data');
		$login_data['user_name']['value'] = $post['user_name'];

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('form/login', $login_data);
		if (isset($view_error)) $this->load->view('paragraphs', $view_error);
		$this->load->view('template/coda');
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */ 
