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
 * Request_model class.
 * 
 * Extends Exchange_base class and provides
 * all methods to manage requests.
 *
 * @package UniBooks
 * @category Models
 * @author Emiliano Bovetti
 */
class Request_model extends Exchange_base {

    /**
     * All user requests
     * 
     * @var array
     * @access protected
     */
    protected $requests = array();

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

    public function get_requests()
    {
        return $this->requests;
    }

    /**
     * Insert method.
     * See the Exchange_base class in ./application/core/MY_Model.php
     * for _insert method.
     *
     * @return void
     */
    public function insert()
    {
        $this->_insert('books_requested');
    }

    /**
     * Get "a page" of requests.
     * The number of requests in a page is defined in ITEMS_PER_PAGE
     * constant.
     *
     * @param int  $page_number must be > 1
     * @return void
     */
    public function get_page($page_number)
    {
        $this->requests = $this->_get_page($page_number, 'books_requested');
    }

    /**
     * Delete a row from `books_requested`,
     * user_id and book_id properties must be setted.
     *
     * @return void
     * @throws Custom_exception(REQUEST_NON_EXISTENT) if 
     *    the request does not exist
     */
    public function delete()
    {
        if ( ! $this->_delete('books_requested'))
        {
            throw new Custom_exception(REQUEST_NON_EXISTENT);
        }
    }
}

// END Request_model class

/* End of file request_model.php */
/* Location: ./application/models/request_model.php */  
