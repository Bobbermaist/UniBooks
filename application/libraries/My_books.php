<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('SRC_PATH', APPPATH . '/third_party/google_api/src/');

require_once SRC_PATH . 'Google_Client.php';
require_once SRC_PATH . 'contrib/Google_BooksService.php';

class My_books {

	//var $CI;
	var $service;

  public function __construct()
  {
		//$this->CI =& get_instance();
  	$client = new Google_Client();
  	$this->service = new Google_BooksService($client);
  }

  public function get($str)
  {
  	return $this->service->volumes->listVolumes($str);
  }
}

/* End of file My_books.php */
/* Location: ./application/libraries/My_books.php */ 