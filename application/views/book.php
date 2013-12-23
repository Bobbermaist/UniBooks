  <ul>
<?php

if( $title !== 'Unknown' )
	echo "\t<li>Titolo: $title</li>\n";

if( $authors[0] !== 'Unknown' )
{
	if( count($authors) == 1 )
		echo "\t<li>Autore: " . $authors[0] . "</li>\n";
	else
		echo "\t<li>Autori: " . implode(', ', $authors) . "</li>\n";
}

if( $publisher !== 'Unknown' )
	echo "\t<li>Editore: $publisher</li>\n";

if( $publication_year )
	echo "\t<li>Anno: $publication_year</li>\n";

if( $pages )
	echo "\t<li>Pagine: $pages</li>\n";

if( $categories[0] !== 'Unknown' )
{
	if( count($categories) == 1 )
		echo "\t<li>Categoria: " . $categories[0] . "</li>\n";
	else
		echo "\t<li>Categoria: " . implode(', ', $categories) . "</li>\n";
}

if( $language !== 'Unknown' )
	echo "\t<li>Lingua: $language</li>\n";

if( $ISBN )
	echo "\t<li>ISBN: $ISBN</li>\n";

if( isset($price) )
	echo "\t<li>Prezzo di vendita: € $price</li>\n";

?>
  </ul>