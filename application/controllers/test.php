<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->view('template/head');
		$this->load->view('template/body');
		
		$this->load->view('par', array('par' => 'Migration test'));
		$this->load->view('par', array('par' => 'Database <b>users</b>: '.
			anchor('test/migration/', 'Effettua current migration')));
		
		$this->load->view('template/coda');
	}

	public function migration()
	{
		$this->load->library('migration');
		if ( ! $this->migration->current() )
			show_error($this->migration->error_string());
		else
			redirect('test');
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
