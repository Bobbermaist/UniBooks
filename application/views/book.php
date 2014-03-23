  <ul>
    <li>Titolo: <?php echo $title; ?> </li>

<?php if ($authors): ?>

    <li>Autori: <?php echo $authors; ?> </li>

<?php endif; if ($publisher): ?>

    <li>Editore: <?php echo $publisher; ?> </li>

<?php endif; if ($publication_year): ?>

    <li>Anno: <?php echo $publication_year; ?> </li>

<?php endif; if ($pages): ?>

    <li>Pagine: <?php echo $pages; ?> </li>

<?php endif; if ($categories): ?>

    <li>Categorie: <?php echo $categories; ?> </li>

<?php endif; if ($language): ?>

    <li>Lingua: <?php echo $language; ?> </li>

<?php endif; ?>

    <li>ISBN 13: <?php echo $ISBN_13; ?> </li>
    <li>ISBN 10: <?php echo $ISBN_10; ?> </li>

<?php if ( ! empty($price)): ?>

    <li>Prezzo di vendita: € <?php echo $price; ?> </li>

<?php endif; if ( ! empty($description)): ?>

    <li>Descrizione: <p> <?php echo $description; ?> </p> </li>

<?php endif; ?>

  </ul>