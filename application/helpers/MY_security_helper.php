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
 * MY_security_helper
 * 
 * Adds functions to the CI security helper.
 *
 * @package UniBooks
 * @category Helpers
 * @author Emiliano Bovetti
 */

// ------------------------------------------------------------------------

require_once PHPASS_PATH . 'PasswordHash.php';

/**
 * Uses the PHPass library to make secure hash.
 *
 * @param string  $str the string to hash
 * @return string  (hashed)
 * @access public 
 */
function do_hash( $str )
{
    $hasher = new PasswordHash(8, FALSE);
    return $hasher->HashPassword($str);
}

/**
 * Uses the PHPass library to check the validity of an hash.
 *
 * @param   string  $hashed_password the hashed password
 * @param string  $str the password provided (not hashed)
 * @return boolean
 * @access public 
 */
function check_hash( $hashed_password, $str )
{
    $hasher = new PasswordHash(8, FALSE);
    return $hasher->CheckPassword($str, $hashed_password);
}

/* End of file MY_security_helper.php */
/* Location: ./application/helpers/MY_security_helper.php */