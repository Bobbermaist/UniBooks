
<?php echo form_open('book/search'); ?> 
	  <h1>Ricerca un libro</h1>
	  <p>
	  	<input type='text' name='book_search' maxlength='255' required autofocus>
	  </p>
<?php
		echo form_submit('search', 'Cerca');
echo form_close();
?> 
