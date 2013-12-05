
<?php echo form_open('user/reset_pass'); ?>
	  <h1>Scegli una nuova password</h1>
	  <p>
	  	<input type="password" name="pass" maxlength="64" required>
	  </p>
<?php
		echo form_hidden('ID', $ID);
		echo form_hidden('activation_key', $activation_key);
		echo form_submit('reset_pass', 'Conferma');
echo form_close(); 
?>

 
