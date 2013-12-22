<?php

class Book_model extends CI_Model {
	
	private $ISBN;
	private $info;

	function __construct()
	{
		parent::__construct();
	}

	public function setISBN($str)
	{
		$str = strtoupper(preg_replace('/[^\d^X]+/i', '', $str));
		$valid = FALSE;
		$len = strlen($str);
		if( $len == 10 )
		{
			$valid = $this->validate10($str);
			if( ! $valid )
			{
				$str = '978' . $str;
				$valid = $this->validate13($str);
			}
		}
		elseif( $len == 13 )
			$valid = $this->validate13($str);
		return $valid ? (boolean) ($this->ISBN = $str) : FALSE;
	}

	public function getISBN()
	{
		return $this->ISBN;
	}

	public function issetISBN()
	{
		return isset($this->ISBN);
	}

	public function set_info($google_data, $index, $isbn = NULL)
	{
		if( ! isset($google_data['items'][intval($index)]) )
			exit;
		$google_data = $google_data['items'][intval($index)]['volumeInfo'];
		$this->setISBN($isbn);
		$this->setISBN($this->industryID_to_ISBN($google_data['industryIdentifiers']));
		$this->info = array(
			'ISBN'							=> isset($this->ISBN) ? $this->ISBN : NULL,
			'title'							=> isset($google_data['title']) ? $google_data['title'] : NULL,
			'publisher'					=> isset($google_data['publisher']) ? $google_data['publisher'] : $this->get_publisher(),
			'authors'						=> isset($google_data['authors']) ? $google_data['authors'] : NULL,
			'publication_year'	=> isset($google_data['publishedDate']) ? substr($google_data['publishedDate'], 0, 4) : NULL,
			'pages'							=> isset($google_data['pageCount']) ? $google_data['pageCount'] : NULL,
			'categories'				=> isset($google_data['categories']) ? $google_data['categories'] : NULL,
			'language'					=> isset($google_data['language']) ? $google_data['language'] : NULL
		);
	}

	public function get_info()
	{
		return $this->info;
	}

	public function google_fetch($data = NULL)
	{
		$this->load->library('MY_books');
		$book = new MY_books;
		return isset($this->ISBN) ? $book->get_by_isbn($this->ISBN) : $book->get($data);
	}

	public function insert()
	{
		if( ! isset($this->info) )
			exit;
		$this->load->database();
		$this->setISBN($this->info['ISBN']);
		if( $id = $this->get_id('books', 'ISBN', $this->cutISBN()) )
			return $id;
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
		if( $table === 'books' AND $field === 'ISBN' AND ! $value )
			return 0;
		$this->db->select('ID')->from($table)->where($field, $value)->limit(1);
		$query = $this->db->get();
		return $query->num_rows == 1 ? $query->row()->ID : 0;
	}

	public function get($id)
	{
		$this->load->database();
		$this->db->from('books')->where('ID', intval($id))->limit(1);
		$query = $this->db->get();
		if( $query->num_rows == 0 )
			return NULL;
		$book = $query->row();
		return array(
			'ISBN'							=> $this->uncutISBN($book->ISBN),
			'title'							=> $book->title,
			'publisher'					=> $this->get_by_id('publishers', 'name', $book->publisher_id),
			'authors'						=> $this->join_links('authors', 'author', $book->ID),
			'publication_year'	=> $book->publication_year,
			'pages'							=> $book->pages,
			'categories'				=> $this->join_links('categories', 'category', $book->ID),
			'language'					=> $this->get_by_id('languages', 'name', $book->language_id)
		);
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

	private function get_by_id($table, $field, $id)
	{
		$this->db->select($field)->from($table)->where('ID', $id)->limit(1);
		$query = $this->db->get();
		if( $query->num_rows == 0)
			return NULL;
		return $query->row()->$field;
	}

	private function join_links($table, $key, $book_id)
	{
		$this->db->select('name')->from($table)->where('book_id', $book_id)
					->join("links_book_$key", "links_book_$key.".$key."_id = $table.ID");
		$query = $this->db->get();
		$data = array();
		foreach ($query->result() as $row)
			array_push($data, $row->name);
		return $data;
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
				isset($book['title']) ? $book['title'] : NULL,
				isset($book['authors']) ? implode(', ', $book['authors']) : NULL,
				isset($book['publishedDate']) ? substr($book['publishedDate'], 0, 4) : NULL,
				$this->industryID_to_ISBN($book['industryIdentifiers']),
				isset($book['pageCount']) ? $book['pageCount'] : NULL,
				isset($book['categories']) ? implode(', ', $book['categories']) : NULL/*,
				isset($book['language']) ? $book['language'] : ''*/
			));
		}
		array_walk_recursive($books_data, create_function('&$val', '$val = htmlentities($val);'));
		return $books_data;
	}

	public function get_country()
	{
		if( ! isset($this->ISBN) )
			return NULL;
		$this->load->database();
		$isbn = $this->cutISBN();
		for($digits = 1; $digits < 6; $digits++)
		{
			$this->db->from('language_groups')->where('code', substr($isbn, 0, $digits));
			$res = $this->db->get();
			if( $res->num_rows > 0 )
				return $res->row()->name;
		}
		return NULL;
	}

	public function get_publisher()
	{
		if( ! isset($this->ISBN) )
			return NULL;
		$this->load->database();
		$isbn = $this->cutISBN();
		for($digits = 7; $digits > 3; $digits--)
		{
			$this->db->from('publisher_codes')->where('code', substr($isbn, 0, $digits));
			$res = $this->db->get();
			if( $res->num_rows > 0 )
				return $res->row()->name;
		}
		return NULL;
	}

	private function industryID_to_ISBN($industryIdentifiers)
	{
		$isbn10 = NULL;
		foreach($industryIdentifiers as $iid)
		{
			if( $iid['type'] === 'ISBN_13' )
			{
				$isbn13 = $iid['identifier'];
				break;
			}
			elseif( $iid['type'] === 'ISBN_10' )
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

	private function uncutISBN($code)
	{
		if( ! $code )
			return 0;
		$isbn = '978' . $code;
		$check = 0;
		for($i = 0; $i < 13; $i+=2) $check += substr($isbn, $i, 1);
		for($i = 1; $i < 12; $i+=2) $check += 3 * substr($isbn, $i, 1);
		if( $check % 10 == 0 )
			return $isbn . 0;
		return $isbn . (10 - $check % 10);
	}

	private function validate10($str)
	{
		$a = 0;
		for($i = 0; $i < 10; $i++)
		{
			if( $str[$i] == 'X' )
				$a += 10 * intval(10 - $i);
			else
				$a += intval($str[$i]) * intval(10 - $i);
		}
		return ($a % 11 == 0);
	}

	private function validate13($str)
	{
		$check = 0;
		for($i = 0; $i < 13; $i+=2) $check += substr($str, $i, 1);
		for($i = 1; $i < 12; $i+=2) $check += 3 * substr($str, $i, 1);
		return $check % 10 == 0;
	}
}

/* End of file book_model.php */
/* Location: ./application/models/book_model.php */  
