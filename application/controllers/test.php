<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

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
    header('Content-type: text/html; charset=utf-8');
    $this->load->helper('security');
    $str = '★';
    $encode = url_encode_utf8($str);
    echo $encode;
    echo '<br><br>';
    echo url_decode_utf8($encode);
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
