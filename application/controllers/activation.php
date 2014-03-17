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
 * UniBooks Activation class.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Activation extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index($id = NULL, $activation_key = NULL)
	{
		$this->User_model->set_id($id);

		$this->_try('User_model', 'activate', $activation_key);

		if ($this->exception_code === NO_EXCEPTIONS)
		{
			$this->_set_view('generic', array(
				'p' => 'Attivazione effettuata con successo',
			));
		}
		if ($this->exception_code === ACCOUNT_ALREADY_CONFIRMED)
		{
			$this->_set_view('generic', array(
				'p'		=> 'Impossibile attivare l\'account (account già attivato)',
				'id'	=> 'error',
			));
		}
		elseif ($this->exception_code === WRONG_CONFIRM_CODE)
		{
			$this->_set_view('generic', array(
				'p'		=> 'Impossibile attivare l\'account (codice di conferma errato)',
				'id'	=> 'error',
			));
		}
		//else { unknown error }

		$this->_view();
	}
}

// END Activation class

/* End of file activation.php */
/* Location: ./application/controllers/activation.php */ 
