
<?php
if( ! isset($p) ) return;

if( is_array($p) )
{
	foreach($p as $par)
		echo "\t<p>$par</p>\n";
}
else
	echo "\t<p>$p</p>\n";
?> 
