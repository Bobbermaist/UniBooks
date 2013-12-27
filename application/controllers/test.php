<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('url');
	}

	public function index()
	{
		$count = 16;
		echo strlen(file_get_contents("http://www.random.org/cgi-bin/randbyte?nbytes=$count"));
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
