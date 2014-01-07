<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller  {

	public function __construct()
	{
		parent::__construct();
		//$this->output->set_header('Content-Type: text/html; charset=utf-8');
		$this->output->set_header('Content-Type: text/html; charset=' . config_item('charset'));
	}

}