<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends User_base {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function insert()
	{
		$this->_set_confirm_code();
		$this->_set_time();
		$this->rights = -1;

		$this->db->insert('users', array(
			'user_name'					=> $this->user_name,
			'password'					=> $this->password,
			'email'							=> $this->email,
			'registration_time'	=> $this->registration_time,
			'rights'						=> $this->rights,
		));
		$this->ID = (int) $this->db->insert_id();

		$this->_insert_tmp();
	}

	public function update()
	{
		$this->db->where('ID', $this->ID)->update('users', array(
			'user_name'					=> $this->user_name,
			'password'					=> $this->password,
			'email'							=> $this->email,
			'registration_time'	=> $this->registration_time,
			'rights'						=> $this->rights,
		));
	}
	
		/* user methods */
	public function activate($activation_key)
	{
		if ( $this->rights > -1 OR $this->_check_confirm_code($activation_key) === FALSE)
			return FALSE;

		$this->rights = 0;
		$this->update();
		$this->_empty_tmp();
		return TRUE;
	}

	public function ask_for_reset_password($user_or_email)
	{
		$this->email = $user_or_email;
		if ($this->select_by('email') === FALSE)
		{
			$this->user_name = $user_or_email;
			$this->select_by('user_name');
		}

		$this->_set_confirm_code();
		return $this->_insert_tmp();
	}

	public function reset_password($confirm_code)
	{
		if ($this->_check_confirm_code($confirm_code) === FALSE)
			return FALSE;
		$this->update();
		$this->_empty_tmp();
		return TRUE;
	}

		/* update settings */
	public function update_user_name($user_name)
	{
		if ($this->_select_one('users', 'user_name', $user_name) !== FALSE)
			return FALSE;

		$this->user_name = $user_name;
		$this->update();
		return TRUE;
	}

	public function ask_for_update_email($email)
	{
		if ($this->_select_one('users', 'email', $email) !== FALSE)
			return FALSE;
		
		$this->tmp_email = $email;
		$this->_set_confirm_code();
		return $this->_insert_tmp();
	}

	public function update_email($confirm_code)
	{
		if ($this->_check_confirm_code($confirm_code) === FALSE)
			return FALSE;

		$this->_get_tmp();
		$this->email = $this->tmp_email;
		$this->update();
		$this->_empty_tmp();
		return TRUE;
	}

	public function update_password($old_pass, $new_pass)
	{
		if($this->_check_password($old_pass) === FALSE)
			return FALSE;
		$this->password($new_pass);
		$this->update();
		return TRUE;
	}

		/* sessions methods */
	public function login($password)
	{
		$this->select_by('user_name');
		if ($this->_check_password($password) === FALSE OR $this->user_data->rights < 0)
			return FALSE;
		$this->session->set_userdata(array(
			'user_id'			=> $this->ID,
		));
		return TRUE;
	}

	private function _check_password($password)
	{
		$this->load->helper('security');
		return check_hash($this->password, $password);
	}

		/* tmp_users methods */
	private function _insert_tmp()
	{
		if ( ! isset($this->ID) OR $this->_get_tmp() !== FALSE)
			return FALSE;
		$data = array(
			'user_id'				=> $this->ID,
			'confirm_code'	=> $this->confirm_code,
		);
		if (isset($this->tmp_email))
			$data['tmp_email'] = $this->tmp_email;
		
		$this->db->insert('tmp_users', $data);
		return TRUE;
	}

	private function _get_tmp()
	{
		$this->db->from('tmp_users')->where('user_id', $this->ID)->limit(1);
		$query = $this->db->get();
		if ($query->num_rows == 0)
			return FALSE;

		$tmp = $query->row();
		$this->confirm_code = $tmp->confirm_code;
		$this->tmp_email($tmp->tmp_email);
		return TRUE;
	}

	private function _check_confirm_code($confirm_code)
	{
		if ($this->_get_tmp() === TRUE)
		{
			return $this->confirm_code === $confirm_code;
		}
		
		return FALSE;
	}

	private function _empty_tmp()
	{
		$this->db->where('user_id', $this->ID)->delete('tmp_users');
	}

		/* get confirm link */
	public function get_confirm_link($controller, $id = TRUE)
	{
		return ($id === TRUE)
			? site_url("$controller/{$this->ID}/" . $this->confirm_code)
			: site_url("$controller/" . $this->confirm_code);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */ 