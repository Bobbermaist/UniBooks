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
	 * Try to get a model property, return the property's value
	 * if not empty, FALSE otherwise.
	 *
	 * Be careful!
	 * Return FALSE if the property has one of the
	 * following values:
	 *
	 * FALSE, integer 0, float 0.0, an empty string
	 * and the string '0', empty array, empty object,
	 * NULL.
	 * 
	 * If the property contains one of those values,
	 * or it isn't setted this method will return boolean FALSE
	 * 
	 * @param string the property name
	 * @return mixed object property or FALSE
	 */
	protected function _get($property)
	{
		return empty( $this->{$property} ) ? FALSE : $this->{$property};
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
	 * @param string  table name
	 * @param mixed  string or array
	 * @param mixed  string or NULL
	 * @return mixed  a row object on success or FALSE
	 */
	protected function _select_one($table, $where, $value = NULL)
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
	 * Ex.
	 * <code>
	 *	$table = 'users';
	 *	$data = array(
	 *		'id' => 1,
	 *		'name' => 'test',
	 *	);
	 *	$this->_insert_on_duplicate($table, $data);
	 * </code>
	 *
	 * produces the following query:
	 *
	 * INSERT INTO `users` (id, name) VALUES ('1', 'test')
	 * ON DUPLICATE KEY UPDATE id='1', name='test'
	 *
	 * @param string
	 * @param array
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
	 * Return the user's ID if setted, bool(FALSE)
	 * otherwise.
	 *
	 * @return mixed  int or FALSE
	 */
	public function get_id()
	{
		return $this->_get('ID');
	}

	/**
	 * Set ID method.
	 *
	 * Sets the ID property with the $value parameter
	 * and then retrieve other properties from `users` table.
	 *
	 * If not exists the select_by method will *throw an exception*
	 * 
	 * @param int
	 * @return void
	 */
	public function set_id($value)
	{
		$this->ID = (int) $value;
		$this->select_by('ID');
	}

	/**
	 * Get user name.
	 *
	 * @return mixed  string or FALSE
	 */
	public function get_user_name()
	{
		return $this->_get('user_name');
	}

	/**
	 * Set user name.
	 *
	 * Trim and sets user_name property.
	 *
	 * @param string
	 * @return void
	 */
	public function set_user_name($value)
	{
		$this->user_name = trim($value);
	}

	/**
	 * Get password.
	 *
	 * Retrieve password property, return FALSE if not setted.
	 * NOTE: The password property is the *hashed* password.
	 *
	 * @return mixed  string or FALSE
	 */
	public function get_password()
	{
		$this->_get('password');
	}

	/**
	 * Set password.
	 *
	 * Hash and sets password.
	 *
	 * @param string
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
	 * @return mixed  string or FALSE
	 */
	public function get_email()
	{
		return $this->_get('email');
	}

	/**
	 * Set email.
	 *
	 * Sets email with $value to lower case and trim.
	 *
	 * @param string
	 * @return void
	 */
	public function set_email($value)
	{
		$this->email = utf8_strtolower(trim($value));
	}

	/**
	 * Get registration time.
	 *
	 * @return mixed string or FALSE
	 */
	public function get_registration_time()
	{
		return $this->_get('registration_time');
	}

	/**
	 * Get rights.
	 *
	 * @return mixed int or FALSE
	 */
	public function get_rights()
	{
		return $this->_get('rights');
	}

	/**
	 * Get confirm_code
	 *
	 * @return mixed string or FALSE
	 */
	public function get_confirm_code()
	{
		return $this->_get('confirm_code');
	}

	/**
	 * Get temporary email.
	 *
	 * @return mixed  string or FALSE
	 */
	public function get_tmp_email()
	{
		return $this->_get('tmp_email');
	}

	/**
	 * Set temporary email.
	 *
	 * @param string
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
	 * @param string
	 * @return void
	 */
	public function select_by($field = 'ID')
	{
		$this->db->from('users')->where($field, $this->{$field});
		$res = $this->db->get();

		if ($res->num_rows == 0)
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
		if ($this->get_id() === FALSE)
		{
			$this->load->library('session');
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
	 * @var array (string)
	 * @access protected
	 */
	protected $authors = array();

	/**
	 * Authors id
	 *
	 * @var array (int)
	 * @access private
	 */
	private $_authors_id = array();

	/**
	 * Book's publisher
	 *
	 * @var string
	 * @access protected
	 */
	protected $publisher;

	/**
	 * Publisher ID
	 *
	 * @var int
	 * @access private
	 */
	private $_publisher_id;

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
	 * Language ID
	 *
	 * @var int
	 * @access private
	 */
	private $_language_id;

	/**
	 * Book's categories
	 *
	 * @var array (string)
	 * @access protected
	 */
	protected $categories = array();

	/**
	 * Categories IDs
	 *
	 * @var array (int)
	 * @access private
	 */
	private $_categories_id = array();

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
			$this->_authors_id,
			$this->publisher,
			$this->_publisher_id,
			$this->publication_year,
			$this->pages,
			$this->language,
			$this->_language_id,
			$this->categories,
			$this->_categories_id
		);
	}

	/**
	 * Get ID
	 * 
	 * Return book's ID if setted or FALSE.
	 *
	 * @return mixed  int or FALSE
	 */
	public function get_id()
	{
		return $this->_get('ID');
	}

	/**
	 * Set ID
	 *
	 * Sets the ID property and try to set all properties
	 * from `books` db.
	 * Throws an exception on failure.
	 *
	 * @param int
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
	 * @return mixed  string or boolean
	 */
	public function get_isbn()
	{
		if ($this->_get('ISBN_13') !== FALSE)
		{
			return $this->_get('ISBN_13');
		}
		return $this->_get('ISBN_10');
	}

	/**
	 * Tries to validate and set the ISBN.
	 *
	 * If $value is a valid 13-digit ISBN it sets ISBN_13 property,
	 * if it is a valid 10-digit ISBN sets ISBN_10.
	 *
	 * Return boolean indicating whether the code is valid
	 * 
	 * @param string
	 * @return boolean
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
		return isset($this->ISBN_13) OR isset($this->ISBN_10);
	}

	/**
	 * Insert a book
	 * 
	 * Insert all properties (that should be setted) in the db.
	 * 
	 * @return void
	 */
	public function insert()
	{
		$this->load->database();

		$this->_publisher_id = $this->_insert_info('publishers', $this->publisher);
		$this->_language_id = $this->_insert_info('languages', $this->language);
		$this->_authors_id = $this->_insert_info('authors', $this->authors);
		$this->_categories_id = $this->_insert_info('categories', $this->categories);

		$this->db->insert('books', array(
			'ISBN'							=> cut_isbn( $this->get_isbn() ),
			'google_id'					=> $this->google_id,
			'title'							=> $this->title,
			'publisher_id'			=> $this->_publisher_id,
			'publication_year'	=> $this->publication_year,
			'pages'							=> $this->pages,
			'language_id'				=> $this->_language_id,
		));
		$this->ID = $this->db->insert_id();

		$this->_insert_authors();
		$this->_insert_categories();
	}

	/**
	 * Insert a value in a table (if it already exists get its id)
	 * and return the value's id.
	 *
	 * The value can be an array, in that case an array of
	 * ID is returned.
	 *
	 * If $value is empty (0, NULL, FALSE etc...), return NULL
	 * 
	 * @param string table name
	 * @param mixed string or strings array
	 * @return mixed int or int array
	 * @access private
	 */
	private function _insert_info($table, $value)
	{
		if (empty($value))
		{
			return NULL;
			//return $this->_insert_info($table, 'Unknown');
		}

		if ( ! is_array($value))
		{
			$result = $this->_select_one($table, 'name', $value);
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
	 * Links all authors with a book.
	 * $this->_authors_id must be setted.
	 * 
	 * @return void
	 * @access private
	 */
	private function _insert_authors()
	{
		$data = array();
		foreach ($this->_authors_id as $author_id)
		{
			$data[] = array(
				'book_id'		=> $this->ID,
				'author_id'	=> $author_id,
			);
		}
		$this->db->insert_batch('links_book_author', $data);
	}

	/**
	 * Links all categories with a book.
	 * $this->_categories_id must be setted.
	 * 
	 * @return void
	 * @access private
	 */
	private function _insert_categories()
	{
		$data = array();
		foreach ($this->_categories_id as $category_id)
		{
			$data[] = array(
				'book_id'			=> $this->ID,
				'category_id'	=> $category_id,
			);
		}
		$this->db->insert_batch('links_book_category', $data);
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
	 * @param string
	 * @return void
	 */
	public function select_by($field)
	{
		$this->load->database();
		$this->db->from('books');
		if ($field === 'ISBN')
		{
			$this->db->where('ISBN', cut_isbn( $this->get_isbn() ));
		}
		else
		{
			$this->db->where($field, $this->$field);
		}
		$this->db->limit(1);
		$this->_set();
	}

	/**
	 * Set all object properties from db.
	 * The db query must be composed previously.
	 *
	 * *Throws an exception* if the query does not 
	 * procudes any result.
	 * 
	 * @return void
	 * @access private
	 */
	private function _set()
	{
		$query = $this->db->get();
		if ($res->num_rows == 0)
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

		$book = $query->row();

		$this->ID = (int) $book->ID;
		$this->ISBN_13 = uncut_isbn_13($book->ISBN);
		$this->ISBN_10 = uncut_isbn_10($book->ISBN);
		$this->google_id = $book->google_id;
		$this->title = $book->title;
		$this->_publisher_id = (int) $book->publisher_id;
		$this->publication_year = (int) $book->publication_year;
		$this->pages = (int) $book->pages;
		$this->_language_id = (int) $book->language_id;

		$this->publisher = $this->_select_one('publishers', 'ID', $this->_publisher_id)->name;
		$this->language = $this->_select_one('languages', 'ID', $this->_language_id)->name;
		$this->_join_authors();
		$this->_join_categories();
	}

	/**
	 * Set authors property by joining the book ID on db.
	 *
	 * ID property must be setted.
	 * 
	 * @return void
	 * @access private
	 */
	private function _join_authors()
	{
		$this->db->from('authors')->where('book_id', $this->ID)
			->join('links_book_author', 'authors.ID = links_book_author.author_id');
		$results = $this->db->get()->result();
		foreach ($results as $result)
		{
			$this->authors[] = $result->name;
		}
	}

	/**
	 * Set categories property by joining the book ID on db.
	 *
	 * ID property must be setted.
	 * 
	 * @return void
	 * @access private
	 */
	private function _join_categories()
	{
		$this->db->from('categories')->where('book_id', $this->ID)
			->join('links_book_category', 'categories.ID = links_book_category.category_id');
		$results = $this->db->get()->result();
		foreach ($results as $result)
		{
			$this->categories[] = $result->name;
		}
	}

	/**
	 * Get publisher's name from the table `publisher_codes`
	 * through ISBN.
	 * ISBN_13 or ISBN_10 must be setted.
	 * 
	 * @return mixed string or NULL
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
	 * @return mixed string or NULL
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
 * UniBooks Exchange_base Class
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
	 * user_id property.
	 * 
	 * @return void
	 * @access protected
	 */
	protected function _set_user_id()
	{
		$user = new User_base;
		$user->read_session();
		$this->user_id = $user->get_id();
	}

	/**
	 * Get book id
	 *
	 * @return mixed  int or FALSE
	 */
	public function get_book_id()
	{
		return $this->_get('book_id');
	}

	/**
	 * Set book_id
	 * 
	 * @param int
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
	 * @param string
	 * @param array (string) (optional)
	 * @return void
	 * @access protected
	 */
	protected function _insert($table, $properties = array())
	{
		$clause = array(
			'user_id'	=> $this->user_id,
			'book_id'	=> $this->book_id,
		);
		if ($this->_select_one($table, $clause) !== FALSE)
		{
			if ($table === 'books_for_sale')
			{
				throw new Custom_exception(EXISTING_SALE);
			}
			elseif ($table === 'books_requested')
			{
				throw new Custom_exception(EXISTING_REQUEST);
			}
			else
			{
				throw new Custom_exception(INVALID_PARAMETER);
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
	 * @param string
	 * @return void
	 * @access protected
	 */
	protected function _delete($table)
	{
		if (isset($this->book_id))
		{
			$this->db->delete($table, array(
				'user_id'	=> $this->user_id,
				'book_id'	=> $this->book_id,
			));
		}
	}
}

// END Exchange_base class

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */  
