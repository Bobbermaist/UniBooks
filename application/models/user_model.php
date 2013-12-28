<?php

class User_model extends CI_Model {
	
	var $user_data;

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

		/* Metodi di gestione database */

	public function create_user_data($data, $registration = TRUE)
	{
		$this->load->helper('security');
		return array(
			'user_name'					=> isset($data['user_name']) ? $data['user_name'] : NULL,
			'pass'							=> isset($data['pass']) ? do_hash($data['pass']) : NULL,
			'email'							=> isset($data['email']) ? $data['email'] : NULL,
			'activation_key'		=> $registration ? url_encode_utf8(get_random_string(15)) : NULL,
			//'activation_key'		=> $registration ? substr(md5(rand()),0,15) : NULL,
			'registration_time'	=> $registration ? date("Y-m-d H:i:s") : NULL
		);
	}

	public function insert_user($data)
	{
		$this->load->helper('security');
		$data['activation_key'] = url_decode_utf8($data['activation_key']);
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
		if ( ! $value OR isset($this->user_data))
			return;
		$this->db->from('users')->where($field, $value)->limit(1);
		$res = $this->db->get();
		if ($res->num_rows == 1)
			$this->user_data = $res->row();
	}

	public function update_by_ID($ID, $data)
	{
		foreach ($data as $key => $field)
			if ($field === NULL)
				unset($data[$key]);
		$this->db->where('ID', $ID)->update('users', $data);
	}

	public function empty_activation_key($user_id)
	{
		$this->db->where('ID', $user_id)->update('users', array('activation_key' => NULL));
	}

		/* Metodi di gestione utente */

	public function activate($activation_key)
	{
		$this->load->helper('security');
		if ( ! $this->user_data OR ! $activation_key OR $this->user_data->rights > -1)
			return FALSE;
		if (url_encode_utf8($this->user_data->activation_key) !== $activation_key)
			return FALSE;

		$this->user_data->rights = 0;
		$this->update_by_ID($this->user_data->ID, (array) $this->user_data);
		$this->User_model->empty_activation_key($this->user_data->ID);
		return TRUE;
	}

	public function reset_request()
	{
		if ( ! isset($this->user_data))
			return FALSE;
		$this->load->helper('security');
		$confirm_code = get_random_string(15);
		$request = $this->insert_tmp($this->user_data->ID, array('confirm_password' => $confirm_code));
		return (! $request) ?
			FALSE :
			array(
				'ID'						=> $this->user_data->ID,
				'user_name'			=> $this->user_data->user_name,
				'email'					=> $this->user_data->email,
				'confirm_code'	=> url_encode_utf8($confirm_code)
			);
	}

	public function check_reset($confirm_code)
	{
		if ( ! isset($this->user_data) OR $confirm_code === NULL OR $this->user_data->rights < 0)
			return FALSE;
		$this->load->helper('security');
		return $this->check_tmp($this->user_data->ID, 'confirm_password', url_decode_utf8($confirm_code));
	}

	public function reset($confirm_code, $new_password)
	{
		if ( ! isset($this->user_data) OR $this->user_data->rights < 0)
			return FALSE;
		$this->load->helper('security');
		if( ! $this->check_tmp($this->user_data->ID, 'confirm_password', url_decode_utf8($confirm_code)))
			return FALSE;
		$data = $this->create_user_data(array('pass' => $new_password), FALSE);
		$this->update_by_ID($this->user_data->ID, $data);
		$this->empty_tmp($this->user_data->ID, 'confirm_password');
		return TRUE;
	}

	public function check_password($password)
	{
		if ( ! isset($this->user_data))
			return FALSE;
		$this->load->helper('security');
		return check_hash($this->user_data->pass, $password);
	}

	public function login($password)
	{
		if ( ! $this->check_password($password) OR $this->user_data->rights < 0)
			return FALSE;
		$this->session->set_userdata(array(
			'ID'					=> $this->user_data->ID,
			'rights'			=> $this->user_data->rights,
			'user_name'		=> $this->user_data->user_name,
			'email'				=> $this->user_data->email
		));
		return TRUE;
	}

		/* Metodi di gestione database temporaneo */

	public function insert_tmp($user_id, $data)
	{
		foreach( $data as $key => $value )
			if ($this->get_tmp($user_id, $key))
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
		if ( ! $user_id OR ! $value)
			return FALSE;
		$this->db->from('tmp_users')->where('user_id', $user_id)->limit(1);
		if ($tmp_user = $this->db->get()->row())
			return $tmp_user->$field === $value;
		else
			return FALSE;
	}

	public function get_tmp($user_id, $field)
	{
		if ( ! $user_id)
			return FALSE;
		$this->db->from('tmp_users')->where('user_id', $user_id)->limit(1);
		if ($tmp = $this->db->get()->row())
			return $tmp->$field === NULL ? FALSE : $tmp->$field;
		return FALSE;
	}

	private function clean_tmp($user_id)
	{
		$this->db->from('tmp_users')->where('user_id', $user_id)->limit(1);
		$tmp = $this->db->get()->row();
		if ($tmp->confirm_password === NULL AND $tmp->tmp_email === NULL AND $tmp->confirm_email === NULL)
			$this->db->delete('tmp_users', array('user_id' => $user_id));
	}

	public function empty_tmp($user_id, $fields)
	{
		if ( ! $user_id)
			return FALSE;
		$data = array();
		if (is_array($fields))
			foreach ($fields as $field)
				$data[$field] = NULL;
		else
			$data[$fields] = NULL;
		$this->db->where('user_id', $user_id)->update('tmp_users', $data);
		$this->clean_tmp($user_id);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */ 