  <ul>
    <li>Titolo: <?php echo $title; ?> </li>

<?php if (count($authors) === 1 AND $authors[0] !== 'Unknown'): ?>

    <li>Autore: <?php echo $authors[0]; ?> </li>

<?php elseif (count($authors) > 1): ?>

    <li>Autori: <?php echo implode(', ', $authors); ?> </li>

<?php endif;

			if ($publisher !== 'Unknown'): ?>

    <li>Editore: <?php echo $publisher; ?> </li>

<?php endif;

			if ($publication_year): ?>

    <li>Anno: <?php echo $publication_year; ?> </li>

<?php endif;

			if ($pages): ?>

			<li>Pagine: <?php echo $pages; ?> </li>

<?php endif;

			if (count($categories) === 1 AND $categories[0] !== 'Unknown'): ?>

    <li>Categoria: <?php echo $categories[0] ?> </li>

<?php elseif (count($categories) > 1): ?>

    <li>Categorie: <?php echo implode(', ', $categories); ?> </li>

<?php endif;

			if ($language !== 'Unknown'): ?>

    <li>Lingua: <?php echo $language; ?> </li>

<?php endif; ?>

    <li>ISBN 13: <?php echo $ISBN_13; ?> </li>
    <li>ISBN 10: <?php echo $ISBN_10; ?> </li>

<?php if (isset($price)): ?>

    <li>Prezzo di vendita: € <?php echo $price; ?> </li>

<?php endif; ?>
  </ul>