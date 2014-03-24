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
 * UniBooks MY_Model class.
 *
 * Extends CI_Model class and is extended
 * by other application models.
 *
 * @package UniBooks
 * @category Models
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

/**
 * UniBooks User_base class.
 *
 * Is extended by User_model class and
 * contains all user properties and
 * base method to manage an user.
 *
 * @package UniBooks
 * @category Base Models
 * @author Emiliano Bovetti
 */
class User_base extends MY_Model {

    /**
     * User ID.
     *
     * @var int
     * @access protected
     */
    protected $ID;

    /**
     * User name.
     *
     * @var string
     * @access protected
     */
    protected $user_name;

    /**
     * Hashed password
     *
     * @var string
     * @access protected
     */
    protected $password;

    /**
     * User email
     *
     * @var string
     * @access protected
     */
    protected $email;

    /**
     * Timestamp
     *
     * @var string
     * @access protected
     */
    protected $registration_time;

    /**
     * User rights
     *
     * @var int
     * @access protected
     */
    protected $rights;

    /**
     * Random string to activate / reset account settings
     *
     * @var string
     * @access protected
     */
    protected $confirm_code;

    /**
     * Email address not confirmed
     *
     * @var string
     * @access protected
     */
    protected $tmp_email;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get ID method.
     *
     * @return int
     */
    public function get_ID()
    {
        return $this->ID;
    }

    /**
     * Set ID method.
     *
     * Sets the ID property with the $value parameter
     * and then retrieve other properties from `users` table.
     *
     * If not exists the select_by method will *throw an exception*
     * 
     * @param int  $value the ID value to set
     * @return void
     * @see select_by method for exceptions thrown
     */
    public function set_ID($value)
    {
        $this->ID = (int) $value;
        $this->select_by('ID');
    }

    /**
     * Get user name.
     *
     * @return string
     */
    public function get_user_name()
    {
        return $this->user_name;
    }

    /**
     * Set user name.
     *
     * Trim and sets user_name property.
     *
     * @param string  $value the user name to set
     * @return void
     */
    public function set_user_name($value)
    {
        $this->user_name = trim($value);
    }

    /**
     * Get password.
     *
     * NOTE: The password property is the *hashed* password.
     *
     * @return string
     */
    public function get_password()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * Hash and sets password.
     *
     * @param string  $value the password (not hashed) to set
     * @return void
     */
    public function set_password($value)
    {
        $this->load->helper('security');
        $this->password = do_hash($value);
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * Sets email with $value to lower case and trim.
     *
     * @param string  $value the email address to set
     * @return void
     */
    public function set_email($value)
    {
        $this->email = utf8_strtolower(trim($value));
    }

    /**
     * Get registration time.
     *
     * @return string
     */
    public function get_registration_time()
    {
        return $this->registration_time;
    }

    /**
     * Get rights.
     *
     * @return int
     */
    public function get_rights()
    {
        return $this->rights;
    }

    /**
     * Get confirm_code
     *
     * @return string
     */
    public function get_confirm_code()
    {
        return $this->confirm_code;
    }

    /**
     * Get temporary email.
     *
     * @return string
     */
    public function get_tmp_email()
    {
        return $this->tmp_email;
    }

    /**
     * Set temporary email.
     *
     * @param string  $value the email to confirm
     * @return void
     */
    public function set_tmp_email($value)
    {
        $this->email = utf8_strtolower(trim($value));
    }

    /**
     * Unset all object properties
     *
     * @return void
     */
    public function unset_all()
    {
        unset(
            $this->ID,
            $this->user_name,
            $this->password,
            $this->email,
            $this->registration_time,
            $this->rights,
            $this->confirm_code,
            $this->tmp_email
        );
    }

    /**
     * Set confirm code.
     * Generates a random string with the CI string helper
     *
     * @return void
     * @access protected
     */
    protected function _set_confirm_code()
    {
        $this->load->helper('string');
        $this->confirm_code = random_string('alnum', 15);
    }

    /**
     * Sets registration_time with $_SERVER['REQUEST_TIME']
     *
     * @return void
     * @access protected
     */
    protected function _set_time()
    {
        $this->registration_time = date(
            $this->config->item('log_date_format'), 
            $_SERVER['REQUEST_TIME']
        );
    }

    /**
     * Select all user fields from a property indicated 
     * in $field (default 'ID')
     *
     * The field indicated must be a unique value
     * (ID, user_name, email) and corresponding object 
     * property should be setted.
     *
     * Throws an exeption on failure.
     *
     * @param string  $field the field name
     * @return void
     * @throws Custom_exception(REQUIRED_PROPERTY if
     *    $this->{$field} is not setted
     * @throws Custom_exception(ID_NON_EXISTENT) if the
     *    ID does not exists
     * @throws Custom_exception(USER_NAME_NON_EXISTENT) if the
     *    user name does not exists
     * @throws Custom_exception(EMAIL_NON_EXISTENT) if the
     *    email address dows not exists
     * @throws Custom_exception(INVALID_PARAMETER) if
     *    the parameter provided does not match with valid ones
     */
    public function select_by($field = 'ID')
    {
        if ( ! isset($this->{$field}))
        {
            throw new Custom_exception(REQUIRED_PROPERTY, $field);
        }

        $this->db->from('users')->where($field, $this->{$field});
        $res = $this->db->get();

        if ($res->num_rows() === 0)
        {
            switch ($field)
            {
                case 'ID':
                    throw new Custom_exception(ID_NON_EXISTENT);
                    break;
                case 'user_name':
                    throw new Custom_exception(USER_NAME_NON_EXISTENT);
                    break;
                case 'email':
                    throw new Custom_exception(EMAIL_NON_EXISTENT);
                    break;
                default:
                    throw new Custom_exception(INVALID_PARAMETER);
                    break;
            }
        }

        $user_data = $res->row();
        $this->ID = (int) $user_data->ID;
        $this->user_name = $user_data->user_name;
        $this->password = $user_data->password;
        $this->email = $user_data->email;
        $this->registration_time = $user_data->registration_time;
        $this->rights = (int) $user_data->rights;
    }

    /**
     * Retrieves all object properties from the session data
     *
     * @return boolean
     */
    public function read_session()
    {
        $this->load->library('session');
        if ( ! isset($this->ID))
        {
            $userdata_id = $this->session->userdata('user_id');

            if ($userdata_id === FALSE)
            {
                return FALSE;
            }
            $this->set_id($userdata_id);
        }
        // ID property is setted, return TRUE
        return TRUE;
    }
}

// END User_base class

/**
 * UniBooks Book_base class.
 *
 * Is extended by Book model class and contains
 * all properties and base method to manage a book.
 *
 * @package UniBooks
 * @category Base Models
 * @author Emiliano Bovetti
 */
class Book_base extends MY_Model {

    /**
     * Book ID
     *
     * @var int
     * @access protected
     */
    protected $ID;

    /**
     * Thirteen-digit ISBN
     *
     * @var string
     * @access protected
     */
    protected $ISBN_13;

    /**
     * Ten-digit ISBN
     *
     * @var string
     * @access protected
     */
    protected $ISBN_10;

    /**
     * Google id
     *
     * @var string
     * @access protected
     */
    protected $google_id;

    /**
     * Book's title
     *
     * @var string
     * @access protected
     */
    protected $title;

    /**
     * Book's authors
     *
     * @var string
     * @access protected
     */
    protected $authors;

    /**
     * Book's publisher
     *
     * @var string
     * @access protected
     */
    protected $publisher;

    /**
     * Book's publication year
     *
     * @var int
     * @access protected
     */
    protected $publication_year;

    /**
     * The book's page count
     *
     * @var int
     * @access protected
     */
    protected $pages;

    /**
     * Book's language
     *
     * @var string
     * @access protected
     */
    protected $language;

    /**
     * Book's categories
     *
     * @var string
     * @access protected
     */
    protected $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('isbn');
    }

    /**
     * Unset all object properties
     *
     * @return void
     */
    public function unset_all()
    {
        unset(
            $this->ID,
            $this->ISBN_13,
            $this->ISBN_10,
            $this->google_id,
            $this->title,
            $this->authors,
            $this->publisher,
            $this->publication_year,
            $this->pages,
            $this->language,
            $this->categories
        );
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function get_ID()
    {
        return $this->ID;
    }

    /**
     * Set ID
     *
     * Sets the ID property and try to set all properties
     * from `books` db.
     * Throws an exception on failure.
     *
     * @param int  $value the ID to set
     * @return void
     */
    public function set_id($value)
    {
        $this->ID = (int) $value;
        $this->select_by('ID');
    }
    
    /**
     * Get ISBN
     * 
     * Return a 13-digit ISBN if setted, 10-digit if not,
     * FALSE if neither are setted.
     * 
     * @return string|false
     */
    public function get_isbn()
    {
        if (isset($this->ISBN_13))
        {
            return $this->ISBN_13;
        }
        
        if (isset($this->ISBN_10))
        {
            return $this->ISBN_10;
        }

        return FALSE;
    }

    /**
     * Tries to validate and set the ISBN.
     *
     * If $value is a valid 13-digit ISBN it sets ISBN_13 property,
     * if it is a valid 10-digit ISBN sets ISBN_10.
     *
     * *Throws an exception* if the ISBN code provided is not valid
     * 
     * @param string  $value the ISBN code to set
     * @return void
     * @throws Custom_exception(WRONG_ISBN) if can't set
     *    the ISBN code
     */
    public function set_isbn($value)
    {
        $isbn = strtoupper(trim($value));

        if (validate_isbn_10($value) === TRUE)
        {
            $this->ISBN_10 = $isbn;
        }
        elseif (validate_isbn_13($value) === TRUE)
        {
            $this->ISBN_13 = $isbn;
        }
        elseif (validate_isbn_13('978' . $value) === TRUE)
        {
            // did you forget the '978' prefix?
            $this->ISBN_13 = '978' . $value;
        }

        if ($this->get_isbn() === FALSE)
        {
            throw new Custom_exception(WRONG_ISBN);
        }
    }

    /**
     * Throws an exception if no ISBN code is setted
     *
     * @return void
     * @throws Custom_exception(REQUIRED_PROPERTY)
     * @access protected
     */
    protected function _required_isbn()
    {
        if ($this->get_isbn() === FALSE)
        {
            throw new Custom_exception(REQUIRED_PROPERTY, 'ISBN');
        }
    }

    /**
     * Insert a book
     * 
     * @return void
     */
    public function insert($book_data)
    {
        if ($book_data['ISBN_13'] !== NULL)
        {
            $this->set_isbn($book_data['ISBN_13']);
        }
        if ($book_data['ISBN_10'] !== NULL)
        {
            $this->set_isbn($book_data['ISBN_10']);
        }
        
        $this->_required_isbn();

        $this->load->database();

        $this->db->insert('books', array(
            'ISBN'                          => cut_isbn( $this->get_isbn() ),
            'google_id'                 => $book_data['google_id'],
            'title'                         => $book_data['title'],
            'publisher_id'          => $this->_insert_info('publishers', $book_data['publisher']),
            'publication_year'  => $book_data['publication_year'],
            'pages'                         => $book_data['pages'],
            'language_id'               => $this->_insert_info('languages', $book_data['language']),
        ));
        $this->ID = $this->db->insert_id();

        $this->_create_links(
            $this->_insert_info('authors', $book_data['authors']),
            'author_id',
            'links_book_author'
        );
        $this->_create_links(
            $this->_insert_info('categories', $book_data['categories']),
            'category_id',
            'links_book_category'
        );
    }

    /**
     * Insert a value in a table (if it already exists get its id)
     * and return the value's id.
     *
     * The value can be an array, in that case an array of
     * ID is returned.
     * 
     * @param string  $table the table name
     * @param mixed  $value string or array of data to insert
     * @return mixed  int if $value is a string or int[] if $value is an array
     * @access private
     */
    private function _insert_info($table, $value)
    {
        if (empty($value) AND $value !== NULL)
        {
            return $this->_insert_info($table, NULL);
        }

        if ( ! is_array($value))
        {
            $result = $this->_single_select($table, 'name', $value);
            if ($result !== FALSE)
            {
                return $result->ID;
            }
            $this->db->insert($table, array('name' => $value));
            return $this->db->insert_id();
        }

        $ids = array();
        foreach ($value as $each)
        {
            $ids[] = $this->_insert_info($table, $each);
        }
        return $ids;
    }

    /**
     * Create book links (authors and categories).
     *
     * @param int[]  $id_array array of IDs
     * @param string  $field_name name of the field that
     *    contains this value
     * @param string  $table the table name where insert
     * @return void
     * @throws Custom_exception(REQUIRED_PROPERTY) if 
     *    $this->{$property_name} is not setted
     * @access private
     */
    private function _create_links($id_array, $field_name, $table)
    {
        if ( ! is_array($id_array))
        {
            $id_array = array($id_array);
        }

        $data = array();
        foreach ($id_array as $id_item)
        {
            $data[] = array(
                'book_id'       => $this->ID,
                $field_name => $id_item,
            );
        }
        $this->db->insert_batch($table, $data);
    }

    /**
     * Select a book by a given field.
     *
     * This field must be unique
     * (ID, 13 or 10 digit ISBN, google_id)
     * and corresponding property must be setted.
     *
     * On success sets all properties, *throws an exception*
     * on failure.
     * 
     * @param string  $field the field name
     * @throws Custom_exception(REQUIRED_PROPERTY) if
     *    $this->{$field} is not setted
     * @return void
     */
    public function select_by($field)
    {
        $this->load->database();
        $this->db->from('books');
        if ($field === 'ISBN')
        {
            $this->_required_isbn();
            
            $this->db->where('ISBN', cut_isbn( $this->get_isbn() ));
        }
        else
        {
            if ( ! isset($this->{$field}))
            {
                throw new Custom_exception(REQUIRED_PROPERTY, $field);
            }

            $this->db->where("books.{$field}", $this->{$field});
        }
        $this->_run_extract($field);
    }

    /**
     * Set all object properties from db.
     * The 'where' clause must be composed previously.
     *
     * *Throws an exception* if the query does not 
     * procudes any result.
     * 
     * @return void
     * @param string  $field the field name
     * @throws Custom_exception(ID_NON_EXISTENT)
     *    if the ID does not exists
     * @throws Custom_exception(ISBN_NON_EXISTENT)
     *    if the ISBN code does not exists in local db
     * @throws Custom_exception(GOOGLE_ID_NON_EXISTENT)
     *    if the google ID does not exists
     * @throws Custom_exception(INVALID_PARAMETER)
     *    if the profided field does not match with any valid field
     * @access private
     */
    private function _run_extract($field)
    {
        $this->compose_select($this->db);
        $this->compose_join($this->db);
        $this->db->limit(1);

        $book = rebuild_codes_row( $this->db->get()->row() );
        if (empty($book->ID))
        {
            switch ($field)
            {
                case 'ID':
                    throw new Custom_exception(ID_NON_EXISTENT);
                    break;
                case 'ISBN':
                    throw new Custom_exception(ISBN_NON_EXISTENT);
                    break;
                case 'google_id':
                    throw new Custom_exception(GOOGLE_ID_NON_EXISTENT);
                    break;
                default:
                    throw new Custom_exception(INVALID_PARAMETER);
                    break;
            }
        }

        $this->ID = (int) $book->ID;
        $this->ISBN_13 = $book->ISBN_13;
        $this->ISBN_10 = $book->ISBN_10;
        $this->google_id = $book->google_id;
        $this->title = $book->title;
        $this->publication_year = (int) $book->publication_year;
        $this->pages = (int) $book->pages;

        $this->publisher = $book->publisher;
        $this->language = $book->language;

        $this->authors = $book->authors;
        $this->categories = $book->categories;
    }

    /**
     * Compose the 'select' part of the query
     * to extract a book.
     * 
     * Takes in input the CI db resource ($this->db)
     *
     * @param object  $db_resource $this->db
     * @return void
     */
    public function compose_select($db_resource)
    {
        $db_resource->select('
            books.ID,
            books.ISBN,
            books.google_id,
            books.title,
            books.publication_year,
            books.pages,
            publishers.name AS publisher,
            languages.name AS language,
            GROUP_CONCAT(DISTINCT authors.name SEPARATOR ", ") AS authors,
            GROUP_CONCAT(DISTINCT categories.name SEPARATOR ", ") AS categories
        ', FALSE);
    }

    /**
     * Compose the 'join' part of the query
     * to extract a book.
     * 
     * Takes in input the CI db resource ($this->db)
     *
     * @param object  $db_resource $this->db
     * @return void
     */
    public function compose_join($db_resource)
    {
        $db_resource
            ->join('links_book_author', 'books.ID = links_book_author.book_id')
            ->join('authors', 'authors.ID = links_book_author.author_id')
            ->join('links_book_category', 'books.ID = links_book_category.book_id')
            ->join('categories', 'categories.ID = links_book_category.category_id')
            ->join('publishers', 'publishers.ID = books.publisher_id')
            ->join('languages', 'languages.ID = books.language_id');
    }

    /**
     * Get publisher's name from the table `publisher_codes`
     * through ISBN.
     * ISBN_13 or ISBN_10 must be setted.
     * 
     * @return string|null
     * @access protected
     */
    protected function _get_publisher()
    {
        $code = cut_isbn( $this->get_isbn() );

        for($digits = 7; $digits > 3; $digits--)
        {
            $this->db->from('publisher_codes')->where('code', substr($code, 0, $digits));
            $res = $this->db->get();
            if ($res->num_rows > 0)
                return $res->row()->name;
        }
        return NULL;
    }

    /**
     * Get country name from the table `language_groups`
     * through ISBN.
     * ISBN_13 or ISBN_10 must be setted.
     * 
     * @return string|null
     * @access protected
     */
    protected function _get_country()
    {
        $code = cut_isbn( $this->get_isbn() );
        for($digits = 1; $digits < 6; $digits++)
        {
            $this->db->from('language_groups')->where('code', substr($code, 0, $digits));
            $res = $this->db->get();
            if ($res->num_rows > 0)
                return $res->row()->name;
        }
        return NULL;
    }
}

// END Book_base class

/**
 * UniBooks Exchange_base class.
 *
 * extended by Sell_model and Request_model
 *
 * @package UniBooks
 * @category Models
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

        return rebuild_codes_results( $this->db->get()->result_array() );
    }
}

// END Exchange_base class

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */  
