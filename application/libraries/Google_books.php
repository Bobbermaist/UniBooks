<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once GOOGLE_API_PATH . 'Google_Client.php';
require_once GOOGLE_API_PATH . 'contrib/Google_BooksService.php';

class Google_books {

  private $_CI;

  private $_service;

  private $_search_id = 0;

  private $_index = 0;

  public $volumes;

  public $total_items = 0;

  public $search_key;

  public function __construct()
  {
    $this->_CI =& get_instance();
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

  public function set_search_key($str)
  {
    $this->search_key = trim($str);
  }

  public function empty_google_cache()
  {
    $this->_CI->load->helper('file');
    delete_files(GOOGLE_CACHE, TRUE);
  }

  public function get($data, $index = 0)
  {
    $this->_get_service();
    $this->_index = $index;
    $query = $data;

    if (is_array($data))
    {
      $query = isset($data['title']) ? 'intitle:' . $data['title'] . ' ' : '';
      $query .= isset($data['author']) ? 'inauthor:' . $data['author'] . ' ' : '';
      $query .= isset($data['publisher']) ? 'inpublisher:' . $data['publisher'] . ' ' : '';
      $query .= isset($data['subject']) ? 'subject:' . $data['subject'] . ' ' : '';
    }
    $this->set_search_key($query);
    $this->_list_volumes();
  }

  public function get_by_isbn($isbn)
  {
    $this->_get_service();
    $this->search_key = "isbn:$isbn";
    //$this->set_search_key();
    $this->_list_volumes(FALSE);
  }

  private function _get_service()
  {
    $client = new Google_Client();
    $this->_service = new Google_BooksService($client);
  }

  private function _list_volumes($caching = TRUE)
  {
    if ($caching === TRUE)
    {
      $this->_CI->load->database();
      $this->_get_search_id();
      if ($this->_get_results() === TRUE)
        return;
    }

    $opt_params = array(
      'startIndex'  => $this->_index,
      'maxResults'  => MAX_RESULTS,
    );
    $google_fetch = $this->_service->volumes->listVolumes($this->search_key, $opt_params);
    $this->volumes = $this->_array_format($google_fetch);
    $this->total_items = $google_fetch['totalItems'];

    if ($caching === TRUE)
    {
      if ($this->_search_id === 0)
      {
        $this->fetch_total_items();
        $this->insert_search_key();
      }
      $this->insert_results();
    }
  }

    /* caching methods */
  private function _insert_search_key()
  {
    $data = array(
      'search_key'  => $this->search_key,
      'total_items' => $this->total_items,
    );
    $this->_CI->db->insert('google_search_keys', $data);
    $this->_search_id = $this->_CI->db->insert_id();
  }

  private function _insert_results()
  {
    $data = array(
      'search_id' => $this->_search_id,
      'index'     => $this->_index,
      'results'   => serialize($this->volumes),
    );
    $this->_CI->db->insert('google_results', $data);
  }

  private function _get_search_id()
  {
    $this->_CI->db->from('google_search_keys')->where('search_key', $this->search_key)->limit(1);
    $query = $this->_CI->db->get();
    if ($query->num_rows == 1)
    {
      $res = $query->row();
      $this->_search_id = $res->ID;
      $this->total_items = $res->total_items;
    }
  }

  private function _get_results()
  {
    if ($this->_search_id == 0)
      return FALSE;
    $where_clause = array(
      'search_id' => $this->_search_id,
      'index'     => $this->_index,
    );
    $this->_CI->db->select('results')->from('google_results')->where($where_clause)->limit(1);
    $query = $this->_CI->db->get();
    if ($query->num_rows == 1)
    {
      $this->volumes = unserialize(utf8_decode($query->row()->results));
      return TRUE;
    }
    return FALSE;
  }

    /* recoursive method to retrieve real total items */
  private function _fetch_total_items()
  {
    define('JUMP', 300);

      /* return, total_items must be ok! */
    if ($this->total_items <= MAX_RESULTS)
      return;

    if ($this->total_items > 1000)
      $this->total_items -= JUMP;

    $query = 'https://www.googleapis.com/books/v1/volumes?q='
      . urlencode($this->search_key) . "&startIndex={$this->total_items}";
    $fetch = file_get_contents($query, FALSE, NULL, -1, 50);
    preg_match('/(?<=("totalItems":\s))(\d+)/', $fetch, $res);

    if (isset($res[0]))
    {
      $fetched_total_items = (int) $res[0];
      if ($this->total_items === $fetched_total_items)
        return
      $this->total_items = $fetched_total_items;
    }
    else
    {
      $this->total_items -= JUMP;
    }
    $this->_fetch_total_items();
  }

  private function _array_format($google_fetch, $only_isbn = FALSE)
  {
    if ($google_fetch['totalItems'] == 0 OR ! isset($google_fetch['items']))
      return NULL;

    $books = array();
    foreach($google_fetch['items'] as $item)
    {
      $google_id = $item['id'];
      $item = $item['volumeInfo'];
      if (isset($item['industryIdentifiers']))
      {
        $isbn = $this->_industryID_to_ISBN($item['industryIdentifiers']);
      }
      else
      {
        $isbn = NULL;
      }
      
          /* Escludo i risultati senza ISBN */
      if ($isbn === NULL AND $only_isbn === TRUE)
        continue;

      $books[] = array(
        'ISBN'              => $isbn,
        'google_id'         => $google_id,
        'title'             => $item['title'],
        'authors'           => isset($item['authors']) ? $item['authors'] : NULL,
        'publisher'         => isset($item['publisher']) ? $item['publisher'] : NULL,
        'publication_year'  => isset($item['publishedDate']) ? substr($item['publishedDate'], 0, 4) : NULL,
        'pages'             => isset($item['pageCount']) ? $item['pageCount'] : NULL,
        'categories'        => isset($item['categories']) ? $item['categories'] : NULL,
        'language'          => isset($item['language']) ? $item['language'] : NULL,
      );
    }
    return $books;
  }

  private function _industryID_to_ISBN($industryIdentifiers)
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
/* End of file MY_Google_books.php */
/* Location: ./application/libraries/MY_Google_books.php */ 