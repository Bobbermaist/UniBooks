<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('SRC_PATH', APPPATH . '/third_party/google_api/src/');

require_once SRC_PATH . 'Google_Client.php';
require_once SRC_PATH . 'contrib/Google_BooksService.php';

class MY_books {

	//var $CI;
	var $service;

  public function __construct()
  {
		//$this->CI =& get_instance();
  	$client = new Google_Client();
  	$this->service = new Google_BooksService($client);
  }

  public function get($data)
  {
  	if( ! is_array($data) )
  		return $this->service->volumes->listVolumes($data);
  	$query = isset($data['title']) ? 'intitle:' . $data['title'] . ' ' : '';
  	$query .= isset($data['author']) ? 'inauthor:' . $data['author'] . ' ' : '';
  	$query .= isset($data['publisher']) ? 'inpublisher:' . $data['publisher'] . ' ' : '';
  	$query .= isset($data['subject']) ? 'subject:' . $data['subject'] . ' ' : '';
  	//$query = substr($query, 0, -1);
  	return $this->service->volumes->listVolumes($query);
  }

  public function get_by_isbn($isbn)
  {
  	return $this->service->volumes->listVolumes("isbn:$isbn");
  }
}

/* End of file My_books.php */
/* Location: ./application/libraries/My_books.php */ 