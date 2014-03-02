<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function validate10( $str )
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

function validate13( $str )
{
	if (strlen($str) != 13)
		return FALSE;

	$check = 0;
	for($i = 0; $i < 13; $i+=2) $check += substr($str, $i, 1);
	for($i = 1; $i < 12; $i+=2) $check += 3 * substr($str, $i, 1);
	return (boolean) ($check % 10 == 0);
}

function cutISBN( $isbn )
{
	return (strlen($isbn) == 13) ? substr($isbn, 3, -1) : substr($isbn, 0, -1);
}

function uncutISBN( $code )
{
	$isbn = '978' . $code;
	$check = 0;
	for($i = 0; $i < 13; $i+=2) $check += substr($isbn, $i, 1);
	for($i = 1; $i < 12; $i+=2) $check += 3 * substr($isbn, $i, 1);
	if ($check % 10 == 0)
		return $isbn . 0;
	return $isbn . (10 - $check % 10);
}

/* End of file isbn_helper.php */
/* Location: ./application/helpers/isbn_helper.php */ 
