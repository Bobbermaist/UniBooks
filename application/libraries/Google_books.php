<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package UniBooks
 * @author Emiliano Bovetti
 * @since Version 1.0
 */

/**
 * Require google API
 */
require_once GOOGLE_API_PATH . 'Google_Client.php';
require_once GOOGLE_API_PATH . 'contrib/Google_BooksService.php';

/**
 * UniBooks Google_books class.
 *
 * Provides an interface to the
 * google books API
 *
 * @package UniBooks
 * @category Libraries
 * @author Emiliano Bovetti
 */
class Google_books {

  /**
   * CodeIgniter istance
   *
   * @var object
   * @access private
   */
  private $_CI;

  /**
   * Google service
   *
   * @var object
   * @access private
   */
  private $_service;

  /**
   * Array with all volumes retrieved
   *
   * @var array
   */
  public $volumes = array();

  /**
   * Total books retrieved
   *
   * @var int
   */
  public $total_items = 0;

  /**
   * Search key
   *
   * @var string
   */
  public $search_key;

  /**
   * Constructor
   *
   * Get the CI instance
   *
   * @return void
   */
  public function __construct()
  {
    $this->_CI =& get_instance();
  }

  /**
   * There is a chance out of 50 
   * destructor remove all google cache
   */
  public function __destruct()
  {
    if (rand(1, 50) == 1)
    {
      $this->empty_google_cache();
    }
  }

  /**
   * Set search key
   *
   * @param string  $str the key for the search
   * @return void
   */
  public function set_search_key($str)
  {
    $this->search_key = trim($str);
  }

  /**
   * Creates a new google clien and set
   * _service.
   *
   * @return void
   * @access private
   */
  private function _get_service()
  {
    $client = new Google_Client();
    $this->_service = new Google_BooksService($client);
  }

  /**
   * Deletes all files in GOOGLE_CACHE
   * directory
   *
   * @return void
   */
  public function empty_google_cache()
  {
    $this->_CI->load->helper('file');
    delete_files(GOOGLE_CACHE, TRUE);
  }

  /**
   * Get a book by its ISBN.
   * 
   * @param string  $isbn the ISBN code
   * @return void
   */
  public function get_by_isbn($isbn)
  {
    $this->_get_service();
    $this->set_search_key("isbn:{$isbn}");
    $this->_list_volumes();
  }

  /**
   * Makes the query to google books.
   * Sets properties volumes and total_items
   * with fetched data.
   *
   * @return void
   * @access private
   */
  private function _list_volumes()
  {
    $opt_params = array(
      'maxResults'  => 10,
    );
    $google_fetch = $this->_service->volumes->listVolumes($this->search_key, $opt_params);
    var_dump($google_fetch);
    $this->volumes = $this->_array_format($google_fetch);
    $this->total_items = $google_fetch['totalItems'];
  }

  /**
   * Reformats google data to a simpler associative array.
   * Indexes can be setted with fetched data or NULL otherwise.
   * 
   * @param array  $google_fetch the output of service->volumes->listVolumes()
   * @return mixed array or NULL
   * @access private
   */
  private function _array_format($google_fetch)
  {
    if (empty($google_fetch['totalItems']) OR empty($google_fetch['items']))
    {
      return NULL;
    }

    $books = array();
    foreach($google_fetch['items'] as $item)
    {
      $google_id = $item['id'];
      $item = $item['volumeInfo'];

      $books[] = array(
        'ISBN_13'           => $this->_get_isbn($item, '13'),
        'ISBN_10'           => $this->_get_isbn($item, '10'),
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

  /**
   * Finds ISBN codes in 'volumeInfo' array.
   *
   * @param array  $item the $google_fetch['items'] array
   * @param string  $type the ISBN type ('13' or '10')
   * @return mixed  string or NULL
   * @access private
   */
  private function _get_isbn($item, $type = '13')
  {
    if (isset($item['industryIdentifiers']))
    {
      $type = "ISBN_$type";
      foreach ($item['industryIdentifiers'] as $industry_id)
      {
        if ($industry_id['type'] === $type)
          return $industry_id['identifier'];
      }
    }
    return NULL;
  }
}

// END Google_books class

/* End of file Google_books.php */
/* Location: ./application/libraries/Google_books.php */ 