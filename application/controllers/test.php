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
		$ran_utf8 = utf8_encode(get_random_bytes(15));
		$ran_url = urlencode($ran_utf8);
		echo $ran_utf8 . '<br>';
		echo $ran_url . '<br>';
		if($ran_utf8 === urldecode($ran_url))
			echo 'TRUE';
		else
			echo 'FALSE';
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
