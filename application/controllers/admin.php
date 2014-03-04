<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_restrict_area('admin', 'admin/index');
	}

	public function index()
	{
		$this->_set_view('generic', array(
			'p'	=> 'Benvenuto, amministratore <b>' . $this->User_model->user_name() . '</b>!'
		));

		$this->_view();
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */ 
 
