<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sell_model extends MY_Model {

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
				$book['ID'] = $sell->book_id;
				array_push($books, $book);
			}
			return $books;
		}
	}

	public function get_price($user_id, $book_id)
	{
		return $this->get($user_id, $book_id)->price;
	}

	public function delete($user_id, $book_id)
	{
		$this->db->delete('books_for_sale', array('user_id' => $user_id, 'book_id' => $book_id));
	}
}

/* End of file sell_model.php */
/* Location: ./application/models/sell_model.php */ 