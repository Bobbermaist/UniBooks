<?php

class Book_model extends CI_Model {
	
	var $ISBN;

	function __construct()
	{
		parent::__construct();
	}

	public function setISBN($isbn)
	{
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
		if( $len == 10 && validate10($this->ISBN) )
			return TRUE;
		elseif( $len == 10 )
		{
			$this->ISBN = "978$this->ISBN";
			return validate13($this->ISBN);
		}
		else
			return validate13($this->ISBN);
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

	public function google_fetch($data = NULL)
	{
		$this->load->library('My_books');
		$book = new My_books;
		if( isset($this->ISBN) )
			return $book->get_by_isbn($this->ISBN);
		else
			return $book->get($data);
	}

}

/* End of file book_model.php */
/* Location: ./application/models/book_model.php */  
