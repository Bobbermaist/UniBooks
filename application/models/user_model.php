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

	public function insert_tmp($user_id, $data)
	{
		foreach( $data as $key => $value )
			if( $this->get_tmp($user_id, $key) )
				return FALSE;
		$data['user_id'] = $user_id;
		$query = $this->db->insert_string('tmp_users', $data) . ' ON DUPLICATE KEY UPDATE ';
		$query .= isset($data['confirm_password']) ? 'confirm_password=\'' . $data['confirm_password'] . '\',' : '';
		$query .= isset($data['tmp_email']) ? 'tmp_email=\'' . $data['tmp_email'] . '\',' : '';
		$query .= isset($data['confirm_email']) ? 'confirm_email=\'' . $data['confirm_email'] . '\',' : '';
		$query = mb_substr($query, 0, -1, 'UTF-8');
		return (boolean) $this->db->query($query);
	}

	public function check_tmp($user_id, $field, $value)
	{
		if( ! $user_id OR ! $value )
			return FALSE;
		$this->db->from('tmp_users')->where('user_id', $user_id)->limit(1);
		if( $tmp_user = $this->db->get()->row() )
			return $tmp_user->$field === $value;
		else
			return FALSE;
	}

	public function get_tmp($user_id, $field)
	{
		if( ! $user_id )
			return FALSE;
		$this->db->select($field)->from('tmp_users')->where('user_id', $user_id)->limit(1);
		if( $tmp_user = $this->db->get()->row() )
			return $tmp_user->$field === NULL ? FALSE : $tmp_user->$field;
		return FALSE;
	}

	public function empty_tmp($user_id, $fields)
	{
		if( ! $user_id )
			return FALSE;
		$data = array();
		if( is_array($fields) )
			foreach ($fields as $field)
				$data[$field] = NULL;
		else
			$data[$fields] = NULL;
		return $this->db->where('user_id', $user_id)->update('tmp_users', $data);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */ 