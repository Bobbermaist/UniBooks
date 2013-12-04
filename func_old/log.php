<?php

require_once("utilities.php");

//require_once("blowfish.class.php");

function login($user, $pass) {	/* Esegue il log in
								 * Controlla se la password immessa corrisponde a quella salvata
								 * e inserisce in un db temporaneo i dati di sessione dell'utente */
	$login = false;
	$user = trim($user);
	if( !checkString($user) || !checkString($pass) ) 
		return false;
	$mysqli = connect();
		/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$select = "SELECT * FROM `users` WHERE LOWER(user_login)='" . strtolower($user) . "' LIMIT 1";
	$res = $mysqli->query($select);
	if( $res->num_rows > 0 ) {
		$fetch = $res->fetch_object();
		//$blowfish = new Blowfish($fetch->user_registered);
		//$pass = sha1($blowfish->Encrypt($pass));
		$pass = sha1($pass);
		if( strcmp($pass, $fetch->user_pass) === 0 && $fetch->user_rights > -1 ) {
			echo "strcmp(\$pass, \$fetch->user_pass) === 0 && \$fetch->user_rights > -1<br>";
			$session_id = sha1(rand());
			$login_time = date("Y-m-d H:i:s");
			$user_ip = $_SERVER["REMOTE_ADDR"];
			$log_query= "INSERT INTO `users_tmp` (user_id, user_session_id, user_login, user_ip)
					VALUES ('$fetch->ID', '$session_id', '$login_time', '$user_ip')
					ON DUPLICATE KEY UPDATE user_session_id='$session_id',
					user_login='$login_time', user_ip='$user_ip'";
			$mysqli->query($log_query);
			session_start();
			$_SESSION["sid"] = $session_id;
			$login = true;
		}
		$mysqli->close();
	}
	return $login;
}

function authenticate() {	/* Autentica un utente
							 * Controlla se i dati salvati in $_SESSION corrispondono con quelli
							 * del db temporaneo
							 * Restituisce l'id utente in caso positivo, 0 altrimenti */
	session_start();
	$id = 0;
	if( !isset($_SESSION["sid"]) || !ereg("([a-f]|\d)", $_SESSION["sid"]) )
		return $id;
	$mysqli = connect();
		/* check connection */
	if(mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$sid = $_SESSION["sid"];
	$res = $mysqli->query("SELECT `user_id`, `user_ip` FROM `users_tmp` WHERE user_session_id='$sid' LIMIT 1");
	if( $res->num_rows > 0 ) {
		$fetch = $res->fetch_object();
		if( $fetch->user_ip == $_SERVER["REMOTE_ADDR"] )
			$id = $fetch->user_id;
	}
	$res->close();
	$mysqli->close();
	return $id;
}

function authenticateSuperUser() {	/* Funzione di autenticazione per gli amministratori
									 * oltre alla normale autenticazione controlla che l'utente 
									 * abbia i permessi di amministratore */
	$id = 0;
	if( $userId = authenticate() ) {
		$mysqli = connect();
			/* check connection */
		if(mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$res = $mysqli->query("SELECT `user_rights` FROM `users` WHERE ID='$userId'");
		$fetch = $res->fetch_object();
		if( $fetch->user_rights > 0 )
			$id = $userId;
		$res->close();
		$mysqli->close();
	}
	return $id;
}

function headerIn() {	/* Esegue un redirect verso l'area riservata senza autenticare i dati di sessione */
	session_start();
	$url = isset($_SESSION["url"]) ? $_SESSION["url"] : "/user/index.php";
	unset($_SESSION["url"]);
	header("Location: $url");
	exit;
}

function redirectIn() {	/* Esegue prima l'autenticazione e poi il redirect */
	if( authenticate() )
		headerIn();
}

function redirectOut() {	/* Esegue un redirect fuori dall'area riservata
							 * se l'autenticazione non e' andata a buon fine 
							 * Salva in $_SESSION["url"] la pagina a cui stava cercando di accedere l'utente */
	if( !authenticate() ) {
		$_SESSION["url"] = $_SERVER["PHP_SELF"];
		header("Location: /index.php");
		exit;
	}
}

function logout() {		/* Elimina i dati di sessione e svuota il db temporaneo */
	$mysqli = connect();
	if ( !isset($_SESSION["sid"]) || !ereg("([a-f]|\d)", $_SESSION["sid"]) || mysqli_connect_errno() )
		exit();
	$sid = $_SESSION["sid"];
	$mysqli->query("DELETE FROM `users_tmp` WHERE user_session_id='$sid' LIMIT 1");
	$mysqli->close();
	session_unset();
	session_destroy();
}

?>