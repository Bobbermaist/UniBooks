	<?= form_open('user/login') ?>
	  
	  <h1>Login</h1>
	  <p>Nickname: <?= form_input($user_name) ?> </p>
	  <p>Password: <?= form_password($pass) ?> </p>
	<?php echo form_submit('login', 'Log In!');
echo form_close();

echo validation_errors();
?>