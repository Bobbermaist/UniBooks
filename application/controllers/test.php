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
		$this->load->helper('file');
		delete_files(GOOGLE_CACHE, TRUE);
		//$this->empty_dir(GOOGLE_CACHE);
	}

	public function empty_dir($directory, $delete = FALSE)
	{
		foreach(glob($directory . '*') as $item)
		{
			if (is_dir($item))
				$this->empty_dir($item . '/', TRUE);
			else
				unlink($item);
		}
		if ($delete === TRUE)
			rmdir($directory);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
