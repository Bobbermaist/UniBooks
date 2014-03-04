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
 * UniBooks MY_Controller Class
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class MY_Controller extends CI_Controller  {

	/**
	 * @var boolean
	 * @access protected
	 */
	protected $logged;

	/**
	 * An array of all view names to be showed.
	 *
	 * @var array  (string)
	 * @access private
	 */
	private $_view_names = array();

	/**
	 * An array of all view data to be showed.
	 *
	 * @var array  (mixed)
	 * @access private
	 */
	private $_view_data = array();

	/**
	 * Number of views to be showed.
	 *
	 * @var int
	 * @access private
	 */
	private $_total_views = 0;

	/**
	 * Constructor
	 * Loads: URL helper, User Model, sets UTF-8 header
	 * and sets $this->logged property.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('User_model');
		$this->output->set_header('Content-Type: text/html; charset=' . config_item('charset'));

		$this->logged = $this->User_model->read_session();
	}

	/**
	 * Allows to add a view that can be showed later
	 * by _view method.
	 *
	 * @param string  the name of the view
	 * @param mixed  the data to pass to the view
	 * @return void
	 * @access protected
	 */
	protected function _set_view($name, $content = NULL)
	{
		$this->_view_names[] = $name;
		$this->_view_data[] = $content;
		++$this->_total_views;
	}

	/**
	 * Shows all the views queued by _set_view method.
	 *
	 * @return void
	 * @access protected
	 */
	protected function _view()
	{
		$this->load->view('template/head');

		for ($i=0; $i < $this->_total_views; ++$i)
		{
			$this->load->view( $this->_view_names[$i], $this->_view_data[$i] );
			$this->load->clean_cached_vars();
		}
		$this->load->view('template/coda');
	}

	/**
	 * Can be called in a controller constructor to restrict
	 * the access.
	 * 
	 * By default restricts to users (logged in)
	 *
	 * @param string  'user' or 'admin'
	 * @param string  optional - redirect here after log in
	 * @return void
	 * @access protected
	 */
	protected function _restrict_area($required = 'user', $redirect = NULL)
	{
		if ($this->logged === FALSE OR $this->User_model->rights < $required_rights)
		{
			switch ($required)
			{
				case 'admin':
					$required_rights = 1;
					break;
				case 'user': default:
					$required_rights = 0;
					break;
			}
			
			if ($redirect !== NULL)
			{
				$this->session->set_userdata('redirect', $redirect);
			}
			redirect('login');
		}
	}
}