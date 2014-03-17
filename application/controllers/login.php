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
 * UniBooks Login class.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->_set_view('form/login', array(
  		'user_name' => array(
  			'name'			=> 'user_name',
  			'maxlength'	=> '20',
  			'value'			=> $this->input->post('user_name'),
  		),
  		'password' => array(
  			'name'			=> 'password',
  			'maxlength'	=> '64',
  		),
  	));

		if ($this->form_validation->run() === TRUE)
		{
			$this->_try('User_model', 'login',
					$this->input->post('user_name'), $this->input->post('password'));
			
			if ($this->exception_code === NO_EXCEPTIONS) // log in OK, redirect in
			{
				$redirect = $this->User_model->userdata('redirect');
				if ($redirect === FALSE)
				{
					redirect('user');
				}
				else
				{
					$this->User_model->del_userdata('redirect');
					redirect($redirect);
				}
			}
			elseif ($this->exception_code === ACCOUNT_NOT_CONFIRMED)
			{
				$this->_set_view('generic', array(
					'p'		=> 'Errore nel login (account non confermato)',
					'id'	=> 'error',
				));
			}
			elseif ($this->exception_code === WRONG_PASSWORD)
			{
				$this->_set_view('generic', array(
					'p'		=> 'Errore nel login (password errata)',
					'id'	=> 'error',
				));
			}
			// else { unknown error }

		}

		$this->_view();
	}
}

// END Login class

/* End of file login.php */
/* Location: ./application/controllers/login.php */ 
