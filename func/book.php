<?php

require_once 'google/Google_Client.php';
require_once 'google/contrib/Google_BooksService.php';

class Book {
	
	public $title, $authors, $publisher, $pubDate;
	private $ISBN;
	
	public function __construct($isbn) {	/* Inizializza la variabile di istanza ISBN se la stringa in
											 * ingresso rispetta il formato corretto */
		$this->ISBN = strtoupper(preg_replace("/[^\d^X]+/i", "", $isbn));
	}
	
	public function goFetch() {		/* Fetch dei dati da google books! */
		if( !isset($this->ISBN) )
			exit;
		$client = new Google_Client();
		$client->setDeveloperKey('AIzaSyB_qpOh6a5EddC32mNT7Cqo7Qi9IeV-vU0');
		$client->setApplicationName("UniBooks");
		$service = new Google_BooksService($client);
		$results = $service->volumes->listVolumes("isbn=$this->ISBN");
		echoBookList($results);
	}
	
	public function validate() {	/* Controlla se la variabile di istanza ISBN e' un isbn 10 o 13 valido */
		$len = strlen($this->ISBN);
		if( $len != 13 && $len != 10 )
			return false;
		if( $len == 10 && validate10($this->ISBN) )
			return true;
		elseif( $len == 10 ) {
			$this->ISBN = "978$this->ISBN";
			return validate13($this->ISBN);
		} else
			return validate13($this->ISBN);
	}
}

function validate10($ISBN10) {	/* Controlla se una stringa e' un ISBN10 valido */
	$a = 0;
	for($i = 0; $i < 10; $i++){
		if ( $ISBN10[$i] == "X" ) {
			$a += 10 * intval(10 - $i);
		} else {
			$a += intval($ISBN10[$i]) * intval(10 - $i);
		}
	}
	return ($a % 11 == 0);
}

function validate13($n) {	/* Controlla se una stringa e' un ISBN13 valido */
    $check = 0;
    for ($i = 0; $i < 13; $i+=2) $check += substr($n, $i, 1);
    for ($i = 1; $i < 12; $i+=2) $check += 3 * substr($n, $i, 1);
    return $check % 10 == 0;
}

function echoBookList($results) {	/* Serve solo pe testa' la roba di google */
  print <<<HTML
  <table><tr><td id="resultcell">
  <div id="searchResults">
    <table class="volumeList"><tbody>
HTML;
  foreach ($results['items'] as $result) {
    $volumeInfo = $result['volumeInfo'];
    $title = $volumeInfo['title'];
    if (isset($volumeInfo['imageLinks']['smallThumbnail'])) {
      $thumbnail = $volumeInfo['imageLinks']['smallThumbnail'];
    } else {
      $thumbnail = null;
    }

    if (isset($volumeInfo['authors'])) {
      $creators = implode(", ", $volumeInfo['authors']);
      if ($creators) $creators = "by " . $creators;
    }

    $preview = $volumeInfo['previewLink'];
    $previewLink = '';
    if ($result['accessInfo']['embeddable'] == true) {
      $previewLink = ""
          . "<a href=\"javascript:load_viewport('${preview}','viewport');\">"
          . "<img class='previewbutton' src='http://code.google.com/apis/books/images/gbs_preview_button1.png' />"
          . "</a><br>";
    }

    $thumbnailImg = ($thumbnail)
        ? "<a href='${preview}'><img alt='$title' src='${thumbnail}'/></a>"
        : '';
    print <<<HTML
    <tr>
    <td><div class="thumbnail">${thumbnailImg}</div></td>
    <td width="100%">
        <a href="${preview}">$title</a><br>
        ${creators}<br>
        ${previewLink}
    </td></tr>
HTML;
  }
  print <<<HTML
  </table></div></td>
      <td width=50% id="previewcell"><div id="viewport"></div>&nbsp;
  </td></tr></table><br></body></html>
HTML;
}

?>