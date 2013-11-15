<?php 
include("module/head.html");
   
include("module/body.html");
?>
	<form name='login' method='post' action='<?php echo $_SERVER["PHP_SELF"] ?>' >
	  <h1>Login</h1>
	  <p>Nickname: <input type='text' name='user' maxlength='60' required autofocus> </p>
	  <p>Password: <input type='password' name='pass' maxlength='64' required></p>
	  <input type='submit' name='login' value='Login'>
	</form>
<?php
require_once("func/log.php");

redirectIn();		//esegue un redirect se l'utente risulta loggato

if( isset($_POST["login"]) && isset($_POST["user"]) && isset($_POST["pass"]) ) {
	if( login($_POST["user"], $_POST["pass"]) )		//login
		headerIn();
	else
		echo "<p><strong>Errore nel login</strong></p>";
}

session_start();	/* in $_SESSION["url"] viene salvato l'indirizzo
					 * al quale l'utente ha cercato di accedere senza login
					 * Dopo il log in verra' rindirizzato qui */
if( isset($_SESSION["url"]) )
	$redirect = $_SESSION["url"];
?>
	<p><a href="/user/registration.php"> Registrati</a></p>
	<p>Problemi con la password? <a href='/user/reset.php'>Recupera</a> </p>
	<ul>
	  <li>Non sar&agrave; possibile accedere alle aree riservate 
	    <a href='/user/index.php'>/user/index.php</a> e <a href='/user/priv.php'>/user/priv.php</a>
	    prima di aver effettuato il log in.
	  </li>
	  <li>Non &egrave; possibile accedere alle aree riservate ai super utenti senza privilegi utente.
	    <a href='/book/insert.php'>/book/insert.php</a>
	  </li>
	  <li <?php if(!isset($redirect)) echo "id='hide'"; ?>>Hai tentato di accedere a 
	    <b><?php echo $redirect; ?></b>,
	    verrai reindirizzato a questa pagina automaticamente dopo il log in.
	  </li>
	</ul>
	
	<hr>

<?php include("module/coda.html"); ?>