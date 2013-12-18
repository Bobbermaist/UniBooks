
<ul>
<?php
if( strcmp($title, 'Unknown') )
	echo '<li>Titolo: ' . $title . '</li>';

if( strcmp($authors[0], 'Unknown') )
{
	if( count($authors) == 1 )
		echo '<li>Autore: ' . $authors[0] . '</li>';
	else
		echo '<li>Autori: ' . implode(', ', $authors) . '</li>';
}
if( strcmp($publisher, 'Unknown') )
	echo '<li>Editore: ' . $publisher . '</li>';
if( $publication_year )
	echo '<li>Anno: ' . $publication_year . '</li>';
if( $pages )
	echo '<li>Pagine: ' . $pages . '</li>';
if( strcmp($categories[0], 'Unknown') )
{
	if( count($categories) == 1 )
		echo '<li>Categoria: ' . $categories[0] . '</li>';
	else
		echo '<li>Categoria: ' . implode(', ', $categories) . '</li>';
}
if( strcmp($language, 'Unknown') )
	echo '<li>Lingua: ' . $language . '</li>';
if( $ISBN )
	echo '<li>ISBN: ' . $ISBN . '</li>';
?>
</ul>