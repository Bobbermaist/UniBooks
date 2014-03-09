
<?php echo validation_errors();
	echo form_open('reset');
?>
	  <h1>Iscrizione</h1>
	  <p>Inserisci il tuo nome utente o la tua email: <?php echo form_input($user_or_email); ?> </p>
<?php
		echo form_submit('reset', 'Reset password');
echo form_close(); 
?>

 
