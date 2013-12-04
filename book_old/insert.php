<?php 
include("../module/super_user.php");	/* area dedicata agli amministratori
										 * SOLO PE TESTS!!! */

include("../module/head.html");

include("../module/body.html");

require_once("../func/book.php");
?>
	<form name='insert' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>' >
	  <h1>Inserisci un nuovo libro</h1>
	  <p>ISBN: <input type='text' name='ISBN' maxlength='60' required autofocus> </p>

	  <input type='submit' name='insert' value='Inserisci' >
	</form>
<?php
if( !isset($_POST["ISBN"]) )
	exit;
$book = new Book($_POST["ISBN"]);
if( !$book->validate() ) {	/* Se l'oggetto $book e' stato inizializzato con un ISBN non valido
							 * non serve eseguire nessun fetch ; ) */
	echo "<p>Hai sbagliato a scrivere il codice ISBN</p>";
}
else {
	$book->goFetch();
}

include("../module/logout.php");

include("../module/coda.html"); 

?>