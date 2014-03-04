<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller  {

	protected $logged;

	private $_view_names = array();

	private $_view_data = array();

	private $_total_views = 0;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('User_model');
		$this->output->set_header('Content-Type: text/html; charset=' . config_item('charset'));

		$this->logged = $this->User_model->read_session();
	}

	protected function _set_view($name, $content = NULL)
	{
		$this->_view_names[] = $name;
		$this->_view_data[] = $content;
		++$this->_total_views;
	}

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