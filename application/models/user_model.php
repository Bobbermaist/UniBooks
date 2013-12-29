<?php

class User_model extends CI_Model {
	
	var $user_data;

	function __construct()
	{
		parent::__construct();
		$this->load->helper('security');
		$this->load->database();
	}

		/* Metodi di gestione database */

	public function create_user_data($data, $registration = TRUE)
	{
		return array(
			'user_name'					=> isset($data['user_name']) ? $data['user_name'] : NULL,
			'pass'							=> isset($data['pass']) ? do_hash($data['pass']) : NULL,
			'email'							=> isset($data['email']) ? $data['email'] : NULL,
			'registration_time'	=> $registration ? date("Y-m-d H:i:s") : NULL,
			'confirm_code'			=> $registration ? get_random_string(15) : NULL
		);
	}

	public function insert_user($data)
	{
		$confirm_code = $data['confirm_code'];
		unset($data['confirm_code']);
		$this->db->insert('users', $data);
		$user_id = $this->db->insert_id();
		$this->insert_tmp($user_id, $confirm_code);
		return $user_id;
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

		/* Metodi di gestione utente */

	public function activate($activation_key)
	{
		if ( ! $this->user_data OR $this->user_data->rights > -1
				OR ! $this->check_confirm_code($this->user_data->ID, $activation_key))
			return FALSE;

		$this->user_data->rights = 0;
		$this->update_by_ID($this->user_data->ID, (array) $this->user_data);
		$this->empty_tmp($this->user_data->ID);
		return TRUE;
	}

	public function reset_request()
	{
		if ( ! isset($this->user_data))
			return FALSE;
		$confirm_code = get_random_string(15);
		$request = $this->insert_tmp($this->user_data->ID, $confirm_code);
		return ( ! $request) ?
			FALSE :
			array(
				'ID'						=> $this->user_data->ID,
				'user_name'			=> $this->user_data->user_name,
				'email'					=> $this->user_data->email,
				'confirm_code'	=> $confirm_code
			);
	}

	public function check_reset($confirm_code)
	{
		if ( ! isset($this->user_data) OR $this->user_data->rights < 0)
			return FALSE;
		return $this->check_confirm_code($this->user_data->ID, $confirm_code);
	}

	public function reset($confirm_code, $new_password)
	{
		if ( ! isset($this->user_data) OR $this->user_data->rights < 0
				OR ! $this->check_confirm_code($this->user_data->ID, $confirm_code))
			return FALSE;
		$data = $this->create_user_data(array('pass' => $new_password), FALSE);
		$this->update_by_ID($this->user_data->ID, $data);
		$this->empty_tmp($this->user_data->ID);
		return TRUE;
	}

	public function check_password($password)
	{
		if ( ! isset($this->user_data))
			return FALSE;
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
		if ($this->get_tmp($user_id, 'confirm_code'))
			return FALSE;
		if( ! is_array($data))
			$data = array('confirm_code' => $data);
		$data['user_id'] = $user_id;
		return (boolean) $this->db->insert('tmp_users', $data);
	}

	public function check_confirm_code($user_id, $confirm_code)
	{
		if( ! ($code_cfr = $this->get_tmp($user_id, 'confirm_code')))
			return FALSE;
		return url_encode_utf8($code_cfr) === $confirm_code;
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

	public function empty_tmp($user_id)
	{
		if ( ! $user_id)
			return FALSE;
		$this->db->delete('tmp_users', array('user_id' => $user_id));
	}

	/*
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

	private function clean_tmp($user_id)
	{
		$this->db->from('tmp_users')->where('user_id', $user_id)->limit(1);
		$tmp = $this->db->get()->row();
		if ($tmp->confirm_code === NULL AND $tmp->tmp_email === NULL)
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
	*/

		/* Metodi di appoggio */

	public function create_email_data($user_data, $controller)
	{
		$this->load->helper('url');
		return array(
			'user_name'	=> $user_data['user_name'],
			'link'			=> site_url("{$controller}/{$user_data['ID']}/" . url_encode_utf8($user_data['confirm_code']))
		);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */ 