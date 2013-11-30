<?php

class User_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}

	function insert_user($data)
	{
		$this->load->database();
		//todo: escape su user_name
		//$data['user_name'] = ...
		$this->db->insert('users', $data);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_models.php */ 