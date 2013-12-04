
<?php echo validation_errors(); ?>

<?php echo form_open('user/registration'); ?>
	  <h1>Iscrizione</h1>
	  <p>Nome utente:
	  	<input type="text" name="user_name" maxlength="20" required autofocus>
	  </p>
	  <p>Email: 
	  	<input type="email" name="email" maxlength="64" required>
	  </p>
	  <p>Password:
	  	<input type="password" name="pass" maxlength="64" required>
	  </p>
	  <p>Conferma password:
	  	<input type="password" name="passconf" maxlength="64" required>
	  </p>
	  <!-- <p>Accetto i termini e le condizioni d'uso di <b> 
	  	<?php //echo $_SERVER['SERVER_NAME']; ?> </b> <input type='checkbox' name='terms'> </p> -->
<?php
		echo form_submit('registration', 'Iscriviti!');
echo form_close(); 
?>

