
<?= form_open($redirect) ?> 
	  <h1><?= $title ?></h1>
	  <p> <?= form_input($input_type) ?> </p>
<?php
		echo form_submit($submit_name, $submit_value);
echo form_close();
echo validation_errors();
?>
