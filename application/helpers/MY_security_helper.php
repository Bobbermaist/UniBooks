<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PHPASS_PATH . 'PasswordHash.php';
require_once UTF8_FUNC_PATH . 'portable-utf8.php';

function do_hash($str)
{
	$hasher = new PasswordHash(8, FALSE);
	return $hasher->HashPassword($str);
}

function check_hash($hashedPassword, $str)
{
	$hasher = new PasswordHash(8, FALSE);
	return $hasher->CheckPassword($str, $hashedPassword);
}

function get_random_char()
{
	return chr(rand(0, 255));
}

function get_random_string($length)
{
	$str = '';
	for($c = 0; $c < $length; $c++)
		$str .= get_random_char();

	return utf8_encode($str);
}

function url_encode_utf8($str)
{
	$out = '';
	for ($i = 0, $len = utf8_strlen($str); $i < $len; $i++)
	{
		$char = utf8_access($str, $i);
		if (($char >= '0' AND $char <= '9')
				OR ($char >= 'A' AND $char <= 'Z')
				OR ($char >= 'a' AND $char <= 'z'))
			$out .= $char;
		else
			$out .= '.' . utf8_ord($char);
	}
	return $out;
	/*
	return mb_ereg_replace_callback(
	//return preg_replace_callback(
		'[^0-9A-Za-z]',
		function ($matches)
		{
			print_r($matches);
			return '.' . utf8_ord($matches[0]);
		},
		$str
	);
	*/
}

function url_decode_utf8($str)
{
	return preg_replace_callback(
		'/\.[0-9]+/',
		function ($matches)
		{
			return utf8_chr((int) substr($matches[0], 1));
		},
		$str
	);
}

/* End of file MY_security_helper.php */
/* Location: ./application/helpers/MY_security_helper.php */