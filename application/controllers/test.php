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
		for($c = 0; $c < 256; $c++)
		{
			$char = chr($c);
			if ($char != ' ')
				echo $c . ': ' . $char;
			echo '<br>';
		}
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
