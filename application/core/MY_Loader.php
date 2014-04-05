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
        set_error_handler(array($this, '_error_handler'));
        spl_autoload_register(array($this, '_autoload_core_class'));
        $this->class_file('Custom_exception');
    }

    /**
     * Custom error handler.
     * Converts errors in exceptions.
     *
     * @param $severity  PHP error code.
     * @param $message  Text of error.
     * @param $filename  File error occurred in.
     * @param $line  Line number of error.
     * @return void
     * @throws ErrorException
     * @access private
     */
    private function _error_handler($severity, $message, $filename, $line)
    { 
        throw new ErrorException($message, 0, $severity, $filename, $line); 
    }

    /**
     * Autoload method for core classes.
     *
     * @param $class_name  The class name.
     * @return void
     * @access private
     */
    private function _autoload_core_class($class_name)
    {
        if (stripos($class_name, 'CI') === FALSE AND stripos($class_name, 'PEAR') === FALSE)
        {
            $this->class_file($class_name);
        }
    }

    /**
     * Load a class.
     *
     * @param $class_name  Class name.
     * @param $file_path  File path.
     * @return void
     */
    public function class_file($class_name, $file_path = CORE_PATH)
    {
        $full_path = $file_path . $class_name . '.php';
        if (in_array($full_path, $this->_ci_loaded_files))
        {
            log_message('debug', $class_name . ' class already loaded. Second attempt ignored.');
            return;
        }

        try
        {
            include_once $full_path;
        }
        catch (ErrorException $e)
        {
            $log = 'Unable to load the requested class: ' . $class_name;
            if (ENVIRONMENT === 'development')
            {
                $log .= '<br><b> Full Trace: </b>' . str_replace('#', '<br>#', $e->getTraceAsString());
            }

            log_message('error', $log);
            show_error($log);
        }

        if ( ! isset($e))
        {
            $this->_ci_loaded_files[] = $full_path;
        }
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