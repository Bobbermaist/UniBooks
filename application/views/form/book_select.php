		<h1>Risultati della ricerca</h1>
<?php
if( ! $books_data )
	echo '<p>La ricerca non ha prodotto risultati</p>';
else
{
	echo form_open('book/select_result');

	$this->table->set_heading('Titolo', 'Autori', 'Anno di pubblicazione', 'ISBN', 'Pagine', 'Materia'/*, 'Lingua'*/);
	foreach ( $books_data as $key => $book )
	{
		$radio = array(
			'name'	=> 'book_select',
			'id'		=> 'book_select',
			'value'	=> $key
		);
		array_push($book, form_radio($radio));
		$this->table->add_row($book);
	}
	echo $this->table->generate();

	echo form_submit('select', 'Seleziona');
	echo form_close();
}
?>