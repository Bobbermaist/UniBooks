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
 * UniBooks MY_Controller class.
 *
 * Extended by controllers.
 *
 * Initializes an instance variable
 * indicating whether the user is logged in,
 * has a system to queue and load
 * multiple views and provides an exception
 * handler system.
 *
 * @package UniBooks
 * @category Controllers
 * @author Emiliano Bovetti
 */
class MY_Controller extends CI_Controller  {

	/**
	 * Initialized by constructor.
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $logged;

	/**
	 * The "_try" method stores here the exception
	 * code if an exception is catched.
	 *
	 * @var int
	 * @access protected
	 */
	protected $exception_code = NO_EXCEPTIONS;

	/**
	 * The "_try" method stores here the exception
	 * messafe if an exception is catched.
	 *
	 * @var string
	 * @access protected
	 */
	protected $exception_message;

	/**
	 * An array of all view names to be shown.
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
	 * Constructor.
	 *
	 * Sets UTF-8 header and sets $this->logged property
	 * calling read_session() method.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->output->set_header('Content-Type: text/html; charset=' . config_item('charset'));

		$this->logged = $this->User_model->read_session();
	}

	/**
	 * Allows to add a view that can be showed later
	 * by _view method.
	 *
	 * @param string  $name the name of the view to queue
	 * @param mixed  $content data to pass to the view queued
	 * @return void
	 * @access protected
	 */
	protected function _set_view($name, $content = NULL)
	{
		$this->_view_names[] = $name;
		$this->_view_data[] = $content;
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

		while (current($this->_view_names) !== FALSE)
		{
			$this->load->view( current($this->_view_names), current($this->_view_data) );
			$this->load->clean_cached_vars();

			next($this->_view_names);
			next($this->_view_data);
		}
		
		$this->load->view('template/coda');
	}

	/**
	 * Executes a try - catch command through a callback.
	 *
	 * A model that may throws exception should be executed
	 * with this method.
	 *
	 * The first parameter indicates the model name to call
	 * (e.g. 'User_model'), the second one the method
	 * (like any method or User_model).
	 *
	 * It is also possible to pass an arbitrary numer of parameters
	 * in addition to these two
	 * `$this->_try('User_model', 'method_name', $param1, $param2, ...)`.
	 *
	 * In this case all parameters will be passed to the called method.
	 *
	 * E.g.
	 *
	 * <pre> 
	 *  $this->_try('User_model', 'reset_password', $confirm_code, $new_password);
	 * </pre>
	 * 
	 * Corresponds to:
	 *
	 * <pre>
	 *  try {
	 *    $this->User_model->reset_password($confirm_code, $new_password);
	 *  } catch (Custom_exception $e) {
	 *    $this->exception_code = $e->getCode();
	 *    $this->exception_message = $e->getMessage();
	 *  }
	 * </pre>
	 *
	 * The model in this example should be loaded before 
	 * calling this method, otherwise an INVALID_PARAMETER 
	 * exception will be thrown.
	 *
	 * If the called method raises a Custom_exception,
	 * its code will be stored in *exception_code* property
	 * and its message in *exception_message*.
	 *
	 * @param string  $property_name controller's property name
	 * @param string  $method_name method name
	 * @param mixed  $optional,...
	 * @return void
	 * @access protected
	 */
	protected function _try($property_name, $method_name)
	{
		$callback = array($this->{$property_name}, $method_name);

		$param_arr = array();
		for ($i=2; func_num_args() > $i; $i++)
		{
			$param_arr[] = func_get_arg($i);
		}

		if (is_callable($callback))
		{
			try
			{
				if (empty($param_arr))
				{
					$this->{$property_name}->{$method_name}();
					//call_user_func($callback);
				}
				else
				{
					call_user_func_array($callback, $param_arr);
				}
			}
			catch (Custom_exception $e)
			{
				$this->exception_code = $e->getCode();
				$this->exception_message = $e->getMessage();
			}
		}
		else
		{
			throw new Custom_exception(INVALID_PARAMETER);
		}
	}

	/**
	 * Loads a message from message_lang and enqueue
	 * this message through "_set_view()".
	 *
	 * If an exception was previously catched
	 * enqueue the exception message instead the
	 * requested message.
	 * After sets the *exception_message* to
	 * NO_EXCEPTIONS.
	 *
	 * If $name parameter is FALSE (default)
	 * won't load any message in case of the is
	 * no exception
	 *
	 * @param string|false $name  the name of message lang
	 * @return void
	 * @throws Custom_exception(INVALID_PARAMETER)
	 *    if can't loads the requested lang line
	 * @access protected
	 */
	protected function _set_message($name = FALSE)
	{
		if ($this->exception_code !== NO_EXCEPTIONS)
		{
			$this->_queue_exception_message();
		}
		elseif ($name !== FALSE)
		{
			$message = $this->lang->line('message_' . $name);
			if ($message === FALSE)
			{
				throw new Custom_exception(INVALID_PARAMETER);
			}
			else
			{
				$this->_set_view('generic', array(
					'p'		=> $message,
				));
			}
		}
	}

	/**
	 * Works as _set_message() but redirect if no exception
	 * where thrown.
	 *
	 * @param string  $controller redirect here
	 * @param string  $default in cases where $controller can be 
	 *    FALSE it is possible to set a default redirect
	 * @return void
	 * @access protected
	 */
	protected function _redirect($controller, $default = 'user')
	{
		if ($this->exception_code !== NO_EXCEPTIONS)
		{
			$this->_queue_exception_message();
		}
		else
		{
			if (empty($controller))
			{
				redirect($default);
			}
			else
			{
				redirect($controller);
			}
		}
	}

	/**
	 * Add the "exception_message" to te view queue
	 * as a paragraph with id "error"
	 *
	 * @return void
	 * @access private
	 */
	private function _queue_exception_message()
	{
		$this->_set_view('generic', array(
			'p'		=> $this->exception_message,
			'id'	=> 'error',
		));
		/*
		$this->exception_code = NO_EXCEPTIONS;
		unset($this->exception_message);
		*/
	}

	/**
	 * Can be called in a controller constructor to restrict
	 * the access.
	 * 
	 * By default restricts to users (logged in)
	 *
	 * @param int  $required_rights USER_RIGHTS or ADMIN_RIGHTS contants
	 * @param string  $redirect redirect here after log in (optional)
	 * @return void
	 * @access protected
	 */
	protected function _restrict_area($required_rights = USER_RIGHTS, $redirect = NULL)
	{
		if ($this->logged === FALSE OR $this->User_model->get_rights() < $required_rights)
		{
			if ($redirect !== NULL)
			{
				$this->User_model->add_userdata('redirect', $redirect);
			}
			redirect('login');
		}
	}

	/**
	 * Can be called in a controller to verify if some input POST
	 * data are setted.
	 * 
	 * $fields can be a string indicating wich field POST is required
	 * or a string array with all fields POST
	 *
	 * @param mixed  $fields the field or fields of POST data to check (string or array)
	 * @return void
	 * @throws Custom_exception(INVALID_PARAMETER) if a POST data missing
	 * @access protected
	 */
	protected function _post_required($fields)
	{
		if ( ! is_array($fields))
		{
			$fields = array($fields);
		}

		foreach ($fields as $field)
		{
			if ( ! $this->input->post($field))
			{
				throw new Custom_exception(INVALID_PARAMETER);
			}
		}
	}
}

// END MY_Controller class

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */  
