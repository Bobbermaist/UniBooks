<?php
include("../module/restrict.php"); // <- area riservata

include("../module/head.html");

include("../module/body.html");

require_once("../func/user.php");

$user = new User;
$user->readSession();
echo "<h1>Benvenuto ". $user->getUserName() . "</h1>";
echo "L'id sessione e': " . $_SESSION["sid"] . "<br>";
echo "L'id utente e': " . $user->getUserId() . "<br>";

include("../module/logout.php");

include("../module/coda.html");

?>