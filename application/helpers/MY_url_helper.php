<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once UTF8_PATH . 'portable-utf8.php';

function url_encode($str)
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
			$out .= '.' . base_convert(utf8_ord($char), 10, 36);
	}
	return $out;
}

function url_decode($str)
{
	$chr_func = create_function('$matches',
		'return utf8_chr((int) base_convert(substr($matches[0], 1), 36, 10));'
	);
	return preg_replace_callback(
		'/\.[0-9]+/',
		$chr_func,
		$str
	);
}

/* End of file MY_url_helper.php */
/* Location: ./application/helpers/MY_url_helper.php */