<?php

require_once("utilities.php");

//require_once("blowfish.class.php");

class User {	/* Classe utente, gestisce un utente tramite il suo ID,
				 * i suoi permessi ed il nome utente */
	
	private $id, $rights, $userName;
	
	function __destruct() {
		unset($this->id);
		unset($this->rights);
		unset($this->userName);
	}

	public function getUserName() {
		return $this->userName;
	}
	
	public function  getUserRights() {
		return $this->rights;
	}
	
	public function getUserId() {
		return $this->id;
	}
	
	public function readSession() {		/* Legge i parametri di sessione e se le variabili 
										 * di stato non sono gia' impostate, scarica i dati dal db
										 * e le setta */
		session_start();
		if( !isset($_SESSION["sid"]) || !ereg("([a-f]|\d)", $_SESSION["sid"]) )
			exit;
		$session_id = $_SESSION["sid"];
		$mysqli = connect();
			/* check connection */
		if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
		}
		if( !isset($this->id) ) {
			$res = $mysqli->query("SELECT `user_id` FROM `users_tmp` WHERE user_session_id='$session_id' LIMIT 1");
			$fetch = $res->fetch_assoc();
			$this->id = intval($fetch["user_id"]);
			$res->close();
		}
		if( !isset($this->rights) || !isset($this->userName) ) {
			$res = $mysqli->query("SELECT * FROM `users` WHERE ID='$this->id' LIMIT 1");
			$fetch = $res->fetch_assoc();
			$this->rights = intval($fetch["user_rights"]);
			$this->userName = $fetch["user_login"];
			$res->close();
		}
		$mysqli->close();
	}
}

?>