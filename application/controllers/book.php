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
 * Book controller.
 *
 * Deals with the book extraction.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class Book extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Book_model');
    }

    public function index()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->_set_view('form/single_field', array(
            'action'                => 'book/',
            'label'                 => 'Cerca un libro (ISBN)',
            'submit_name'       => 'search_book',
            'submit_value'  => 'Cerca',
            'input'                 => array(
                'name'          => 'search_key',
                'id'                => 'search_book',
                'maxlength' => '255',
            ),
        ));

        if ($this->form_validation->run() === TRUE)
        {
            $search_key = $this->input->post('search_key');
            if ($search_key !== FALSE)
            {
                $this->_try('Book_model', 'set_isbn', $search_key);
                if ($this->_caught_exception() === FALSE)
                {
                    redirect("book/result/{$search_key}");
                }
                else
                {
                    $this->_set_message();
                }
            }
        }

        $this->_view();
    }

    public function result($isbn = 0)
    {
        $this->_try('Book_model', 'set_isbn', $isbn);
        $this->_try('Book_model', 'search_by_isbn');

        if ($this->_caught_exception() === FALSE)
        {
            $this->_set_view('book', $this->Book_model->get_array());
        }
        $this->_set_message();
        $this->_view();
    }
}

// END Book class

/* End of file book.php */
/* Location: ./application/controllers/book.php */ 