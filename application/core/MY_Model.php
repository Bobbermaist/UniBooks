<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	protected function _get($property)
	{
		return isset($this->$property) ? $this->$property : FALSE;
	}
}

class Book_base extends MY_Model {
	
	protected $ID;

	protected $ISBN_13;

	protected $ISBN_10;

	protected $google_id;

	protected $title;

	protected $authors = array();

	private $_authors_id = array();

	protected $publisher;

	private $_publisher_id;

	protected $publication_year;

	protected $pages;

	protected $language;

	private $_language_id;

	protected $categories = array();

	private $_categories_id = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('isbn');
	}

	public function unset_all()
	{
		unset(
			$this->ID,
			$this->ISBN_13,
			$this->ISBN_10,
			$this->google_id,
			$this->title,
			$this->authors,
			$this->_authors_id,
			$this->publisher,
			$this->_publisher_id,
			$this->publication_year,
			$this->pages,
			$this->language,
			$this->_language_id,
			$this->categories,
			$this->_categories_id
		);
	}

	private function _get_ISBN()
	{
		if ($this->_get('ISBN_13') !== FALSE)
		{
			return $this->_get('ISBN_13');
		}
		return $this->_get('ISBN_10');
	}

	public function ISBN($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_get_ISBN();
		}

		$isbn = strtoupper(trim($value));
		$valid = validate($isbn);
		if ($valid === 13)
		{
			return (boolean) $this->ISBN_13 = $isbn;
		}
		if ($valid === 10)
		{
			return (boolean) $this->ISBN_10 = $isbn;
		}
		if (strlen($isbn) === 10)
		{
			return $this->ISBN("978$isbn");
		}
		return FALSE;
	}

	public function insert()
	{
		$this->load->database();

		$this->_publisher_id = $this->_insert_info('publishers', $this->publisher);
		$this->_language_id = $this->_insert_info('languages', $this->language);
		$this->_authors_id = $this->_insert_info('authors', $this->authors);
		$this->_categories_id = $this->_insert_info('categories', $this->categories);

		$this->db->insert('books', array(
			'ISBN'							=> cut_isbn( $this->ISBN() ),
			'google_id'					=> $this->google_id,
			'title'							=> $this->title,
			'publisher_id'			=> $this->_publisher_id,
			'publication_year'	=> $this->publication_year,
			'pages'							=> $this->pages,
			'language_id'				=> $this->_language_id,
		));
		$this->ID = $this->db->insert_id();

		$this->_insert_authors();
		$this->_insert_categories();
	}

	private function _insert_info($table, $value)
	{
		if ($value === NULL)
		{
			return $this->_insert_info($table, 'Unknown');
		}

		if (is_array($value) === FALSE)
		{
			$result = $this->_select($table, 'name', $value);
			if ($result !== FALSE)
			{
				return $result->ID;
			}
			$this->db->insert($table, array('name' => $value));
			return $this->db->insert_id();
		}

		$ids = array();
		foreach ($value as $each)
		{
			$ids[] = $this->_insert_info($table, $each);
		}
		return $ids;
	}

	private function _insert_authors()
	{
		$data = array();
		foreach ($this->_authors_id as $author_id)
		{
			$data[] = array(
				'book_id'		=> $this->ID,
				'author_id'	=> $author_id,
			);
		}
		$this->db->insert_batch('links_book_author', $data);
	}

	private function _insert_categories()
	{
		$data = array();
		foreach ($this->_categories_id as $category_id)
		{
			$data[] = array(
				'book_id'			=> $this->ID,
				'category_id'	=> $category_id,
			);
		}
		$this->db->insert_batch('links_book_category', $data);
	}

	private function _select($table, $field, $value)
	{
		$this->db->from($table)->where($field, $value)->limit(1);
		$query = $this->db->get();
		return $query->num_rows == 1 ? $query->row() : FALSE;
	}

	public function select_by($field)
	{
		$this->load->database();
		$this->db->from('books');
		if ($field === 'ISBN')
		{
			$this->db->where('ISBN', cut_isbn( $this->ISBN() ));
		}
		else
		{
			$this->db->where($field, $this->$field);
		}
		$this->db->limit(1);
		return $this->_set();
	}

	private function _set()
	{
		$query = $this->db->get();
		if ($query->num_rows == 0)
		{
			//$this->unset_all();
			return FALSE;
		}
		$book = $query->row();

		$this->ID = $book->ID;
		$this->ISBN_13 = uncut_isbn_13($book->ISBN);
		$this->ISBN_10 = uncut_isbn_10($book->ISBN);
		$this->google_id = $book->google_id;
		$this->title = $book->title;
		$this->_publisher_id = $book->publisher_id;
		$this->publication_year = $book->publication_year;
		$this->pages = $book->pages;
		$this->_language_id = $book->language_id;

		$this->publisher = $this->_select('publishers', 'ID', $this->_publisher_id)->name;
		$this->language = $this->_select('languages', 'ID', $this->_language_id)->name;
		$this->_join_authors();
		$this->_join_categories();
		return TRUE;
	}

	private function _join_authors()
	{
		$this->db->from('authors')->where('book_id', $this->ID)
			->join('links_book_author', 'authors.ID = links_book_author.author_id');
		$results = $this->db->get()->result();
		foreach ($results as $result)
		{
			$this->authors[] = $result->name;
		}
	}

	private function _join_categories()
	{
		$this->db->from('categories')->where('book_id', $this->ID)
			->join('links_book_category', 'categories.ID = links_book_category.category_id');
		$results = $this->db->get()->result();
		foreach ($results as $result)
		{
			$this->categories[] = $result->name;
		}
	}

	protected function _get_publisher()
	{
		$code = cut_isbn( $this->ISBN() );

		for($digits = 7; $digits > 3; $digits--)
		{
			$this->db->from('publisher_codes')->where('code', substr($code, 0, $digits));
			$res = $this->db->get();
			if ($res->num_rows > 0)
				return $res->row()->name;
		}
		return NULL;
	}

	protected function _get_country()
	{
		$code = cut_isbn( $this->ISBN() );
		for($digits = 1; $digits < 6; $digits++)
		{
			$this->db->from('language_groups')->where('code', substr($code, 0, $digits));
			$res = $this->db->get();
			if ($res->num_rows > 0)
				return $res->row()->name;
		}
		return NULL;
	}

}

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */  
