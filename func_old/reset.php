<?php

require_once("utilities.php");

//require_once("blowfish.class.php");

function makeNewPassword($user_or_email) {	/* Valuta il parametro $user_or_email, se contiene un 
											 * indirizzo email valido, controlla se e' presente nel db,
											 * genera una password temporanea e chiede conferma via email
											 * Altrimenti controlla se il parametro contiene un username
											 * presente nel db */
	if( checkMail($user_or_email) ) {
		$mysqli = connect();
			/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$select = "SELECT * FROM `users` WHERE LOWER(user_email)='" . strtolower(trim($user_or_email)) . "' LIMIT 1";
		$res = $mysqli->query($select);
		if( $res->num_rows > 0 ) {
			$fetch = $res->fetch_assoc();
			$user = $fetch["user_login"];
			$activation_key = substr(md5(rand()),0,15);
			$pass = substr(md5(rand()),0,6);			// genero una nuova password di 6 caratteri
			sendNewPassword($user, trim($user_or_email), $activation_key, $pass);
			//$blowfish = new Blowfish($fetch["user_registered"]);
			//$update = "UPDATE `users` SET tmp_pass='" . sha1($blowfish->Encrypt($pass)) . "', 
			//			user_activation_key='$activation_key' WHERE user_login='$user'";
			$update = "UPDATE `users` SET tmp_pass='" . sha1($pass) . "',
			user_activation_key='$activation_key' WHERE user_login='$user'";
			$mysqli->query($update);
			$res->close();
			$mysqli->close();
			return true;
		}
	}
	elseif( checkString($user_or_email) ) {
		$mysqli = connect();
			/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$user = strtolower(trim($user_or_email));
		$res = $mysqli->query("SELECT * FROM `users` WHERE LOWER(user_login)='$user' LIMIT 1");
		if( $res->num_rows > 0 ) {
			$fetch = $res->fetch_assoc();
			$email = $fetch["user_email"];
			$activation_key = substr(md5(rand()),0,15);
			$pass = substr(md5(rand()),0,6);
			sendNewPassword($user, $email, $activation_key, $pass);
			//$blowfish = new Blowfish($fetch["user_registered"]);
			//$update = "UPDATE `users` SET tmp_pass='" . sha1($blowfish->Encrypt($pass)) . "', 
			//			user_activation_key='$activation_key' WHERE LOWER(user_login)='$user' LIMIT 1";
			$update = "UPDATE `users` SET tmp_pass='" . sha1($pass) . "',
						user_activation_key='$activation_key' WHERE LOWER(user_login)='$user' LIMIT 1";
			$mysqli->query($update);
			$res->close();
			$mysqli->close();
			return true;
		}
	}
	return false;
}

function sendNewPassword($user, $email, $activation_key, $password) {	/* Invia un'email ad un utente con la sua
																		 * nuova password e chiede conferma per il reset */
	$link = "http://" . $_SERVER['SERVER_NAME'] . "/user/reset.php?user=$user&activation_key=$activation_key";
	$msg = "
		<html>
			<head>
				<title>Reset Password</title>
			</head>
			<body>
				<p>È stata inoltrata la richiesta di reset password per questo account, se vuoi confermare
					l'operazione clicca sul seguente link o incollalo nella barra degli indirizzi<br> \n
					<a href='$link'><b>$link</b></a> </p> \n
				<p>Il tuo nome utente è: <b>$user</b> </p> \n
				<p>La tua nuova password è: <b>$password</b></p> \n
				<p>Ignora questa email se non hai effettuato tu questa richiesta</p> \n
			</body>
		</html> \n";
	$msg = ordtochr($msg);
	$headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
			/* $headers .= "To: $user <$email>\r\n"; */
	$headers .= "From: BOOKS <account@ns161.altervista.org>\r\n";
		mail($email, "Reset Password", $msg, $headers);
}

function resetPassword($user, $activation_key) {	/* Se l'utente ha confermato il reset password tramite email
													 * aggiorna la sua password nel database */
	$IDreturn = 0;
	if ( !checkString($user) || !checkString($activation_key) )
		return $IDreturn;
	$mysqli = connect();
		/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$res = $mysqli->query("SELECT * FROM `users` WHERE user_login='$user' LIMIT 1");
	$fetch = $res->fetch_assoc();
	if( strcmp($fetch["tmp_pass"], "") === 0 )
		$IDreturn = 2;
	elseif( strcmp($activation_key, $fetch["user_activation_key"]) === 0 ) {
		$pass = $fetch["tmp_pass"];
		$mysqli->query("UPDATE `users` SET user_pass='$pass', tmp_pass='', user_activation_key='' WHERE user_login='$user' LIMIT 1");
		$IDreturn = 1;
	}
	$res->close();
	$mysqli->close();
	return $IDreturn;
}

?>