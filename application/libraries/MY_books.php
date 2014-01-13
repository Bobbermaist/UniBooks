<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once GOOGLE_API_PATH . 'Google_Client.php';
require_once GOOGLE_API_PATH . 'contrib/Google_BooksService.php';

class MY_books {

  var $CI;
  var $service;
  var $search_key;
  var $search_id = 0;
  var $index = 0;
  var $volumes;
  var $total_items;

  public function __construct()
  {
    $this->CI =& get_instance();
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

  public function get_service()
  {
    $client = new Google_Client();
    $this->service = new Google_BooksService($client);
  }

  public function set_search_key($str)
  {
    $this->search_key = trim($str);
  }

  public function get($data, $index = 0)
  {
    $this->get_service();
    $this->index = $index;
    if ( ! is_array($data))
    {
      $this->set_search_key($data);
      $this->list_volumes();
      return;
    }
    $query = isset($data['title']) ? 'intitle:' . $data['title'] . ' ' : '';
    $query .= isset($data['author']) ? 'inauthor:' . $data['author'] . ' ' : '';
    $query .= isset($data['publisher']) ? 'inpublisher:' . $data['publisher'] . ' ' : '';
    $query .= isset($data['subject']) ? 'subject:' . $data['subject'] . ' ' : '';
    $this->set_search_key($query);
    $this->list_volumes();
  }

  public function get_by_isbn($isbn)
  {
    $this->get_service();
    $this->set_search_key("isbn:$isbn");
    $this->list_volumes();
  }

  private function list_volumes()
  {
    $this->CI->load->database();

    $this->get_search_id();
    if ($this->get_results() === TRUE)
      return;

    $opt_params = array(
      'startIndex'  => $this->index,
      'maxResults'  => MAX_RESULTS,
    );
    $google_fetch = $this->service->volumes->listVolumes($this->search_key, $opt_params);
    $this->volumes = $this->array_format($google_fetch);
    if ($this->search_id === 0)
    {
      $this->total_items = $google_fetch['totalItems'];
      $this->fetch_total_items();
      $this->insert_search_key();
    }
    $this->insert_results();
  }

  private function insert_search_key()
  {
    $data = array(
      'search_key'  => $this->search_key,
      'total_items' => $this->total_items,
    );
    $this->CI->db->insert('google_search_keys', $data);
    $this->search_id = $this->CI->db->insert_id();
  }

  private function insert_results()
  {
    $data = array(
      'search_id' => $this->search_id,
      'index'     => $this->index,
      'results'   => serialize($this->volumes),
    );
    $this->CI->db->insert('google_results', $data);
  }

  private function get_search_id()
  {
    $this->CI->db->from('google_search_keys')->where('search_key', $this->search_key)->limit(1);
    $query = $this->CI->db->get();
    if ($query->num_rows == 1)
    {
      $res = $query->row();
      $this->search_id = $res->ID;
      $this->total_items = $res->total_items;
    }
  }

  private function get_results()
  {
    if ($this->search_id == 0)
      return FALSE;
    $where_clause = array(
      'search_id' => $this->search_id,
      'index'     => $this->index,
    );
    $this->CI->db->select('results')->from('google_results')->where($where_clause)->limit(1);
    $query = $this->CI->db->get();
    if ($query->num_rows == 1)
    {
      $this->volumes = unserialize(utf8_decode($query->row()->results));
      return TRUE;
    }
    return FALSE;
  }

  private function fetch_total_items()
  {
    if ($this->total_items >= 0 AND $this->total_items <= 10)
      return;
    if ($this->total_items > 1000)
      $this->total_items -= 300;
    $query = 'https://www.googleapis.com/books/v1/volumes?q='
      . urlencode($this->search_key) . '&startIndex=' . $this->total_items;
    $regex = '/(?<=("totalItems":\s))(\d+)/';
    $fetch = file_get_contents($query, FALSE, NULL, -1, 50);
    preg_match($regex, $fetch, $res);
    $total_items = isset($res[0]) ? (int) $res[0] : 0;
    if ($this->total_items !== $total_items)
    {
      $this->total_items = $total_items;
      $this->fetch_total_items();
    }
  }

  private function array_format($google_fetch, $only_isbn = FALSE)
  {
    if ($google_fetch['totalItems'] == 0 OR ! isset($google_fetch['items']))
      return NULL;
    $books = array();
    foreach($google_fetch['items'] as $item)
    {
      $google_id = $item['id'];
      $item = $item['volumeInfo'];
      $isbn = isset($item['industryIdentifiers']) ? 
        $this->industryID_to_ISBN($item['industryIdentifiers']) :
        NULL;
      
          /* Escludo i risultati senza ISBN */
      if ($isbn === NULL AND $only_isbn === TRUE)
        continue;
      $book = array(
        'ISBN'              => $isbn,
        'google_id'         => $google_id,
        'title'             => $item['title'],
        'authors'           => isset($item['authors']) ? $item['authors'] : NULL,
        'publisher'         => isset($item['publisher']) ? $item['publisher'] : $this->get_publisher($isbn),
        'publication_year'  => isset($item['publishedDate']) ? substr($item['publishedDate'], 0, 4) : NULL,
        'pages'             => isset($item['pageCount']) ? $item['pageCount'] : NULL,
        'categories'        => isset($item['categories']) ? $item['categories'] : NULL,
        'language'          => isset($item['language']) ? $item['language'] : NULL,
      );
      array_push($books, $book);
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