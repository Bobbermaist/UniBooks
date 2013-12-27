
<?php echo form_open($action);

echo form_submit('delete', 'Elimina');

echo form_hidden('book_id', $book_id);

echo form_close();
