<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('url');
	}

	public function index()
	{
		$this->load->model('Book_model');
		$this->rrmdir(GOOGLE_CACHE);
	}

	public function rrmdir($dir)
	{
		foreach(glob($dir . '/*') as $file)
		{
			if(is_dir($file))
				$this->rrmdir($file);
			else
			unlink($file);
		}
		rmdir($dir);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
