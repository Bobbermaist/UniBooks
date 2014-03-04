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
 * UniBooks Rquest_model Class
 *
 * @package UniBooks
 * @category Models
 * @author Emiliano Bovetti
 */
class Request_model extends Exchange_base {

	/**
	 * @var array
	 * @access protected
	 */
	protected $requests = array();

	/**
	 * Constructor, loads db and sets user_id by the session.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->_set_user_id();
	}

	/**
	 * Insert method.
	 * See the Exchange_base class in ./application/core/MY_Model.php
	 * for _insert method.
	 *
	 * @return boolean
	 */
	public function insert()
	{
		return $this->_insert('books_for_sale');
	}

	/**
	 * Get all books requested by a user and sets $this->requests array.
	 *
	 * @return void
	 */
	public function get()
	{
		$this->db->from('books_requested')->where('user_id', $this->ID);
		if (($query = $this->db->get()) !== 0)
		{
			$this->requests = $query->result_array();
			/*
			foreach ($query->result() as $row)
			{
				$this->requests[] = array(
					'book_id'	=> $row->book_id,
				);
			}
			*/
		}
	}

	/**
	 * Delete a row from `books_requested`,
	 * user_id and book_id properties must be setted.
	 *
	 * @return void
	 */
	public function delete()
	{
		$this->_delete('books_requested');
	}
}

/* End of file request_model.php */
/* Location: ./application/models/request_model.php */  
