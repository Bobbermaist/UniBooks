<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request_model extends Exchange_base {

	protected $requests = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->_set_user_id();
	}

	public function insert()
	{
		return $this->_insert('books_for_sale');
	}

	public function get()
	{
		$this->db->from('books_requested')->where('user_id', $this->ID);
		if (($query = $this->db->get()) !== 0)
		{
			foreach ($query->result() as $row)
			{
				$this->sells[] = array(
					'book_id'	=> $row->book_id,
				);
			}
		}
	}
}

/* End of file request_model.php */
/* Location: ./application/models/request_model.php */  
