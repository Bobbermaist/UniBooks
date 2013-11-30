<?php

require_once("../func/log.php");
		/* Modulo da includere nella pagine dedicate agli amministratori */
session_start();
if( !isset($_SESSION["sid"]) ) {
	$_SESSION["url"] = $_SERVER["PHP_SELF"];
	header("Location: /index.php");
	exit;
} elseif( !authenticateSuperUser() ) {
	header("Location: /user/unauthorized.php");
	exit;
}

?>