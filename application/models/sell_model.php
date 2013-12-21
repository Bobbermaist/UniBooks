<?php

class Sell_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function insert($user_id, $book_id, $price)
	{
		$user_id = intval($user_id);
		$book_id = intval($book_id);
		$price = str_replace(',', '.', $price);
		if( $this->get($user_id, $book_id) )
			return FALSE;
		$this->db->insert('books_for_sale', array('user_id' => $user_id, 'book_id' => $book_id, 'price' => $price));
		return TRUE;
	}

	public function get($user_id, $book_id = NULL)
	{
		if( $book_id )
		{
			$this->db->from('books_for_sale')->where(array('user_id' => $user_id, 'book_id' => $book_id));
			$query = $this->db->get();
			if( $query->num_rows == 0 )
				return FALSE;
			else
				return $query->row();
		}
		else
		{
			$this->load->model('Book_model');
			//$this->db->from('books')->where('user_id', $user_id)->join('books_for_sale', 'books_for_sale.book_id = books.ID');
			$this->db->from('books_for_sale')->where('user_id', $user_id);
			$sells = $this->db->get();
			$books = array();
			foreach($sells->result() as $sell)
			{
				$book = $this->Book_model->get($sell->book_id);
				$book['price'] = $sell->price;
				array_push($books, $book);
			}
			return $books;
		}
	}

	public function get_price($user_id, $book_id)
	{
		return $this->get($user_id, $book_id)->price;
	}
}

/* End of file sell_model.php */
/* Location: ./application/models/sell_model.php */ 