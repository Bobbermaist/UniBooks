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
 * MY_Model class.
 *
 * Extends CI_Model class and is extended
 * by other application models.
 *
 * @package UniBooks
 * @category Core
 * @author Emiliano Bovetti
 */
class MY_Model extends CI_Model {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Select one result from a table.
     *
     * Accepts three parameters, the table name,
     * a string with the field name or an associative array
     * ('field' => 'value').
     * If the second parameter is a string, the third accepts
     * the value.
     * 
     * @param string  $table is the table name
     * @param string|string[]  $where is a string or an associative array
     * @param string|null  $value NULL if $where is an array, string otherwise (optional)
     * @return mixed  a row object on success or FALSE
     */
    protected function _single_select($table, $where, $value = NULL)
    {
        $this->db->from($table)->where($where, $value)->limit(1);
        $query = $this->db->get();
        return $query->num_rows == 1 ? $query->row() : FALSE;
    }

    /**
     * Perform an insert query with 'on duplicate key update'
     * clause.
     *
     * The first parameter contains a tring with the table name,
     * the second one an associative array with th values.
     *
     * E.g.
     * <pre>
     *  $table = 'users';
     *  $data = array(
     *    'id' => 1,
     *    'name' => 'test',
     *  );
     *  $this->_insert_on_duplicate($table, $data);
     * </pre>
     *
     * produces the following query:
     *
     * <pre>
     *  INSERT INTO `users` (id, name) VALUES ('1', 'test')
     *  ON DUPLICATE KEY UPDATE id='1', name='test'
     * </pre>
     *
     * @param string  $table is the table name
     * @param array  $data the associative array to insert
     * @return void
     */
    protected function _insert_on_duplicate($table, $data)
    {
        $sql = $this->db->insert_string($table, $data) . ' ON DUPLICATE KEY UPDATE ';

        while (current($data) !== FALSE)
        {
            $sql .= key($data) . "='" . current($data) . "'";

            if (next($data) !== FALSE)
            {
                $sql .= ', ';
            }
        }
        $this->db->query($sql);
    }

    /**
     * Get the number of items from a table.
     *
     * If second and third parameters are FALSE
     * retrieves all items from $table, otherwise
     * counts all items having a $value in $field_name
     *
     * @param string  $table the table name
     * @param string|false  $field_name restricts results on this field
     * @param string|false  $value the value of the field
     * @return int
     * @access protected
     */
    protected function _fetch_total_items($table, $field_name = FALSE, $value = FALSE)
    {
        if ($field_name === FALSE)
        {
            return (int) $this->db->count_all($table);
        }
        $this->db->from($table)->where($field_name, $value);
        return (int) $this->db->count_all_results();
    }

    /**
     * Calculates the starting index to retrieve items
     * from db.
     *
     * If $total_items parameter is not FALSE calculates
     * if there are enugh items
     *
     * @param int  $page_number number of the page
     * @param int  $items_per_page how many items shown per page
     * @param int|false  $total_items FALSE if don't want to control
     * @return int
     * @throws Custom_exception(REQUEST_UNDERFLOW) if $page_number < 1
     * @throws Custom_exception(REQUEST_OVERFLOW) if
     *    $total_items parameter was setted and $start_index >= $total_items
     */
    protected function _get_start_index($page_number, $items_per_page, $total_items = FALSE)
    {
        if ($page_number < 1)
        {
            throw new Custom_exception(REQUEST_UNDERFLOW);
        }
        $start_index = ($page_number - 1) * $items_per_page;

        if ($total_items !== FALSE AND $start_index >= $total_items)
        {
            throw new Custom_exception(REQUEST_OVERFLOW);
        }
        return $start_index;
    }
}

// END MY_Model class

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */  
