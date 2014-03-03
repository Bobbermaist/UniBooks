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
	$isbn = '978' . $code;
	$check = 0;
	for($i = 0; $i < 13; $i+=2) $check += substr($isbn, $i, 1);
	for($i = 1; $i < 12; $i+=2) $check += 3 * substr($isbn, $i, 1);
	if ($check % 10 == 0)
		return $isbn . 0;
	return $isbn . (10 - $check % 10);
}

function uncut_isbn_10( $code )
{
	for($i = 0, $weight = 10, $sum = 0; $i < 9; $i++, $weight--)
	{
		$sum += $code[$i] * $weight;
	}
	$check_digit = 11 - ($sum % 11);
	switch ($check_digit)
	{
		case 11: return $code . '0';
		case 10: return $code . 'X';
		default: return $code . $check_digit;
	}
}

/* End of file isbn_helper.php */
/* Location: ./application/helpers/isbn_helper.php */ 
