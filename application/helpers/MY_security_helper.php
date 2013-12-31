<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PHPASS_PATH . 'PasswordHash.php';

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
	//return preg_replace('/%/', '.', urlencode($str));
	return str_replace('%', '.', urlencode($str));
}

function url_decode_utf8($str)
{
	//return urldecode(preg_replace('/\./', '%', $str));
	return urldecode(str_replace('.', '%', $str));
}

/* End of file MY_security_helper.php */
/* Location: ./application/helpers/MY_security_helper.php */