
<?php echo form_open('user/login'); ?> 
	  <h1>Login</h1>
	  <p>Nickname: <input type='text' name='user_name' maxlength='20' required autofocus> </p>
	  <p>Password: <input type='password' name='pass' maxlength='64' required></p>
<?php
		echo form_submit('login', 'Log In!');
echo form_close();
?>