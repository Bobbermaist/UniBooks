<?php

namespace utf8;

/*
 * http://eqcode.com/wiki/Utf8_\ord
 *
 */

/*
function ord($ch) {
  $len = strlen($ch);
  if($len <= 0) return false;
  $h = \ord($ch{0});
  if ($h <= 0x7F) return $h;
  if ($h < 0xC2) return false;
  if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (\ord($ch{1}) & 0x3F);
  if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (\ord($ch{1}) & 0x3F) << 6 | (\ord($ch{2}) & 0x3F);          
  if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (\ord($ch{1}) & 0x3F) << 12 | (\ord($ch{2}) & 0x3F) << 6 | (\ord($ch{3}) & 0x3F);
  return false;
}
*/

function ord($c){
    $ud = 0;
    if (\ord($c{0})>=0 && \ord($c{0})<=127)   $ud = $c{0};
    if (\ord($c{0})>=192 && \ord($c{0})<=223) $ud = (\ord($c{0})-192)*64 + (\ord($c{1})-128);
    if (\ord($c{0})>=224 && \ord($c{0})<=239) $ud = (\ord($c{0})-224)*4096 + (\ord($c{1})-128)*64 + (\ord($c{2})-128);
    if (\ord($c{0})>=240 && \ord($c{0})<=247) $ud = (\ord($c{0})-240)*262144 + (\ord($c{1})-128)*4096 + (\ord($c{2})-128)*64 + (\ord($c{3})-128);
    if (\ord($c{0})>=248 && \ord($c{0})<=251) $ud = (\ord($c{0})-248)*16777216 + (\ord($c{1})-128)*262144 + (\ord($c{2})-128)*4096 + (\ord($c{3})-128)*64 + (\ord($c{4})-128);
    if (\ord($c{0})>=252 && \ord($c{0})<=253) $ud = (\ord($c{0})-252)*1073741824 + (\ord($c{1})-128)*16777216 + (\ord($c{2})-128)*262144 + (\ord($c{3})-128)*4096 + (\ord($c{4})-128)*64 + (\ord($c{5})-128);
    if (\ord($c{0})>=254 && \ord($c{0})<=255) $ud = false; //error
    return $ud;
}
