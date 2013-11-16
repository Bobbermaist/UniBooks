
<?php echo validation_errors(); ?>

<?php echo form_open('registration'); ?>
	  <h1>Iscrizione</h1>
	  <p>Nome utente:
	  	<input type="text" value="<?php echo set_value('username'); ?>" maxlength="20" required autofocus>
	  </p>
	  <p>Email: 
	  	<input type="email" value="<?php echo set_value('email'); ?>" maxlength="64" required>
	  </p>
	  <p>Password:
	  	<input type="password" value="<?php echo set_value('pass'); ?>" maxlength="64" required>
	  </p>
	  <p>Conferma password:
	  	<input type="password" value="<?php echo set_value('passconf'); ?>" maxlength="64" required>
	  </p>
	  <!-- <p>Accetto i termini e le condizioni d'uso di <b> 
	  	<?php //echo $_SERVER['SERVER_NAME']; ?> </b> <input type='checkbox' name='terms'> </p> -->
	  <input type="submit" value="<?php echo set_value('registration'); ?>" value="Iscriviti" >
	</form>

