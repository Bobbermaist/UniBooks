<?php
	echo form_open('account/password');
?>
	  <h1>Modifica password</h1>
	  <p>Vecchia password: <?php echo form_password($old_pass_data); ?> </p>
	  <p>Nuova password: <?php echo form_password($new_pass_data); ?> </p>
	  <p>Conferma password: <?php echo form_password($passconf_data); ?> </p>
<?php
		echo form_submit('change', 'Modifica');
	echo form_close(); 
echo validation_errors();
?>

