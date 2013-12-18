<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

  public function __construct($rules = array())
  {
  	parent::__construct($rules);
  }

	public function valid_price($str)
	{
		//[0-9]+[[\.|,][0-9]{1,2}]?$
		return ( ! preg_match('/^([0-9]+)([\.|,][0-9]{1,2})?$/', $str)) ? FALSE : TRUE;
	}
}

/* End of file Form_validation.php */
/* Location: ./application/libraries/Form_validation.php */