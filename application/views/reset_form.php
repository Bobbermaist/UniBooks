
<?php echo validation_errors(); ?>

<?php echo form_open('user/reset'); ?>
	  <h1>Iscrizione</h1>
	  <p>Inserisci il tuo nome utente o la tua email:
	  	<input type="text" name="user_or_email" maxlength="64" required autofocus>
	  </p>
<?php
		echo form_submit('reset', 'Reset password');
echo form_close(); 
?>

 