<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('url');
	}

	public function index()
	{
		$str = '54x5435436x5x43x5X';
		$correct_res = '5454354365435X';
		echo preg_replace('/[^[0-9]([^[0-9][^X]$)/i', '', $str);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
