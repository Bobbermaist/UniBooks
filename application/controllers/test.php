<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index($code = NULL)
	{
		foreach ($this as $p_name => $p_value)
		{
			echo '$p_name = ', $p_name, '<br>';
		}
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
