      <form id='logout' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>' >
         <input type='submit' name='logout' value='Effettua il logout' onClick="return confirm('Effettuare il logout?')">
      </form>
<?php 
require_once("../func/log.php");
		/* Questo modulo inserisce sia il bottone di logout nella pagina
		 * che la relativa funzione di log out */
if( isset($_POST["logout"]) ) {
	logout();
	header("Location: /index.php");
	exit;
}

?>