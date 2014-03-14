<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * UniBooks Custom_exception class.
 *
 * Extends native PHP Exception class.
 * The Custom exception can be constructed
 * with simply the error code.
 *
 * All error codes are defined as contants in
 * ./application/config/constants.php
 * 
 * The custom exception class will construct
 * the parent Exception class with this error
 * code and the message retrieved by 'exception_lang'
 * file.
 *
 * @package UniBooks
 * @category Models
 * @author Emiliano Bovetti
 */
class Custom_exception extends Exception {

	public function __construct($code, Exception $previous = NULL)
	{
		$code = (int) $code;
		
		parent::__construct($this->_get_message($code), $code, $previous);
	}

	/**
	 * Retrieve exception message.
	 * If not exists in exception_lang.php return the
	 * INVALID_EXCEPTION message.
	 *
	 * @param int
	 * @return string
	 * @access private
	 */
	private function _get_message($code)
	{
		$message = $this->lang->line('exception_' . $code);
		if ($message === FALSE)
		{
			return $this->lang->line('exception_' . INVALID_EXCEPTION_CODE);
		}
		return $message;
	}
}

/* End of file Custom_exception.php */
/* Location: ./application/libraries/Custom_exception.php */  
