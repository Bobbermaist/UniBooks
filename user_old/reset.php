<?php 
include("../module/head.html");
   
include("../module/body.html");

require_once("../func/reset.php");

$showInput = true;
		/* La pagina caricata senza parametri mostra il form di reset,
		 * se sono settati $_GET["user"] e $_GET["activation_key"] cerca di
		 * resettare la password */
if( isset($_GET["activation_key"]) && isset($_GET["user"]) ) {
	$showInput = false;
	switch ( resetPassword($_GET["user"], $_GET["activation_key"]) ) {
		case 1: 
			$msg = "<b>La password &egrave; stata aggiornata con successo!</b> <br> Ora puoi effettuare il <a href='/index.php'>login</a>";
			break;
		case 2:
			$msg = "Non c'&egrave; nessuna richiesta di reset password per questo account";
			break;
		default:
			$msg = "Errore nel reset";
	}
	echo "<p>" . $msg . "</p>";
}

?>
	<div <?php if(!$showInput) echo "id='hide'"; ?> >
	  <form name='reset' method='post' action=' <?php echo $_SERVER['PHP_SELF']; ?> ' >
	    <h1>Reset password</h1>
	    <p>Indica il tuo nome utente o la tua email: <input type='text' name='user_or_email' maxlength='64' required autofocus> </p>
	    <input type='submit' name='confirm' value='Conferma' >
	  </form>
	</div>

<?php
if( isset($_POST["confirm"]) && isset($_POST["user_or_email"]) ) {
	if( makeNewPassword($_POST["user_or_email"]) )
		$msg = "Controlla l'email per effettuare il reset della password";
	else
		$msg = "Errore nella richiesta";
	echo "<p>" . $msg . "</p>";
}

include("../html/coda.html"); 

?>
