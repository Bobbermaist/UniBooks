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
 * UniBooks Book_model class.
 *
 * Extends Book_base class and provides all
 * methods to manage books.
 *
 * @package UniBooks
 * @category Models
 * @author Emiliano Bovetti
 */
class Book_model extends Book_base {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Return the object properties as an associative array.
	 *
	 * @return array
	 */
	public function get_array()
	{
		return array(
			'ID'								=> $this->ID,
			'ISBN_13'						=> $this->ISBN_13,
			'ISBN_10'						=> $this->ISBN_10,
			'google_id'					=> $this->google_id,
			'title'							=> $this->title,
			'authors'						=> $this->authors,
			'publisher'					=> $this->publisher,
			'publication_year'	=> $this->publication_year,
			'pages'							=> $this->pages,
			'language'					=> $this->language,
			'categories'				=> $this->categories,
		);
	}

	/**
	 * Search for a book.
	 * Throws an exception if can't find
	 *
	 * @param string
	 * @return void
	 */
	public function search($key)
	{
		if ($this->set_isbn($key) === TRUE)
		{
			return $this->_search_by_isbn();
		}
		
		throw new Custom_exception(BOOK_NOT_FOUND);
	}

	/**
	 * Search by ISBN code.
	 * 
	 * Search first in local db, if ISBN code is not found
	 * calls the google_books library to retrieve book data.
	 *
	 * If google books api fail throws an exception
	 *
	 * @return void
	 * @access private
	 */
	private function _search_by_isbn()
	{
		if ($this->select_by('ISBN') === FALSE)
		{
			$this->load->library('google_books');
			$this->google_books->get_by_isbn( $this->get_isbn() );
			if ($this->google_books->total_items === 0)
			{
				throw new Custom_exception(ISBN_NOT_FOUND);
			}
			$this->_set_from_google();
			$this->insert();
		}
	}

	/**
	 * Set the object properties from google data.
	 * The query to google_books library must be done before
	 *
	 * @return void
	 * @access private
	 */
	private function _set_from_google()
	{
		$book_data = $this->google_books->volumes[0];

		if ( ! isset($this->ISBN_13) AND $book_data['ISBN_13'] !== NULL)
		{
			$this->ISBN_13 = $book_data['ISBN_13'];
		}
		if ( ! isset($this->ISBN_10) AND $book_data['ISBN_10'] !== NULL)
		{
			$this->ISBN_10 = $book_data['ISBN_10'];
		}
		$this->google_id = $book_data['google_id'];
		$this->title = $book_data['title'];
		$this->authors = $book_data['authors'];
		if ($book_data['publisher'] === NULL)
		{
			$this->publisher = $this->_get_publisher();
		}
		else
		{
			$this->publisher = $book_data['publisher'];
		}
		$this->publication_year = $book_data['publication_year'];
		$this->pages = $book_data['pages'];
		$this->language = $book_data['language'];
		$this->categories = $book_data['categories'];
	}
}

// END Book_model class

/* End of file book_model.php */
/* Location: ./application/models/book_model.php */
