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
 * Exchange_base class.
 *
 * extended by Sell_model and Request_model
 *
 * @package UniBooks
 * @category Base Models
 * @author Emiliano Bovetti
 */
class Exchange_base extends MY_Model {

    /**
     * User id
     *
     * @var int
     * @access protected
     */
    protected $user_id;

    /**
     * Book id
     *
     * @var int
     * @access protected
     */
    protected $book_id;

    /**
     * Total sells / requests of a user
     *
     * @var int
     * @access protected
     */
    protected $total_items = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set user id
     *
     * Retrieve the user id from the session and set
     * user_id property only if is not setted.
     * 
     * @return void
     * @access protected
     */
    protected function _set_user_id()
    {
        if ( ! isset($this->user_id))
        {
            $user = new User_base;
            $user->read_session();
            $this->user_id = $user->get_ID();
        }
    }

    public function get_total_items()
    {
        return $this->total_items;
    }

    /**
     * Set total excanges from a $table.
     *
     * @param string  $table the table name
     * @return void
     */
    protected function _set_total_items($table)
    {
        $this->total_items = $this->_fetch_total_items($table, 'user_id', $this->user_id);
    }

    /**
     * Get book id
     *
     * @return int
     */
    public function get_book_id()
    {
        return $this->book_id;
    }

    /**
     * Set book_id
     * 
     * @param int  $value the book ID to set
     * @return void
     */
    public function set_book_id($value)
    {
        $this->book_id = (int) $value;
    }

    /**
     * _insert method, it inserts in $table the user_id and book_id
     * properties.
     * *Throws an exception* if exists a row with these values.
     *
     * The second parameter can be used to insert other 
     * property
     * 
     * @param string  $table the table name
     * @param string[]  $properties other object properties to insert, like 'price' (optional)
     * @return void
     * @throws Custom_exception(EXISTING_SALE) if the pair
     *    user_id - book_id exists in the 'books_for_sale' table
     * @throws Custom_exception(EXISTING_REQUEST) if the pair
     *    user_id - book_id exists int the 'books_requested' table
     * @access protected
     */
    protected function _insert($table, $properties = array())
    {
        $clause = array(
            'user_id'   => $this->user_id,
            'book_id'   => $this->book_id,
        );
        if ($this->_single_select($table, $clause) !== FALSE)
        {
            if ($table === 'books_for_sale')
            {
                throw new Custom_exception(EXISTING_SALE);
            }
            elseif ($table === 'books_requested')
            {
                throw new Custom_exception(EXISTING_REQUEST);
            }
        }

        foreach ($properties as $property)
        {
            $clause[$property] = $this->{$property};
        }
        $this->db->insert($table, $clause);
    }

    /**
     * Deletes a row from $table.
     * user_id and book_id properties must be setted
     * 
     * @param string  $table the table name
     * @return boolean
     * @access protected
     */
    protected function _delete($table)
    {
        $this->_set_user_id();
        return $this->db->delete($table, array(
            'user_id'   => $this->user_id,
            'book_id'   => $this->book_id,
        ));
    }

    /**
     * Get "a page" of exchanges (sells - requests)
     * The number of items in a page is defined in ITEMS_PER_PAGE
     * constant.
     *
     * @param int  $page_number
     * @param string  $table the table name where to get items
     * @return object  the results
     */
    protected function _get_page($page_number, $table)
    {
        $this->_set_total_items($table);
        $start_index = $this->_get_start_index($page_number, ITEMS_PER_PAGE, $this->total_items);

        $book_resource = new Book_base;

        $this->db->select($table . '.*');
        $book_resource->compose_select($this->db);
        $this->db->from($table);
        $this->db->join('books', "books.ID = {$table}.book_id");
        $book_resource->compose_join($this->db);
        $this->db->group_by('books.ID');
        $this->db->having($table . '.user_id', $this->user_id);
        $this->db->limit(ITEMS_PER_PAGE, $start_index);
        $result = $this->db->get()->result_array();

        return rebuild_codes_results($result);
    }
}

// END Exchange_base class 

/* End of file Exchange_base.php */
/* Location: ./application/core/Exchange_base.php */  