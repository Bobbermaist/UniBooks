<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Load the Custom_exception class.
 */

require_once CORE_PATH . 'Custom_exception.php';

/**
 * MY_Loader class.
 *
 * Extends the default loader class.
 *
 * @package UniBooks
 * @category Loader
 * @author Emiliano Bovetti
 */
class MY_Loader extends CI_Loader {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The variables loaded in this way
     *
     * <code>
     *  $this->load->vars($var);
     * </code>
     * or
     * <code>
     *  $this->load->view('name', $var);
     * </code>
     *
     * are cached by CodeIgniter and if you try
     * to load the same view with different
     * variables this could lead to problems.
     *
     * This method allows to unset all these cached variables.
     *
     * @return void
     */
    public function clean_cached_vars()
    {
        // sets this instance variable to its default value
        $this->_ci_cached_vars = array();
    }
}

// END MY_Loader class

/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */