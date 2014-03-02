<?php
	echo form_open('user/settings');
?>
	  <p>Modifica password</p>
	  <p>Vecchia password: <?php echo form_password($old_password); ?> </p>
	  <p>Nuova password: <?php echo form_password($new_password); ?> </p>
	  <p>Conferma password: <?php echo form_password($passconf); ?> </p>
<?php
		echo form_submit('modify_password', 'Modifica');
	echo form_close(); 
echo validation_errors();
?>

