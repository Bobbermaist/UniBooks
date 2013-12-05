
<?php echo form_open('book/search'); ?> 
	  <h1>Ricerca libro</h1>
	  <p>
	  	<input type='text' name='book_data' maxlength='255' required autofocus>
	  </p>
<?php
		echo form_submit('search', 'Cerca');
echo form_close();
?> 
