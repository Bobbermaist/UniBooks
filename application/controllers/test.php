<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once THIRD_PARTY . 'utf8/portable-utf8.php';

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
    $encode = url_encode_utf8($str);
    echo $encode;
    echo '<br><br>';
    echo url_decode_utf8($encode);
    echo '<br><br>';
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
