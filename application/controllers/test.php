<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
		redirect('test/replace');
	}

	public function replace($str = 'intitle:l\'uccello del sole ★ inauthor:wilbur smith')
	{
    $this->load->helper('security');
    $str = '★';
    $encode = url_encode($str);
    echo $encode;
    echo '<br><br>';
    echo url_decode($encode);
    echo '<br><br>';
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
