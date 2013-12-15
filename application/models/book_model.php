<?php

class Book_model extends CI_Model {
	
	var $ISBN;
	var $info;

	function __construct()
	{
		parent::__construct();
	}

	public function google_fetch($data = NULL)
	{
		$this->load->library('My_books');
		$book = new My_books;
		if( isset($this->ISBN) )
			return $book->get_by_isbn($this->ISBN);
		return $book->get($data);
	}

	public function printable_array($google_data)
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
				isset($book['publishedDate']) ? $book['publishedDate'] : '',
				$this->industryID_to_ISBN($book['industryIdentifiers']),
				isset($book['pageCount']) ? $book['pageCount'] : '',
				isset($book['categories']) ? implode(', ', $book['categories']) : '',
				isset($book['language']) ? $book['language'] : ''
			));
		}
		array_walk_recursive($books_data, create_function('&$val', '$val = htmlentities($val);'));
		return $books_data;
	}

	public function set_info($google_data, $index)
	{
		if( ! isset($google_data['items'][intval($index)]) )
			exit;
		$google_data = $google_data['items'][intval($index)]['volumeInfo'];
		$this->info = array(
			'title'							=> isset($google_data['title']) ? $google_data['title'] : NULL,
			'authors'						=> isset($google_data['authors']) ? $google_data['authors'] : NULL,
			'publication_year'	=> isset($google_data['publishedDate']) ? substr($google_data['publishedDate'], 0, 4) : NULL,
			'ISBN'							=> $this->industryID_to_ISBN($google_data['industryIdentifiers']),
			'pages'							=> isset($google_data['pageCount']) ? $google_data['pageCount'] : NULL,
			'categories'				=> isset($google_data['categories']) ? $google_data['categories'] : NULL,
			'language'					=> isset($google_data['language']) ? $google_data['language'] : NULL
		);
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
