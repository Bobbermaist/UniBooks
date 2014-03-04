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
 * UniBooks ISBN helper
 *
 * @package UniBooks
 * @category Helpers
 * @author Emiliano Bovetti
 */

// ------------------------------------------------------------------------

/**
 * Validate a 13-digit ISBN.
 *
 * @param	string
 * @return boolean
 * @access public 
 */
function validate_isbn_13( $str )
{
	if (strlen($str) !== 13)
		return FALSE;

	$check = 0;
	for($i = 0; $i < 13; $i+=2) $check += substr($str, $i, 1);
	for($i = 1; $i < 12; $i+=2) $check += 3 * substr($str, $i, 1);
	return (boolean) ($check % 10 == 0);
}

/**
 * Validate a 10-digit ISBN.
 *
 * @param	string
 * @return boolean
 * @access public 
 */
function validate_isbn_10( $str )
{
	if (strlen($str) !== 10)
		return FALSE;

	$a = 0;
	for($i = 0; $i < 10; $i++)
	{
		if ($str[$i] == 'X')
			$a += 10 * intval(10 - $i);
		else
			$a += intval($str[$i]) * intval(10 - $i);
	}
	return (boolean) ($a % 11 == 0);
}

/**
 * cut_isbn() function take an ISBN in input and
 * return a 9-digit code.
 *
 * Cuts the '978' EAN prefix in the 13-digit ISBN
 * and cuts the last digit (witch is the check digits).
 *
 * @param	string
 * @return string
 * @access public 
 */
function cut_isbn( $isbn )
{
	switch (strlen($isbn))
	{
		case 13: return substr($isbn, 3, -1);
		case 10: return substr($isbn, 0, -1);
		default: return $isbn;
	}
}

/**
 * This function take a 9-digit code (output of cut_isbn() function)
 * and return a valid 13-digit ISBN.
 *
 * @param	string
 * @return string
 * @access public 
 */
function uncut_isbn_13( $code )
{
	$check_digit = (38 + ($code[0] * 3) + ($code[1] * 1) + ($code[2] * 3) +
					($code[3] * 1) + ($code[4] * 3) + ($code[5] * 1) + ($code[6] * 3) +
						($code[7] * 1) + ($code[8] * 3)) % 10;
	switch ($check_digit)
	{
		case 0: return '978' . $code . '0';
		default: return '978' . $code . (10 - $check_digit);
	}
}

/**
 * This function take a 9-digit code (output of cut_isbn() function)
 * and return a valid 10-digit ISBN.
 *
 * @param	string
 * @return string
 * @access public 
 */
function uncut_isbn_10( $code )
{
	$check_digit = 11 - (($code[0] * 10) + ($code[1] * 9) + ($code[2] * 8) +
				($code[3] * 7) + ($code[4] * 6) + ($code[5] * 5) +
					($code[6] * 4) + ($code[7] * 3) + ($code[8] * 2)) % 11;
	switch ($check_digit)
	{
		case 11: return $code . '0';
		case 10: return $code . 'X';
		default: return $code . $check_digit;
	}
}

/* End of file isbn_helper.php */
/* Location: ./application/helpers/isbn_helper.php */ 
