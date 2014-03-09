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
 * UniBooks Sell_model Class
 *
 * @package UniBooks
 * @category Models
 * @author Emiliano Bovetti
 */
class Sell_model extends Exchange_base {

	/**
	 * @var float
	 * @access protected
	 */
	protected $price;

	/**
	 * @var array
	 * @access protected
	 */
	protected $sells = array();

	/**
	 * Constructor, loads db and sets user_id from session.
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
	 * Get / Set price
	 * If $value === NULL retrieve price, otherwiese it sets
	 *
	 * @param mixed  float or NULL
	 * @return mixed  FALSE or float
	 */
	public function get_price()
	{
		return $this->_get('price');
	}

	public function set_price($value)
	{
		$this->price = (float) str_replace(',', '.', $value);
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
		return $this->_insert('books_for_sale', array('price'));
	}

	/**
	 * Get all books for sale of a user and sets $this->sells array.
	 *
	 * @return void
	 */
	public function get()
	{
		$this->db->from('books_for_sale')->where('user_id', $this->user_id);
		if (($query = $this->db->get()) !== 0)
		{
			$this->sells = $query->result_array();
			/*
			foreach ($query->result() as $row)
			{
				$this->sells[] = array(
					'book_id'	=> $row->book_id,
					'price'		=> $row->price,
				);
			}
			*/
		}
	}

	/**
	 * Delete a row from `books_for_sale`,
	 * user_id and book_id properties must be setted.
	 *
	 * @return void
	 */
	public function delete()
	{
		$this->_delete('books_for_sale');
	}
}

/* End of file sell_model.php */
/* Location: ./application/models/sell_model.php */ 