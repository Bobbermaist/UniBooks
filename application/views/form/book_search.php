
<?= form_open('book/search') ?> 
	  <h1>Ricerca un libro</h1>
	  <p> <?= form_input($book_search) ?> </p>
<?php
		echo form_submit('search', 'Cerca');
echo form_close();
?> 
