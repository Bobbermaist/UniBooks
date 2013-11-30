<?php

require_once("../func/log.php");
		/* Modulo da includere nelle pagine riservate agli utenti loggati */
session_start();
if( !isset($_SESSION["sid"]) || !authenticate() ) {
	$_SESSION["url"] = $_SERVER["PHP_SELF"];
	header("Location: /index.php");
	exit;
}

?>