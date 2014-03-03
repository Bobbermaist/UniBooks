<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller  {

	protected $logged;

	private $view_names = array();

	private $view_data = array();

	private $total_views = 0;

	public function __construct()
	{
		parent::__construct();
		//$this->benchmark->mark('start');
		$this->load->helper('url');
		$this->load->model('User_model');
		$this->output->set_header('Content-Type: text/html; charset=' . config_item('charset'));

		$this->logged = $this->User_model->read_session();
	}

	protected function _set_view($name, $content = NULL)
	{
		$this->view_names[] = $name;
		$this->view_data[] = $content;
		++$this->total_views;
	}

	protected function _view()
	{
		$this->load->view('template/head');

		for ($i=0; $i < $this->total_views; ++$i)
		{
			$this->load->view( $this->view_names[$i], $this->view_data[$i] );
			$this->load->clean_cached_vars();
		}
		$this->load->view('template/coda');
		//$this->benchmark->mark('end');
		//echo $this->benchmark->elapsed_time('start', 'end');
	}
}