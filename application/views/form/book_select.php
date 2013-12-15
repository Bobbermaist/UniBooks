
	  <h1>Risultati della ricerca</h1>
<?php
if( ! $books_data )
	echo '<p>La ricerca non ha prodotto risultati</p>';
else
{
	echo form_open('book/select_result');

	$this->table->set_heading('Titolo', 'Autori', 'Anno di pubblicazione', 'ISBN', 'Pagine', 'Materia'/*, 'Lingua'*/);
	$book_id = 0;
	foreach ( $books_data as $book )
	{
		$radio = array(
			'name'	=> 'book_select',
    	'id'		=> 'book_select',
    	'value'	=> $book_id
    );
    $book_id++;
		array_push($book, form_radio($radio));
		$this->table->add_row($book);
	}
	echo $this->table->generate();

	echo form_submit('select', 'Seleziona');
	echo form_close();
}
?>
 
