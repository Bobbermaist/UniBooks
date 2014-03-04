<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * UniBooks MY_Loader Class
 *
 * Extends the default loader class.
 *
 * @package UniBooks
 * @category Loader
 * @author Emiliano Bovetti
 */
class MY_Loader extends CI_Loader {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * The variables loaded in this way
	 *
	 * $this->load->vars($var) or $this->load->view('name', $var)
	 *
	 * are cached by CodeIgniter.
	 *
	 * This method allows to unset all these cached variables.
	 *
	 * @return void
	 */
	public function clean_cached_vars()
	{
		$this->_ci_cached_vars = array();
		//$this->_ci_cached_vars = (new CI_Loader)->_ci_cached_vars;
	}
}

/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */