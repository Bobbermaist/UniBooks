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

// END Activation class

/* End of file activation.php */
/* Location: ./application/controllers/activation.php */ 
