
<?php
	echo form_open('account/change');
?>
	  <h1>Modifica dati</h1>
	  <p>Nome utente: <?php echo form_input($user_name_data); ?> </p>
	  <p>Email: <?php echo form_input($email_data); ?> </p>
	  <p>Vecchia password: <?php echo form_password($old_pass_data); ?> </p>
	  <p>Nuova password: <?php echo form_password($new_pass_data); ?> </p>
	  <p>Conferma password: <?php echo form_password($passconf_data); ?> </p>
<?php
		echo form_submit('change', 'Modifica');
	echo form_close(); 
echo validation_errors();
?>

