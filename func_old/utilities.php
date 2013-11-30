<?php
include("../config/db.php");

function connect() {	/* Connessione a mysqli, restituisce l'oggetto mysqli in caso
						 * di esito positivo, false altrimenti */
	if( $mysqli = new mysqli(HOST, USER, PWD, DB) )
		return $mysqli;
	else
		return false;
}

function checkString($str) {	/* Controlla una stringa, restituisce true se nella stringa
								 * NON sono presenti i caratteri nell'array */
	$search = array("'", '"', "\\", "/", "<", ">", "%", "$", "#", "?", "&", "@");
	$cfr = str_replace($search, "", $str);
	if( strcmp($str, $cfr) === 0 )
		return true;
	else
		return false;
}

/*function checkMail($email) {
	$email = trim($email);
	$atIndex = strpos($email, "@");
	$dotIndex = strrpos($email,".");
		/* Controlla: se c'?una '@', se c'?un '.', se prima della '@' ci sono almeno 4 caratteri,
		 * se tra la '@' e l'ultimo '.' ci sono almeno 3 caratteri,
		 * se dopo l'ultimo '.' ci sono almeno due caratteri,
		 * se nella stringa ci sono ';', ',', ' ' e altri pattern non comuni negli indirizzi email
	if( !$atIndex || !$dotIndex || $atIndex < 4 || $dotIndex < $atIndex+4 || $dotIndex+2 >= strlen($email) || strpos($email,";") ||
			strpos($email,",") || strpos($email," ") || !preg_match( "/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/", $email))
		return false;
	else
		return true;
}*/

function checkMail($email) {	/* Verifica la validita' di un indirizzo email */
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function ordtochr($string) {	/* Converte una stringa in codici ASCII HTML
								 * Le email inviate in questo modo non finiscono
								 * in spam ;) */
	$nstring = "";

	for($i=0; $i < strlen($string); $i++) {
			/* copia il contenuto dei tag */
		if( $string[$i] == "<" ) {
			while($string[$i] != ">") {
				$nstring .= $string[$i];
				$i++;
			}
			$nstring .= $string[$i];
		}
		elseif( $string[$i] == " " || $string[$i] == "\r" || $string[$i] == "\n" )
			$nstring .= $string[$i];
		else
			$nstring .= "&#".ord($string[$i]).";";
	}
	return $nstring;
}

?>
