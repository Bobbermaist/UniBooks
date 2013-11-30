<?php 
		/* Questa pagina caricata senza parametri mostra il form di registrazione,
		 * se sono settati $_GET["activation_key"] e $_GET["user"] cerca di attivare
		 * l'account */
include("../module/head.html");

include("../module/body.html");

require_once("../func/utilities.php");

require_once("../func/reg.php");

$registered = false;

if( isset($_SESSION["sid"]) )
	$msg = "Sei gi&agrave; registrato</b>!";
elseif( isset($_GET["activation_key"]) && isset($_GET["user"]) ) {
	switch ( checkActivation($_GET["user"], $_GET["activation_key"]) ) {
		case 1:
			$msg =  "<b>Attivazione effettuata con successo!</b> <br> Ora puoi effettuare il <a href='/index.php'>login</a>";
			break;
		case 2:
			$msg =  "<b>Account gi&agrave; attivato</b> <br> Ora puoi effettuare il <a href='/index.php'>login</a>";
			break;
		default:
			$msg =  "<b>Errore</b>";
	}
		
}
else
    $registered = true;

if(!$registered)
  echo "<p>" . $msg . "</p>";
?>

      <div <?php if(!$registered) echo "id='hide'"; ?> >
	<form name='registration' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>' >
	  <h1>Iscrizione</h1>
	  <p>Nome utente: <input type='text' name='user' maxlength='60' required autofocus> </p>
	  <p>Email: <input type='text' name='email' maxlength='64' required> </p>
	  <p>Password: <input type='password' name='pass' maxlength='64' required> </p>
	  <p>Accetto i termini e le condizioni d'uso di <b> <?php echo $_SERVER['SERVER_NAME']; ?> </b> <input type='checkbox' name='terms'> </p>
	  <input type='submit' name='confirm' value='Iscriviti' >
	</form>
      </div>

<?php
		/* Concateno alla variabile $msg tutti gli eventuali messaggi di errore */
if( isset($_POST["confirm"]) && isset($_POST["user"]) && isset($_POST["email"]) && isset($_POST["pass"]) ) {

  $error = false;
  $msg = "";
  if( strlen($_POST["user"]) < 3 ) {
    $msg .= "<li>Username troppo corto</li>";
    $error = true;
  }
  if( strlen($_POST["pass"]) < 6 ) {
    $msg .= "<li>Password troppo corta</li>";
    $error = true;
  }
  if( !checkMail($_POST["email"]) ) {
    $msg .= "<li>L'email inserita <b>non &egrave;</b> valida</li>";
    $error = true;
  }
  if( existingUser($_POST["user"]) ) {
    $msg .= "<li>L'utente inserito &egrave; gi&agrave; presente nel database</li>";
    $error = true;
  }
  if( existingMail($_POST["email"]) ) {
    $msg .= "<li>L'email inserita &egrave; gi&agrave; presente nel database</li>";
    $error = true;
  }
  if( !checkString($_POST["user"]) ) {
    $msg .= "<li>Il nome utente contiene caratteri non consentiti</li>";
    $error = true;
  }
  if( !checkString($_POST["pass"]) ) {
    $msg .= "<li>La password contiene caratteri non consentiti</li>";
    $error = true;
  }
  if( !isset($_POST["terms"]) ) {
    $msg .= "<li>Per proseguire nella registrazione &egrave; necessario accettare i <b>termini e le condizioni d'uso</b></li>";
    $error = true;
  }

  if(!$error) {
    if( signUp($_POST["user"], $_POST["pass"], $_POST["email"]) ) {
      $msg = "<li><b>Utente inserito con successo</b>, controlla la tua email per l'attivazione dell'account</li>";
    }
    else
      $msg = "<li>Errore nell'inserimento utente</li>";
  }
		/* Stampo una lista con tutti i messaggi di errore */
  echo "<ul>" . $msg . "</ul>";
}
?>

		<p><a href='/index.php'>torna indietro</a></p>

<?php include("../html/coda.html"); ?>