<?php

require_once("utilities.php");

//require_once("blowfish.class.php");

function signUp($user, $pass, $email) {		/* Procedura di inserimento di un nuovo utente
											 * TODO rivedere il sistema di criptazione della password */
	$mysqli = connect();
		/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	if( !checkString($user) || !checkString($pass) || !checkMail($email) )
		return false;
	$reg = date("Y-m-d H:i:s");
//	$blowfish = new Blowfish($reg);
	$activation_key = substr(md5(rand()),0,15);
	//$insert = "INSERT INTO `users` (user_login, user_pass, user_email, user_activation_key, user_registered) 
	//			VALUES ('$user', '" . sha1($blowfish->Encrypt($pass)) . "', 
	//				'$email', '$activation_key', '$reg')";
	$insert = "INSERT INTO `users` (user_login, user_pass, user_email, user_activation_key, user_registered)
				VALUES ('$user', '" . sha1($pass) . "',
					'$email', '$activation_key', '$reg')";
	$res = $mysqli->query($insert);
	$mysqli->close();
	sendActivation($user, $activation_key, $email);
	return $res;
}

function sendActivation($userName, $activation_key, $email) {	/* Manda un'email di attivazione dell'account
																 * l'utente registrato potra' effettuare il log in
																 * solo dopo aver confermato la sua email */
	$link = "http://" . $_SERVER['SERVER_NAME'] . "/user/registration.php?user=$userName&activation_key=$activation_key";
	$msg ="
		<html>
			<head>
				<title> Registrazione Account </title>
			</head>
			<body>
				<p>Ciao, <i>$userName</i> la registrazione è stata effettuata con successo,
				per attivare il tuo account clicca sul seguente link,
				o incollalo nella barra degli indirizzi. <br>
				<a href='$link'><b>$link</b></a> </p>
			</body>
		</html>";
	$msg = ordtochr($msg);
	$headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
			/* $headers .= "To: $user <$email>\r\n"; */
			$headers .= "From: BOOKS <account@ns161.altervista.org>\r\n";
			mail($email, "Attivazione account", $msg, $headers);
}

function checkActivation($user, $activation_key) {	/* Se la chiave di attivazione corrisponde
													 * a quella salvata nel db, attiva l'account 
													 * e ritorna 1
													 * Ritorna 2 se l'utente risulta gia' attivato
													 * 0 in ogni altro caso */
	$IDreturn = 0;

	if( !checkString($user) || !checkString($activation_key) )
		return $IDreturn;
	$mysqli = connect();
		/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$res = $mysqli->query("SELECT `ID`, `user_activation_key`, `user_rights` FROM `users` WHERE user_login='$user' LIMIT 1");
	$fetch = $res->fetch_assoc();
	if( $fetch["user_rights"] > -1)		//utente gia' registrato
		$IDreturn = 2;
	elseif( strcmp($activation_key, $fetch["user_activation_key"]) === 0 ) {
		$mysqli->query("UPDATE `users` SET user_rights=0, user_activation_key='' WHERE user_login='$user' LIMIT 1");
		$IDreturn = 1;
	}
	$res->close();
	$mysqli->close();
	return $IDreturn;
}

function existingUser($user) {	/* Controlla se un nome utente e' gia' presente nel db */
	$existingUser = false;
	if( !checkString($user) )
		return false;
	$mysqli = connect();
		/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	if( $mysqli->query("SELECT * FROM `users` WHERE LOWER(user_login)='".strtolower(trim($user))."' limit 1")->num_rows > 0 )
		$existingUser = true;
	$mysqli->close();
	return $existingUser;
}

function existingMail($email) {	/* Controlla se un email e' gia' presente nel db */
	$existingMail = false;
	if( !checkMail($email) )
		return false;
	$mysqli = connect();
		/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	if( $mysqli->query("SELECT * FROM `users` WHERE LOWER(user_email)='".strtolower(trim($email))."' limit 1")->num_rows > 0 )
		$existingMail = true;
	$mysqli->close();
	return $existingMail;
}

?>