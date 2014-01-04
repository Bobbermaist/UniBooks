<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once GOOGLE_API_PATH . 'Google_Client.php';
require_once GOOGLE_API_PATH . 'contrib/Google_BooksService.php';

class MY_books {

	var $CI;
	var $service;
  var $volumes;
  var $total_items;

  public function __construct()
  {
		$this->CI =& get_instance();
  	$client = new Google_Client();
  	$this->service = new Google_BooksService($client);
  }

  public function __destruct()
  {
      /*
       * C'è una possibilità su 50 che il
       * distruttore cancelli tutta la
       * cache di Google
       */
    if (rand(1, 50) == 1)
      $this->empty_google_cache();
  }

  public function get($data, $index = 0)
  {
  	if ( ! is_array($data))
    {
      $this->list_volumes($data, $index);
      return;
    }
  	$query = isset($data['title']) ? 'intitle:' . $data['title'] . ' ' : '';
  	$query .= isset($data['author']) ? 'inauthor:' . $data['author'] . ' ' : '';
  	$query .= isset($data['publisher']) ? 'inpublisher:' . $data['publisher'] . ' ' : '';
  	$query .= isset($data['subject']) ? 'subject:' . $data['subject'] . ' ' : '';
  	$this->list_volumes($query, $index);
  }

  public function get_by_isbn($isbn)
  {
  	$this->list_volumes("isbn:$isbn", 0);
  }

  private function list_volumes($str, $index)
  {
    $opt_params = array(
      'startIndex'  => $index,
      'maxResults'  => MAX_RESULTS,
    );
    $google_fetch = $this->service->volumes->listVolumes($str, $opt_params);
    $this->total_items = $google_fetch['totalItems'];
    $this->volumes = $this->array_format($google_fetch, FALSE);
  }

  private function array_format($google_fetch, $with_isbn = TRUE)
  {
    if ($google_fetch['totalItems'] == 0 OR ! isset($google_fetch['items']))
      return NULL;
    $books = array();
    foreach($google_fetch['items'] as $item)
    {
      $item = $item['volumeInfo'];

      $item['ISBN'] = $this->industryID_to_ISBN($item['industryIdentifiers']);
        /* Escludo i risultati senza ISBN */
      if ($item['ISBN'] === NULL AND $with_isbn === TRUE)
        continue;
      unset($item['industryIdentifiers']);
      unset($item['printType']);
      unset($item['averageRating']);
      unset($item['ratingsCount']);
      unset($item['contentVersion']);
      unset($item['imageLinks']);
      unset($item['previewLink']);
      unset($item['infoLink']);
      unset($item['canonicalVolumeLink']);
      unset($item['description']);
      $item['publisher'] = (isset($item['publisher'])) ? $item['publisher'] : $this->get_publisher($item['ISBN']);
      $item['authors'] = (isset($item['authors'])) ? $item['authors'] : NULL;
      $item['publication_year'] = (isset($item['publishedDate'])) ? substr($item['publishedDate'], 0, 4) : NULL;
      unset($item['publishedDate']);
      $item['pages'] = (isset($item['pageCount'])) ? $item['pageCount'] : NULL;
      unset($item['pageCount']);
      $item['categories'] = (isset($item['categories'])) ? $item['categories'] : NULL;
      $item['language'] = (isset($item['language'])) ? $item['language'] : NULL;

      array_push($books, $item);
    }
    return $books;
  }

  public function get_publisher($isbn)
  {
    $this->CI->load->database();
    if (strlen($isbn) == 13)
      $isbn = substr($isbn, 3);
    for($digits = 7; $digits > 3; $digits--)
    {
      $this->CI->db->from('publisher_codes')->where('code', substr($isbn, 0, $digits));
      $res = $this->CI->db->get();
      if ($res->num_rows > 0)
        return $res->row()->name;
    }
    return NULL;
  }

  public function empty_google_cache()
  {
    $this->CI->load->helper('file');
    delete_files(GOOGLE_CACHE, TRUE);
  }

  private function industryID_to_ISBN($industryIdentifiers)
  {
    $isbn10 = NULL;
    foreach($industryIdentifiers as $iid)
    {
      if ($iid['type'] === 'ISBN_13')
      {
        $isbn13 = $iid['identifier'];
        break;
      }
      elseif ($iid['type'] === 'ISBN_10')
        $isbn10 = $iid['identifier'];
    }
    return isset($isbn13) ? $isbn13 : $isbn10;
  }
}

/* End of file MY_books.php */
/* Location: ./application/libraries/MY_books.php */ 