<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('url');
	}

	public function index()
	{
		$this->load->helper('security');
		$fh = fopen('/dev/random', 'rb');
		$output = fread($fh, 15);
		echo $output;
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
