<?php

class User_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}

	function insert_user($input_post)
	{
		$this->load->database();
		$data = array(
				'user_name' => $input_post['user_name'],
				'pass' => sha1($input_post['pass']),
				'email' => $input_post['email'],
				'activation_key' => substr(md5(rand()),0,15),
				'registration_time' => date("Y-m-d H:i:s")
			);
		$this->db->insert('users', $data);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_models.php */ 