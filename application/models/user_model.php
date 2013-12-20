<?php

class User_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function create_user_data($post)
	{
		$this->load->helper('security');
		return array(
			'user_name'					=> $post['user_name'],
			'pass'							=> do_hash($post['pass']),
			'email'							=> $post['email'],
			'activation_key'		=> substr(md5(rand()),0,15),
			'registration_time'	=> date("Y-m-d H:i:s")
		);
	}

	public function insert_user($data)
	{
		$this->db->insert('users', $data);
		return $this->db->insert_id();
	}

	public function exists($field, $value)
	{
		$this->db->from('users')->where($field, trim($value))->limit(1);
		return (boolean) $this->db->get()->num_rows();
	}

	public function select_where($field, $value)
	{
		$this->db->from('users')->where($field, trim($value))->limit(1);
		return $this->db->get()->row();
	}

	public function update_by_ID($ID, $data)
	{
		$this->db->where('ID', $ID);
		$this->db->update('users', $data);
	}

	public function login($user, $pass)
	{
		$this->load->helper('security');
		if( ! is_object($user) OR $user->rights < 0 OR ! check_hash($user->pass, $pass) )
			return FALSE;
		return array(
			'ID'					=> $user->ID,
			'rights'			=> $user->rights,
			'user_name'		=> $user->user_name,
			'email'				=> $user->email
		);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */ 