<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	public function index()
	{
		$this->load->view('head');
		$this->load->view('body');

		$this->load->model('User_model');
		if( $this->User_model->exists('user_name','  BoB        ') === FALSE )
			echo 'FALSE';
		else
			echo 'TRUE';

		if( $this->User_model->exists('email','  EmilianoBovetti@hotmail.it') === FALSE )
			echo 'FALSE';
		else
			echo 'TRUE';

		$this->load->view('coda');
	}
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */ 
 
