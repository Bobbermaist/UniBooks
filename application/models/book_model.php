<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Book_model extends CI_Model {
	
	private $ISBN;
	private $info;

	var $results;
	var $total_items;

	function __construct()
	{
		parent::__construct();
	}

	public function setISBN($str)
	{
		$str = strtoupper(preg_replace('/[^\d^X]+/i', '', $str));

		$valid = $this->validate13($str);
		$valid = ($valid) ? TRUE : $this->validate10($str);
		if ( ! $valid)
		{
			$str = '978' . $str;
			$valid = $this->validate13($str);
		}
		return $valid ? (boolean) ($this->ISBN = $str) : FALSE;
	}

	public function ISBN()
	{
		return isset($this->ISBN) ? $this->ISBN : FALSE;
	}

	public function info()
	{
		return isset($this->info) ? $this->info : FALSE;
	}

	public function results()
	{
		return (isset($this->results) AND $this->results) ? $this->results : FALSE;
	}

	public function set_info($index)
	{
		if ( ! isset($this->results[$index]))
			return FALSE;
		$this->info = $this->results[$index];
	}

	public function google_fetch($data = NULL, $page = 1)
	{
		if ( ! $this->ISBN() AND ! $data)
			return NULL;
		$this->load->library('MY_books');
		$index = $page * MAX_RESULTS - MAX_RESULTS;
		$book = new MY_books;
		$this->ISBN() ? $book->get_by_isbn($this->ISBN) : $book->get($data, $index);
		$this->results = $book->volumes;
		$this->total_items = $book->total_items;
	}

	public function books_to_table()
	{
		if ( ! $this->results())
			return NULL;
		$table = array();
		foreach( $this->results as $book )
		{
			array_push($table, array(
				$book['title'],
				($book['authors'] !== NULL) ? implode(', ', $book['authors']) : NULL,
				$book['publication_year'],
				$book['ISBN'],
				$book['pages'],
				($book['categories'] !== NULL) ? implode(', ', $book['categories']) : NULL,
				'ID' => $book['ID'],
			));
		}
		array_walk_recursive($table, create_function('&$val', '$val = htmlentities($val);'));
		return $table;
	}

	public function insert()
	{
			/* Nothing to insert */
		if ( ! $this->info() AND ! $this->results())
			return FALSE;

		$this->load->database();
			/* insert all results */
		if ( ! $this->info())
		{
			foreach ($this->results as $index => $book)
			{
				$this->set_info($index);
				$this->results[$index]['ID'] = $this->insert();
			}
			unset($this->info);
			return TRUE;
		}

		if ($book_id = $this->exists())
			return $book_id;

		$language_id = $this->insert_info('languages', $this->info['language']);
		$categories_id = $this->insert_info('categories', $this->info['categories']);
		$publisher_id = $this->insert_info('publishers', $this->info['publisher']);
		$authors_id = $this->insert_info('authors', $this->info['authors']);

		$data = array(
			'ISBN'							=> $this->cutISBN($this->info['ISBN']),
			'google_id'					=> $this->info['google_id'],
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

	private function exists()
	{
		if ( ! $this->info())
			return FALSE;

		if ($this->info['google_id'] !== NULL)
			return $this->get_id('books', 'google_id', $this->info['google_id']);
		if ($this->info['ISBN'] !== NULL)
			return $this->get_id('books', 'ISBN', $this->cutISBN($this->info['ISBN']));
		return FALSE;
	}

	public function get_id($table, $field, $value)
	{
		$this->db->select('ID')->from($table)->where($field, $value)->limit(1);
		$query = $this->db->get();
		return $query->num_rows == 1 ? $query->row()->ID : FALSE;
	}

	public function get($id)
	{
		$this->load->database();
		$this->db->from('books')->where('ID', intval($id))->limit(1);
		$query = $this->db->get();
		if ($query->num_rows == 0)
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

	public function get_book($search = NULL)
	{
		$this->load->database();
		if ($this->ISBN())
			$book_id = $this->get_id('books', 'ISBN', $this->cutISBN($this->ISBN()));
		//echo $this->cutISBN();
		return $this->get($book_id);
	}

	private function insert_info($table, $value)
	{
		if ( ! $value)
			return $this->insert_info($table, 'Unknown');
		if ( ! is_array($value))
		{
			if ($id = $this->get_id($table, 'name', $value))
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
		if ( ! is_array($ids))
			$this->db->insert($table, array('book_id' => $book_id, $field => $ids));
		else
			foreach ($ids as $id)
				$this->create_links($table, $field, $book_id, $id);
	}

	private function get_by_id($table, $field, $id)
	{
		$this->db->select($field)->from($table)->where('ID', $id)->limit(1);
		$query = $this->db->get();
		if ($query->num_rows == 0)
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

	public function get_country()
	{
		if ( ! $this->ISBN())
			return NULL;
		$this->load->database();
		$isbn = $this->cutISBN($this->ISBN());
		for($digits = 1; $digits < 6; $digits++)
		{
			$this->db->from('language_groups')->where('code', substr($isbn, 0, $digits));
			$res = $this->db->get();
			if ($res->num_rows > 0)
				return $res->row()->name;
		}
		return NULL;
	}

	private function cutISBN($isbn)
	{
		if ( ! $isbn)
			return 0;
		return (strlen($isbn) == 13) ? substr($isbn, 3, -1) : substr($isbn, 0, -1);
	}

	private function uncutISBN($code)
	{
		if ( ! $code)
			return 0;
		$isbn = '978' . $code;
		$check = 0;
		for($i = 0; $i < 13; $i+=2) $check += substr($isbn, $i, 1);
		for($i = 1; $i < 12; $i+=2) $check += 3 * substr($isbn, $i, 1);
		if ($check % 10 == 0)
			return $isbn . 0;
		return $isbn . (10 - $check % 10);
	}

	private function validate10($str)
	{
		if (strlen($str) != 10)
			return FALSE;
		$a = 0;
		for($i = 0; $i < 10; $i++)
		{
			if ($str[$i] == 'X')
				$a += 10 * intval(10 - $i);
			else
				$a += intval($str[$i]) * intval(10 - $i);
		}
		return ($a % 11 == 0);
	}

	private function validate13($str)
	{
		if (strlen($str) != 13)
			return FALSE;
		$check = 0;
		for($i = 0; $i < 13; $i+=2) $check += substr($str, $i, 1);
		for($i = 1; $i < 12; $i+=2) $check += 3 * substr($str, $i, 1);
		return $check % 10 == 0;
	}
}

/* End of file book_model.php */
/* Location: ./application/models/book_model.php */  
