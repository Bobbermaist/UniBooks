<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package	UniBooks
 * @author	Emiliano Bovetti
 * @since		Version 1.0
 */

/**
 * UniBooks Model Class
 *
 * @package			UniBooks
 * @category		Models
 * @author			Emiliano Bovetti
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
	 * _get
	 * 
	 * Allows models to retrieve their properties.
	 * 
	 * @param string 	$property the property to get
	 * @return mixed 	FALSE if not setted, the object property on succes
	 */
	protected function _get($property)
	{
		return isset($this->$property) ? $this->$property : FALSE;
	}

	/**
	 * _select_one
	 * 
	 * Select one result from a table.
	 * Accepts three parameters, the table name,
	 * a string with the field value or an associative array
	 * ('field'	=> 'value').
	 * If the second parameter is a string, the third accepts
	 * the value.
	 * 
	 * @param string 	table name
	 * @param	mixed 	string or array
	 * @param mixed 	a string or NULL
	 * @return mixed 	a row object on success or FALSE
	 */
	protected function _select_one($table, $where, $value = NULL)
	{
		$this->db->from($table)->where($where, $value)->limit(1);
		$query = $this->db->get();
		return $query->num_rows == 1 ? $query->row() : FALSE;
	}
}

/**
 * UniBooks User_base Class
 *
 * @package			UniBooks
 * @category		Base Models
 * @author			Emiliano Bovetti
 */
class User_base extends MY_Model {

	/**
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
	 * Random string to activate /reset account settings
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
	 * get / set id
	 * 
	 * If the method is called with NULL,
	 * he return the ID value (FALSE if not setted).
	 *
	 * Otherwise he set the ID property with the $value parameter
	 * and then he retrieve other properties from `users` database.
	 * In this case return boolean indicates whether the ID exists.
	 * If not exists the select_by method will unset all properties. 
	 * 
	 * @param mixed 	int or NULL
	 * @return mixed
	 */
	public function id($value = NULL)
	{
		if ($value === FALSE)
		{
			return FALSE;
		}
		if ($value === NULL)
		{
			return $this->_get('ID');
		}
		
		$this->ID = (int) $value;
		return $this->select_by('ID');
	}

	/**
	 * get / set user_name
	 * 
	 * Retrieve user_name with $value === NULL,
	 * set user_name otherwise.
	 *
	 * @param mixed 	string or NULL
	 * @return mixed 	string or FALSE
	 */
	public function user_name($value = NULL)
	{
		return ($value === NULL)
			? $this->_get('user_name')
			: $this->user_name = $value;
	}

	/**
	 * get / set password
	 * 
	 * Retrieve password with $value === NULL,
	 * hash the $value and set password otherwise.
	 *
	 * @param mixed 	string or NULL
	 * @return mixed 	string or FALSE
	 */
	public function password($value = NULL)
	{
		$this->load->helper('security');
		return ($value === NULL)
			? $this->_get('password')
			: $this->password = do_hash($value);
	}

	/**
	 * get / set email
	 * 
	 * Retrieve email with $value === NULL,
	 * set email otherwise.
	 *
	 * @param mixed 	string or NULL
	 * @return mixed 	string or FALSE
	 */
	public function email($value = NULL)
	{
		return ($value === NULL)
			? $this->_get('email')
			: $this->email = $value;
	}

	/**
	 * get registration_time
	 *
	 * @return mixed string or FALSE
	 */
	public function registration_time()
	{
		return $this->_get('registration_time');
	}

	/**
	 * get rights
	 *
	 * @return mixed int or FALSE
	 */
	public function rights()
	{
		return $this->_get('rights');
	}

	/**
	 * get confirm_code
	 *
	 * @return mixed string or FALSE
	 */
	public function confirm_code()
	{
		return $this->_get('confirm_code');
	}

	/**
	 * get registration_time
	 *
	 * @return mixed string or FALSE
	 */
	public function tmp_email($value = NULL)
	{
		return ($value === NULL)
			? $this->_get('tmp_email')
			: $this->tmp_email = $value;
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
	 * Set registration time.
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
	 * in $field (default - ID)
	 *
	 * The field indicated must be a unique value
	 * (ID, user_name, email) and corresponding object 
	 * property must be setted.
	 *
	 * Unsets all properties on failure
	 *
	 * @return boolean
	 */
	public function select_by($field = 'ID')
	{
		$this->db->from('users');
			
		$this->db->where($field, $this->$field);
		$res = $this->db->get();

		if ($res->num_rows == 0)
		{
			$this->unset_all;
			return FALSE;
		}

		$user_data = $res->row();
		$this->ID = (int) $user_data->ID;
		$this->user_name = $user_data->user_name;
		$this->password = $user_data->password;
		$this->email = $user_data->email;
		$this->registration_time = $user_data->registration_time;
		$this->rights = (int) $user_data->rights;
		return TRUE;
	}

	/**
	 * Retrieves all object properties from the session data
	 *
	 * @return boolean
	 */
	public function read_session()
	{
		if ($this->id() === FALSE)
		{
			$this->load->library('session');

			return $this->id( $this->session->userdata('user_id') );
		}
		return TRUE;
	}
}


/**
 * UniBooks Book_base Class
 *
 * @package			UniBooks
 * @category		Base Models
 * @author			Emiliano Bovetti
 */
class Book_base extends MY_Model {

	/**
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
	 * @var string
	 * @access protected
	 */
	protected $publisher;

	/**
	 * @var int
	 * @access private
	 */
	private $_publisher_id;

	/**
	 * @var int
	 * @access protected
	 */
	protected $publication_year;

	/**
	 * @var int
	 * @access protected
	 */
	protected $pages;

	/**
	 * @var string
	 * @access protected
	 */
	protected $language;

	/**
	 * @var int
	 * @access private
	 */
	private $_language_id;

	/**
	 * @var array (string)
	 * @access protected
	 */
	protected $categories = array();

	/**
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
	 * Get ISBN
	 * 
	 * Return a 13-digit ISBN if setted, 10-digit if not,
	 * FALSE if neither are setted.
	 * 
	 * @return mixed string or boolean
	 * @access private
	 */
	private function _get_isbn()
	{
		if ($this->_get('ISBN_13') !== FALSE)
		{
			return $this->_get('ISBN_13');
		}
		return $this->_get('ISBN_10');
	}

	/**
	 * Get / Set ISBN
	 * 
	 * Calls $this->_get_isbn() if $value === NULL, otherwise tries
	 * to validate and set the ISBN.
	 *
	 * If $value is a valid 13-digit ISBN it sets ISBN_13 property,
	 * if it is a valid 10-digit ISBN sets ISBN_10.
	 * 
	 * @param mixed string or NULL
	 * @return mixed string or boolean
	 */
	public function ISBN($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_get_isbn();
		}

		$isbn = strtoupper(trim($value));
		$valid = validate($isbn);
		if ($valid === 13)
		{
			return (boolean) $this->ISBN_13 = $isbn;
		}
		if ($valid === 10)
		{
			return (boolean) $this->ISBN_10 = $isbn;
		}
		if (strlen($isbn) === 10)
		{
			return $this->ISBN("978$isbn");
		}
		return FALSE;
	}

	/**
	 * Insert a book
	 * 
	 * Insert all properties (that must be setted) in the db.
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
			'ISBN'							=> cut_isbn( $this->ISBN() ),
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
	 * Insert a value in a table (if it already get its id)
	 * and return the value's ID.
	 *
	 * The value can be an array, in that case an array of
	 * ID is returned.
	 *
	 * If $value === NULL, 'Unknown' is inserted.
	 * 
	 * @param string table name
	 * @param mixed string or strings array
	 * @return mixed int or int array
	 * @access private
	 */
	private function _insert_info($table, $value)
	{
		if ($value === NULL)
		{
			return $this->_insert_info($table, 'Unknown');
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
	 * This field must be unique
	 * (ID, 13 or 10 digit ISBN, google_id)
	 * and corresponding property must be setted.
	 *
	 * On success sets all properties
	 * 
	 * @param string
	 * @return boolean
	 */
	public function select_by($field)
	{
		$this->load->database();
		$this->db->from('books');
		if ($field === 'ISBN')
		{
			$this->db->where('ISBN', cut_isbn( $this->ISBN() ));
		}
		else
		{
			$this->db->where($field, $this->$field);
		}
		$this->db->limit(1);
		return $this->_set();
	}

	/**
	 * Set all object properties from db.
	 * The db query must be composed previously.
	 * 
	 * @return boolean
	 * @access private
	 */
	private function _set()
	{
		$query = $this->db->get();
		if ($query->num_rows == 0)
		{
			//$this->unset_all();
			return FALSE;
		}
		$book = $query->row();

		$this->ID = $book->ID;
		$this->ISBN_13 = uncut_isbn_13($book->ISBN);
		$this->ISBN_10 = uncut_isbn_10($book->ISBN);
		$this->google_id = $book->google_id;
		$this->title = $book->title;
		$this->_publisher_id = $book->publisher_id;
		$this->publication_year = $book->publication_year;
		$this->pages = $book->pages;
		$this->_language_id = $book->language_id;

		$this->publisher = $this->_select_one('publishers', 'ID', $this->_publisher_id)->name;
		$this->language = $this->_select_one('languages', 'ID', $this->_language_id)->name;
		$this->_join_authors();
		$this->_join_categories();
		return TRUE;
	}

	/**
	 * Set authors property by joining the book ID on db.
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
		$code = cut_isbn( $this->ISBN() );

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
		$code = cut_isbn( $this->ISBN() );
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


/**
 * UniBooks Exchange_base Class
 *
 * needed by Sell_model and Request_model
 *
 * @package			UniBooks
 * @category		Models
 * @author			Emiliano Bovetti
 */
class Exchange_base extends MY_Model {

	/**
	 * @var int
	 * @access protected
	 */
	protected $user_id;

	/**
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
		$this->user_id = $user->ID;
	}

	/**
	 * Get / Set book_id
	 *
	 * Retrieve $this->book_id if $value === NULL
	 * otherwive set this property.
	 * 
	 * @param int
	 * @return mixed int or FALSE
	 */
	public function book_id($value = NULL)
	{
		return ($value === NULL)
			? $this->_get('book_id')
			: $this->book_id = (int) $value;
	}

	/**
	 * _insert method, it inserts in $table the user_id and book_id
	 * properties.
	 * Return FALSE if exists a row with these values.
	 *
	 * The second parameter can be used to insert other 
	 * property
	 *
	 * 
	 * @param string
	 * @param array (string)
	 * @return boolean
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
			return FALSE;
		}
		foreach ($properties as $property)
		{
			$clause[$property] = $this->property;
		}
		$this->db->insert($table, $clause);
		return TRUE;
	}

	/**
	 * Deletes a row.
	 * user_id and book_id properties must be setted
	 * 
	 * @param string
	 * @return void
	 */
	public function delete($table)
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

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */  
