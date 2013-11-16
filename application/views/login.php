 
	<form name='login' method='post' action='<?php echo $_SERVER["PHP_SELF"] ?>' >
	  <h1>Login</h1>
	  <p>Nickname: <input type='text' name='user' maxlength='60' required autofocus> </p>
	  <p>Password: <input type='password' name='pass' maxlength='64' required></p>
	  <input type='submit' name='login' value='Login'>
	</form>
	