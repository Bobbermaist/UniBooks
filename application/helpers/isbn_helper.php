<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function validate( $str )
{
	if (validate_isbn_13($str) === TRUE)
	{
		return 13;
	}
	if (validate_isbn_10($str) === TRUE)
	{
		return 10;
	}
	return FALSE;
}

function validate_isbn_13( $str )
{
	if (strlen($str) != 13)
		return FALSE;

	$check = 0;
	for($i = 0; $i < 13; $i+=2) $check += substr($str, $i, 1);
	for($i = 1; $i < 12; $i+=2) $check += 3 * substr($str, $i, 1);
	return (boolean) ($check % 10 == 0);
}

function validate_isbn_10( $str )
{
	if (strlen($str) != 10)
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

function cut_isbn( $isbn )
{
	switch (strlen($isbn))
	{
		case 13: return substr($isbn, 3, -1);
		case 10: return substr($isbn, 0, -1);
		default: return $isbn;
	}
}

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
