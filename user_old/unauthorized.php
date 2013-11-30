<?php
		/* Reindirizzo qui se l'utente ha cercato di accedere ad un'area
		 * "amministratori" */
include("../module/restrict");

include("../module/head.html");

include("../module/body.html");

require_once("../func/user.php");

$user = new User;
$user->readSession();

?>
	<p>L'accesso a quest'area &egrave; limitato agli amministratori.</p>
	<p>Non hai l'autorizzazione per visualizzare questa pagina,
	    <b><?php echo $user->getUserName(); ?></b>.</p>
<?php
include("../module/logout.php");

include("../module/coda.html"); 

?>