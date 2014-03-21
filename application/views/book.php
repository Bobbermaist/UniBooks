  <ul>
    <li>Titolo: <?php echo $title; ?> </li>

    <li>Autori: <?php echo $authors; ?> </li>

<?php if ($publisher): ?>

    <li>Editore: <?php echo $publisher; ?> </li>

<?php endif; if ($publication_year): ?>

    <li>Anno: <?php echo $publication_year; ?> </li>

<?php endif; if ($pages): ?>

    <li>Pagine: <?php echo $pages; ?> </li>

<?php endif; ?>

    <li>Categorie: <?php echo $categories; ?> </li>

<?php if ($language): ?>

    <li>Lingua: <?php echo $language; ?> </li>

<?php endif; ?>

    <li>ISBN 13: <?php echo $ISBN_13; ?> </li>
    <li>ISBN 10: <?php echo $ISBN_10; ?> </li>

<?php if (isset($price)): ?>

    <li>Prezzo di vendita: € <?php echo $price; ?> </li>

<?php endif; ?>
  </ul>