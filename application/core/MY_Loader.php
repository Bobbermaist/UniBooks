<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

	public function __construct()
	{
		parent::__construct();
	}

	public function clean_cached_vars()
	{
		$this->_ci_cached_vars = array();
		//$this->_ci_cached_vars = (new CI_Loader)->_ci_cached_vars;
	}

	// --------------------------------------------------------------------

}

/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */