<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package UniBooks
 * @author Emiliano Bovetti
 * @since Version 1.0
 */

/**
 * UniBooks MY_Form_validation Class
 *
 * @package UniBooks
 * @category Validation
 * @author Emiliano Bovetti
 */
class MY_Form_validation extends CI_Form_validation {

	/**
	 * CodeIgniter istance
	 *
	 * @var object
	 * @access protected
	 */
	protected $CI;

	/**
	 * Constructor
	 * Get the CodeIgniter istance
	 *
	 * @return void
	 */
  public function __construct($rules = array())
  {
  	parent::__construct($rules);
  	$this->CI =& get_instance();
  }

	/**
	 * Validate an email address.
	 *
	 * @param string
	 * @return boolean
	 */
  public function valid_email($str)
  {
  	return filter_var($str, FILTER_VALIDATE_EMAIL);
  }

	/**
	 * Validate a price.
	 *
	 * @param string
	 * @return boolean
	 */
	public function valid_price($str)
	{
		return ( ! preg_match('/^([0-9]+)([\.|,][0-9]{1,2})?$/', $str)) ? FALSE : TRUE;
	}
	
	/**
	 * Validate an ISBN code.
	 * to be redone
	 *
	 * @param string
	 * @return boolean
	 */
	public function valid_ISBN($str)
	{
		$this->CI->load->model('Book_model');
		//return $this->CI->Book_model->setISBN($str);
	}
}

/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */