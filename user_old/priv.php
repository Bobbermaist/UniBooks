<?php
include("../module/restrict.php");	/* Serve solo per testsss */

include("../module/head.html");

include("../module/body.html");

require_once("../func/user.php");

$user = new User;
$user->readSession();
?>
	<p>Area privata riservata a <b><?php echo $user->getUserName(); ?></b></p>
<?php 
include("../module/logout.php");

include("../module/coda.html");
?>