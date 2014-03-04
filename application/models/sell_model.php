<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sell_model extends Exchange_base {

	protected $price;

	protected $sells = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->_set_user_id();
	}

	public function price($value = NULL)
	{
		return ($value === NULL)
			? $this->_get('price')
			: $this->price = (float) str_replace(',', '.', $value);
	}

	public function insert()
	{
		return $this->_insert('books_for_sale', array('price'));
	}

	public function get()
	{
		$this->db->from('books_for_sale')->where('user_id', $this->user_id);
		if (($query = $this->db->get()) !== 0)
		{
			foreach ($query->result() as $row)
			{
				$this->sells[] = array(
					'book_id'	=> $row->book_id,
					'price'		=> $row->price,
				);
			}
		}
	}
}

/* End of file sell_model.php */
/* Location: ./application/models/sell_model.php */ 