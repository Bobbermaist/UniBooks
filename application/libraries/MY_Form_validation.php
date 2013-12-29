<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

	var $CI;

  public function __construct($rules = array())
  {
  	parent::__construct($rules);
  	$this->CI =& get_instance();
  }

  public function valid_email($str)
  {
  	return filter_var($str, FILTER_VALIDATE_EMAIL);
  }

	public function valid_price($str)
	{
		return ( ! preg_match('/^([0-9]+)([\.|,][0-9]{1,2})?$/', $str)) ? FALSE : TRUE;
	}
	
	public function valid_ISBN($str)
	{
		$this->CI->load->model('Book_model');
		return $this->CI->Book_model->setISBN($str);
	}
}

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */