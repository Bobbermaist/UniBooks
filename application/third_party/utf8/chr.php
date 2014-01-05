<?php

namespace utf8;

/*
 * http://eqcode.com/wiki/Utf8_chr
 *
 */

function chr($num) {
	if($num<128) return \chr($num);
	if($num<2048) return \chr(($num>>6)+192).\chr(($num&63)+128);
	if($num<65536) return \chr(($num>>12)+224).\chr((($num>>6)&63)+128).\chr(($num&63)+128);
	if($num<2097152) return \chr(($num>>18)+240).\chr((($num>>12)&63)+128).\chr((($num>>6)&63)+128).\chr(($num&63)+128);
	return false;
}
