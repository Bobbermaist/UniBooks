<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
	
	private $ID;

	private $user_name;

	private $password;

	private $email;

	private $registration_time;

	private $rights;

	private $confirm_code;

	private $tmp_email;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

		/* get / set methods */
	public function id($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->ID;
		}
		else
		{
			$this->ID = (int) $value;
			return $this->select();
		}
	}

	public function user_name($value = NULL)
	{
		return ($value === NULL)
			? $this->user_name
			: $this->user_name = $value;
	}

	public function password($value = NULL)
	{
		$this->load->helper('security');
		return ($value === NULL)
			? $this->password
			: $this->password = do_hash($value);
	}

	public function email($value = NULL)
	{
		return ($value === NULL)
			? $this->email
			: $this->email = $value;
	}

	public function registration_time()
	{
		return $this->registration_time;
	}

	public function rights()
	{
		return $this->rights;
	}

	public function confirm_code()
	{
		return $this->confirm_code;
	}

	public function tmp_email($value = NULL)
	{
		return ($value === NULL)
			? $this->tmp_email
			: $this->tmp_email = $value;
	}

	public function unset_all()
	{
		unset(
			$this->ID,
			$this->user_name,
			$this->password,
			$this->email,
			$this->registration_time,
			$this->rights,
			$this->confirm_code,
			$this->tmp_email
		);
	}

		/* private set methods */
	private function _set_confirm_code()
	{
		$this->load->helper('string');
		$this->confirm_code = random_string('alnum', 15);
	}

	private function _set_time()
	{
		$this->registration_time = date(
			$this->config->item('log_date_format'), 
			$_SERVER['REQUEST_TIME']
		);
	}

		/* base db methods */
	private function _exists($field, $value)
	{
		$query = $this->db->from('users')->where($field, $value)->limit(1)->get();
		return (boolean) $query->num_rows;
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

	public function select($field = 'ID')
	{
		$this->db->from('users');
			
		$this->db->where($field, $this->$field);
		$res = $this->db->get();

		if ($res->num_rows == 0)
		{
			$this->unset_all;
			return FALSE;
		}

		$user_data = $res->row();
		$this->ID = (int) $user_data->ID;
		$this->user_name = $user_data->user_name;
		$this->password = $user_data->password;
		$this->email = $user_data->email;
		$this->registration_time = $user_data->registration_time;
		$this->rights = (int) $user_data->rights;
		return TRUE;
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
		if ($this->select('email') === FALSE)
		{
			$this->user_name = $user_or_email;
			$this->select('user_name');
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

	private function _check_password($password)
	{
		$this->load->helper('security');
		return check_hash($this->password, $password);
	}

		/* update settings */
	public function update_user_name($user_name)
	{
		if ($this->_exists('user_name', $user_name))
			return FALSE;

		$this->user_name = $user_name;
		$this->update();
		return TRUE;
	}

	public function ask_for_update_email($email)
	{
		if ($this->_exists('email', $email))
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
		$this->select('user_name');
		if ($this->_check_password($password) === FALSE OR $this->user_data->rights < 0)
			return FALSE;
		$this->session->set_userdata(array(
			'user_id'			=> $this->ID,
		));
		return TRUE;
	}

	public function read_session()
	{
		$this->load->library('session');
		$user_id = $this->session->userdata('user_id');

		return ($user_id === FALSE)
			? FALSE
			: $this->id($user_id);
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