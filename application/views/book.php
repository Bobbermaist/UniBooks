
	<ul>
		<li>Titolo: <?= $title ?> </li>
		<li><?php
if( count($authors) == 1 )
	echo 'Autore: ' . $authors[0];
else
	echo 'Autori: ' . implode(', ', $authors);
?> </li>
		<li>Editore: <?= $publisher ?> </li>
		<li>Anno: <?= $publication_year ?> </li>
		<li>Pagine: <?= $pages ?> </li>
		<li><?php
if( count($categories) == 1 )
	echo 'Categoria: ' . $categories[0];
else
	echo 'Categoria: ' . implode(', ', $categories);
?> </li>
		<li>Lingua: <?= $language ?> </li>
		<li>ISBN: <?= $ISBN ?> </li>
	</ul>
