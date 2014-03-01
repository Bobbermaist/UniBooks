<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
		$this->benchmark->mark('start');
		for ($i=0; $i < 10000; $i++)
			$this->load->clean_cached_vars();
		$this->benchmark->mark('end');

		echo $this->benchmark->elapsed_time('start', 'end');
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
