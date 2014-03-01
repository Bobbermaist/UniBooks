<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activation extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index($id = NULL, $activation_key = NULL)
	{
		$this->User_model->id($id);
		if ($this->User_model->activate($activation_key) === TRUE)
		{
			$this->_set_view('generic', array(
				'p' => 'Attivazione effettuata con successo',
			));
		}
		else
		{
			$this->_set_view('generic', array(
				'p'		=> 'Impossibile attivare l\'account',
				'id'	=> 'error',
			));
		}

		$this->_view();
	}
}

/* End of file activation.php */
/* Location: ./application/controllers/activation.php */ 
