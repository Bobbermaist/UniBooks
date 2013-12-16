
<?php echo validation_errors();
	echo form_open('user/registration');
?>
	  <h1>Iscrizione</h1>
	  <p>Nome utente: <?= form_input($user_name_data) ?> </p>
	  <p>Email: <?= form_input($email_data) ?> </p>
	  <p>Password: <?= form_password($pass_data) ?> </p>
	  <p>Conferma password: <?= form_password($passconf_data) ?> </p>
	  <!-- <p>Accetto i termini e le condizioni d'uso di <b> 
	  	<?php //echo $_SERVER['SERVER_NAME']; ?> </b> <input type='checkbox' name='terms'> </p> -->
<?php
		echo form_submit('registration', 'Iscriviti!');
	echo form_close(); 
?>

