<?php

class Request_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function insert($user_id, $book_id)
	{
		$user_id = intval($user_id);
		$book_id = intval($book_id);
		if( $this->get($user_id, $book_id) )
			return FALSE;
		$this->db->insert('books_requested', array('user_id' => $user_id, 'book_id' => $book_id));
		return TRUE;
	}

	public function get($user_id, $book_id = NULL)
	{
		if( $book_id )
		{
			$this->db->from('books_requested')->where(array('user_id' => $user_id, 'book_id' => $book_id));
			$query = $this->db->get();
			if( $query->num_rows == 0 )
				return FALSE;
			else
				return $query->row();
		}
		else
		{
			$this->load->model('Book_model');
			$this->db->from('books_requested')->where('user_id', $user_id);
			$requests = $this->db->get();
			$books = array();
			foreach($requests->result() as $request)
			{
				$book = $this->Book_model->get($request->book_id);
				$book['ID'] = $request->book_id;
				array_push($books, $book);
			}
			return $books;
		}
	}

	public function delete($user_id, $book_id)
	{
		$this->db->delete('books_requested', array('user_id' => $user_id, 'book_id' => $book_id));
	}
}

/* End of file request_model.php */
/* Location: ./application/models/request_model.php */  
