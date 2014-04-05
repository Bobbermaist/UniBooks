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
 * Book_base class.
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
     * @param int  $value the ID to set
     * @return void
     */
    public function set_id($value)
    {
        $this->ID = (int) $value;
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
            'ISBN'              => cut_isbn( $this->get_isbn() ),
            'google_id'         => $book_data['google_id'],
            'title'             => $book_data['title'],
            'publisher_id'      => $this->_insert_info('publishers', $book_data['publisher']),
            'publication_year'  => $book_data['publication_year'],
            'pages'             => $book_data['pages'],
            'language_id'       => $this->_insert_info('languages', $book_data['language']),
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
                'book_id'   => $this->ID,
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
        $result = $this->db->get()->row();

        $book = rebuild_codes_row($result);
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

/* End of file Book_base.php */
/* Location: ./application/core/Book_base.php */  