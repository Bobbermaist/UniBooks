
<?php echo form_open('user/reset_pass'); ?>
	  <h1>Scegli una nuova password</h1>
	  <p>
	  	<input type="password" name="pass" maxlength="64" required>
	  </p>
	  	<input type="hidden" name="ID" value="<?php echo $ID; ?>">
	  	<input type="hidden" name="activation_key" value="<?php echo $activation_key; ?>">
<?php
		echo form_submit('reset_pass', 'Conferma');
echo form_close(); 
?>

 
