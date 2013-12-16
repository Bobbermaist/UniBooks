<?php

class Book_model extends CI_Model {
	
	private $ISBN;
	private $info;

	function __construct()
	{
		parent::__construct();
	}

	public function setISBN($isbn)
	{
		/*
		if( isset($this->ISBN) )
			return TRUE;
		*/
		$this->ISBN = strtoupper(preg_replace('/[^\d^X]+/i', '', $isbn));
		if( $this->validate() )
			return TRUE;
		unset( $this->ISBN );
		return FALSE;
	}

	public function getISBN()
	{
		return $this->ISBN;
	}

	public function issetISBN()
	{
		return isset($this->ISBN);
	}

	public function set_info($google_data, $index)
	{
		if( ! isset($google_data['items'][intval($index)]) )
			exit;
		$google_data = $google_data['items'][intval($index)]['volumeInfo'];
		$this->info = array(
			'ISBN'							=> $this->industryID_to_ISBN($google_data['industryIdentifiers']),
			'title'							=> isset($google_data['title']) ? $google_data['title'] : NULL,
			'publisher'					=> isset($google_data['publisher']) ? $google_data['publisher'] : NULL,
			'authors'						=> isset($google_data['authors']) ? $google_data['authors'] : NULL,
			'publication_year'	=> isset($google_data['publishedDate']) ? substr($google_data['publishedDate'], 0, 4) : NULL,
			'pages'							=> isset($google_data['pageCount']) ? $google_data['pageCount'] : NULL,
			'categories'				=> isset($google_data['categories']) ? $google_data['categories'] : NULL,
			'language'					=> isset($google_data['language']) ? $google_data['language'] : NULL
		);
	}

	public function google_fetch($data = NULL)
	{
		$this->load->library('My_books');
		$book = new My_books;
		if( isset($this->ISBN) )
			return $book->get_by_isbn($this->ISBN);
		return $book->get($data);
	}

	public function insert_book($isbn = NULL)
	{
		if( ! isset($this->info) )
			exit;
		$this->setISBN($this->info['ISBN']);
		if( ! isset($this->ISBN) AND $isbn )
			$this->setISBN($isbn);
		if( $id = $this->get_id('books', 'ISBN', $this->cutISBN()) )
			return $id;
		$this->load->database();
		$language_id = $this->insert_info('languages', $this->info['language']);
		$categories_id = $this->insert_info('categories', $this->info['categories']);
		$publisher_id = $this->insert_info('publishers', $this->info['publisher']);
		$authors_id = $this->insert_info('authors', $this->info['authors']);

		$data = array(
			'ISBN'							=> $this->cutISBN(),
			'title'							=> $this->info['title'],
			'publisher_id'			=> $publisher_id,
			'publication_year'	=> $this->info['publication_year'],
			'pages'							=> $this->info['pages'],
			'language_id'				=> $language_id
		);
		$this->db->insert('books', $data);
		$book_id = $this->db->insert_id();
		$this->create_links('links_book_author', 'author_id', $book_id, $authors_id);
		$this->create_links('links_book_category', 'category_id', $book_id, $categories_id);
		return $book_id;
	}

	public function get_id($table, $field, $value)
	{
		$this->db->select('ID')->from($table)->where($field, $value)->limit(1);
		$query = $this->db->get();
		if( $query->num_rows == 1 )
			return $query->row()->ID;
		return FALSE;
	}

	private function insert_info($table, $value)
	{
		if( ! $value )
			return $this->insert_info($table, 'Unknown');
		if( ! is_array($value) )
		{
			if( $id = $this->get_id($table, 'name', $value) )
				return $id;
			$this->db->insert($table, array('name' => $value));
			return $this->db->insert_id();
		}
		$ids = array();
		foreach ($value as $each)
			array_push($ids, $this->insert_info($table, $each));
		return $ids;
	}

	private function create_links($table, $field, $book_id, $ids)
	{
		if( ! is_array($ids) )
			$this->db->insert($table, array('book_id' => $book_id, $field => $ids));
		else
			foreach ($ids as $id)
				$this->create_links($table, $field, $book_id, $id);
	}

	public function gdata_to_table($google_data)
	{
		if( $google_data['totalItems'] == 0 )
			return NULL;
		$books_data = array();
		foreach( $google_data['items'] as $book )
		{
			$book = $book['volumeInfo'];
			array_push($books_data, array(
				isset($book['title']) ? $book['title'] : '',
				isset($book['authors']) ? implode(', ', $book['authors']) : '',
				isset($book['publishedDate']) ? substr($book['publishedDate'], 0, 4) : '',
				$this->industryID_to_ISBN($book['industryIdentifiers']),
				isset($book['pageCount']) ? $book['pageCount'] : '',
				isset($book['categories']) ? implode(', ', $book['categories']) : ''/*,
				isset($book['language']) ? $book['language'] : ''*/
			));
		}
		array_walk_recursive($books_data, create_function('&$val', '$val = htmlentities($val);'));
		return $books_data;
	}

	private function industryID_to_ISBN($industryIdentifiers)
	{
		$isbn10 = NULL;
		foreach ($industryIdentifiers as $iid)
		{
			if( strcmp($iid['type'], 'ISBN_13') == 0 )
			{
				$isbn13 = $iid['identifier'];
				break;
			}
			elseif( strcmp($iid['type'], 'ISBN_10') == 0 )
				$isbn10 = $iid['identifier'];
		}
		return isset($isbn13) ? $isbn13 : $isbn10;
	}

	private function cutISBN()
	{
		if( ! isset($this->ISBN) )
			return 0;
		if( strlen($this->ISBN) == 13 )
			return substr($this->ISBN, 3, -1);
		return substr($this->ISBN, 0, -1);
	}

	private function uncutISBN($isbn)
	{
		$this->ISBN = '978' . $isbn;
		$check = 0;
    for($i = 0; $i < 13; $i+=2) $check += substr($isbn, $i, 1);
    for($i = 1; $i < 12; $i+=2) $check += 3 * substr($isbn, $i, 1);
    $this->ISBN .= 10 - ($check % 10);
	}

	private function validate()
	{
		$len = strlen($this->ISBN);
		if( $len != 13 && $len != 10 )
			return FALSE;
		if( $len == 10 && $this->validate10($this->ISBN) )
			return TRUE;
		elseif( $len == 10 )
		{
			$this->ISBN = "978$this->ISBN";
			return $this->validate13($this->ISBN);
		}
		else
			return $this->validate13($this->ISBN);
	}

	private function validate10($ISBN10)
	{
		$a = 0;
		for($i = 0; $i < 10; $i++)
		{
			if( $ISBN10[$i] == 'X' )
				$a += 10 * intval(10 - $i);
			else
				$a += intval($ISBN10[$i]) * intval(10 - $i);
		}
		return ($a % 11 == 0);
	}

	private function validate13($ISBN13)
	{
    $check = 0;
    for($i = 0; $i < 13; $i+=2) $check += substr($ISBN13, $i, 1);
    for($i = 1; $i < 12; $i+=2) $check += 3 * substr($ISBN13, $i, 1);
    return $check % 10 == 0;
	}
}

/* End of file book_model.php */
/* Location: ./application/models/book_model.php */  
