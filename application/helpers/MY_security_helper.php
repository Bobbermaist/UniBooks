<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'third_party/phpass/PasswordHash.php';

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

function get_random_bytes($bytes)
{
	return file_get_contents("http://www.random.org/cgi-bin/randbyte?nbytes=$bytes");
}

function get_random_string($length)
{
	return utf8_encode(get_random_bytes($length));
}

function url_encode_utf8($str)
{
	return preg_replace('/%/', '.', urlencode($str));
}

function url_decode_utf8($str)
{
	return urldecode(preg_replace('/\./', '%', $str));
}

/* End of file MY_security_helper.php */
/* Location: ./application/helpers/MY_security_helper.php */