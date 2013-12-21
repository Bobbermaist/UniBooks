
<?php echo form_open('user/reset_pass'); ?>
	  <h1>Scegli una nuova password</h1>
	  <p> <?php echo form_password($new_password_form_data); ?> </p>
<?php
		echo form_hidden('ID', $ID);
		echo form_hidden('confirm_code', $confirm_code);
		echo form_submit('reset_pass', 'Conferma');
echo form_close(); 
?>

 
