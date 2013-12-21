<?php

class User_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function create_user_data($post, $registration = TRUE)
	{
		$this->load->helper('security');
		return array(
			'user_name'					=> isset($post['user_name']) ? $post['user_name'] : NULL,
			'pass'							=> isset($post['pass']) ? do_hash($post['pass']) : NULL,
			'email'							=> isset($post['email']) ? $post['email'] : NULL,
			'activation_key'		=> $registration ? substr(md5(rand()),0,15) : NULL,
			'registration_time'	=> $registration ? date("Y-m-d H:i:s") : NULL
		);
	}

	public function insert_user($data)
	{
		$this->db->insert('users', $data);
		return $this->db->insert_id();
	}

	public function exists($field, $value)
	{
		$this->db->from('users')->where($field, $value)->limit(1);
		return (boolean) $this->db->get()->num_rows();
	}

	public function select_where($field, $value)
	{
		if( ! $value )
			return FALSE;
		$this->db->from('users')->where($field, $value)->limit(1);
		return $this->db->get()->row();
	}

	public function update_by_ID($ID, $data)
	{
		foreach ($data as $key => $field)
			if( $field === NULL )
				unset($data[$key]);
		$this->db->where('ID', $ID)->update('users', $data);
	}

	public function empty_activation_key($user_id)
	{
		$this->db->where('ID', $user_id)->update('users', array('activation_key' => NULL));
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