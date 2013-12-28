<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('User_model');
		if ( ! $this->session->userdata('ID'))
		{
			$this->session->set_userdata(array('redirect' => 'account'));
			redirect('user/login');
		}
	}

	public function index()
	{
		$user = $this->session->all_userdata();
		$view_data['p'] = array(
			'User name: ' . $user['user_name'] . ' ' . anchor('account/user_name', 'Modifica'),
			'Email: ' . $user['email'] . ' ' . anchor('account/email', 'Modifica'),
			anchor('account/password', 'Modifica password'),
			'Visualizza ' . anchor('account/sells', 'annunci'),
			'Visualizza ' . anchor('account/requests', 'richieste'),
		);

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view('paragraphs', $view_data);
		$this->load->view('template/coda');
	}

	public function user_name()
	{
		$this->load->config('form_data');
		$user = $this->session->all_userdata();
		if ( ! $post = $this->input->post())
		{
			$view = 'form/single';
			$data = $this->config->item('change_user_name_data');
			$data['input_type']['value'] = $user['user_name'];
		}
		else
		{
			$view = 'paragraphs';
			$this->User_model->update_by_ID($user['ID'], $this->User_model->create_user_data($post, FALSE));
			$this->session->set_userdata(array('user_name' => $post['user_name']));
			$data = array('p' => 'User name modificato in: ' . $this->session->userdata('user_name'));
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view, $data);
		$this->load->view('template/coda');
	}

	public function email($user_id = NULL, $confirm_code = NULL)
	{
		$this->load->helper('security');
		$this->load->config('form_data');
		$user = $this->session->all_userdata();
		if ($this->User_model->check_tmp($user_id, 'confirm_email', url_decode_utf8($confirm_code)))
		{
			$view = 'paragraphs';
			$new_email = $this->User_model->get_tmp($user_id, 'tmp_email');
			$this->User_model->update_by_ID($user_id, array('email' => $new_email));
			$this->User_model->empty_tmp($user_id, array('tmp_email', 'confirm_email'));
			$data = array('p' => 'L\'indirizzo ' . $new_email . ' &egrave; stato confermato correttamente');
		}
		elseif ( ! $post = $this->input->post())
		{
			$view = 'form/single';
			$data = $this->config->item('change_email_data');
			$data['input_type']['value'] = $user['email'];
		}
		else
		{
			$view = 'paragraphs';
			$user_data = array(
				'tmp_email'			=> $post['email'],
				'confirm_email'	=> get_random_string(15),
			);
			if ($this->User_model->insert_tmp($user['ID'], $user_data))
			{
				$user_data['confirm_email'] = url_encode_utf8($user_data['confirm_email']);
				$this->send_confirm($user_data);
				$data = array('p' => '&Egrave; stata inviata un\'email di conferma all\'indirizzo indicato');
			}
			else
				$data = array('p' => 'C\'&egrave; gi&agrave; una richiesta per questo account');
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view, $data);
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
		$this->load->library('form_validation');
		$this->load->config('form_data');
		$user = $this->session->all_userdata();
		print_r($user);
		if ($post = $this->input->post() AND $this->form_validation->run())
		{
			$view = 'paragraphs';
			$this->User_model->select_where('ID', $user['ID']);
			if ($this->User_model->check_password($post['old_pass']))
			{
				$user_data = $this->User_model->create_user_data(array('pass' => $post['new_pass']), FALSE);
				$this->User_model->update_by_ID($user['ID'], $user_data);
				$data = array('p' => 'Password modificata correttamente');
			}
			else
				$data = array('p' => 'La password immessa non &egrave; corretta');
		}
		else
		{
			$view = 'form/new_password';
			$data = $this->config->item('change_password_data');
		}

		$this->load->view('template/head');
		$this->load->view('template/body');
		$this->load->view($view, $data);
		$this->load->view('template/coda');
	}

	public function sells($page = 1)
	{
		$this->load->library('pagination');
		$this->load->model('Sell_model');
		$books = $this->Sell_model->get($this->session->userdata('ID'));

		$config['base_url'] = site_url('account/sells');
		$config['use_page_numbers'] = TRUE;
		$config['total_rows'] = count($books);
		$config['per_page'] = 3;
		$this->pagination->initialize($config);
		$books_to_show = array_chunk($books, 3);

		$this->load->view('template/head');
		$this->load->view('template/body');
		if ($books)
		{
			$this->load->view('paragraphs', array('p' => 'Libri in vendita'));
			$this->load->view('paragraphs', array('p' => $this->pagination->create_links()));
			foreach($books_to_show[$page - 1] as $book)
			{
				$this->load->view('book', $book);
				$this->load->view('form/delete', array('action' => 'sell/delete', 'book_id' => $book['ID']));
			}
		}
		else
			$this->load->view('paragraphs', array('p' => 'Nessun libro in vendita'));
		$this->load->view('paragraphs', array('p' => 'Inserisci una ' . anchor('sell', 'vendita')));
		$this->load->view('template/coda');
	}

	public function requests($page = 1)
	{
		$this->load->library('pagination');
		$this->load->model('Request_model');
		$books = $this->Request_model->get($this->session->userdata('ID'));

		$config['base_url'] = site_url('account/requests');
		$config['use_page_numbers'] = TRUE;
		$config['total_rows'] = count($books);
		//$config['per_page'] = REQUESTS_PER_PAGE;
		$config['per_page'] = 3;
		$this->pagination->initialize($config);
		//$books_to_show = array_chunk($books, REQUESTS_PER_PAGE);
		$books_to_show = array_chunk($books, 3);

		$this->load->view('template/head');
		$this->load->view('template/body');
		if ($books)
		{
			$this->load->view('paragraphs', array('p' => 'Richieste inserite'));
			$this->load->view('paragraphs', array('p' => $this->pagination->create_links()));
			foreach($books_to_show[$page - 1] as $book)
			{
				$this->load->view('book', $book);
				$this->load->view('form/delete', array('action' => 'request/delete', 'book_id' => $book['ID']));
			}
		}
		else
			$this->load->view('paragraphs', array('p' => 'Nessuna richiesta inserita'));
		$this->load->view('paragraphs', array('p' => 'Inserisci una ' . anchor('request', 'richiesta')));
		$this->load->view('template/coda');
	}
}

/* End of file account.php */
/* Location: ./application/controllers/account.php */ 
 
