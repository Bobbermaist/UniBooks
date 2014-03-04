<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * UniBooks
 *
 * An application for books trade off
 *
 * @package UniBooks
 * @author Emiliano Bovetti
 * @since Version 1.0
 */

/**
 * UniBooks User_model Class
 *
 * @package UniBooks
 * @category Models
 * @author Emiliano Bovetti
 */
class User_model extends User_base {

	/**
	 * Constructor load the db
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Insert a user in the db.
	 * Object properties user_name, password and email must be setted.
	 *
	 * Set user rights to -1, the email must be confirmed before
	 * log in.
	 *
	 * @return void
	 */
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

	/**
	 * Update method updates user settings by ID.
	 *
	 * @return void
	 */
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
	
	/**
	 * Activate an account. Sets user rights to 0.
	 * The user can now log in.
	 *
	 * @param string
	 * @return boolean
	 */
	public function activate($activation_key)
	{
		if ( $this->rights > -1 OR $this->_check_confirm_code($activation_key) === FALSE)
			return FALSE;

		$this->rights = 0;
		$this->update();
		$this->_empty_tmp();
		return TRUE;
	}

	/**
	 * Set a reset password request.
	 * The request can be done by user name or email
	 * (because both are unique fields)
	 *
	 * If user ID is already present in the `tmp_users`
	 * return FALSE
	 *
	 * @param string
	 * @return boolean
	 */
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

	/**
	 * Reset the password if $confirm_code is correct.
	 * The password property must be setted with the new password.
	 *
	 * @param string
	 * @return boolean
	 */
	public function reset_password($confirm_code)
	{
		if ($this->_check_confirm_code($confirm_code) === FALSE)
			return FALSE;
		$this->update();
		$this->_empty_tmp();
		return TRUE;
	}

	/**
	 * Updates the user's name if this does not exists.
	 *
	 * @param string
	 * @return boolean
	 */
	public function update_user_name($user_name)
	{
		if ($this->_select_one('users', 'user_name', $user_name) !== FALSE)
			return FALSE;

		$this->user_name = $user_name;
		$this->update();
		return TRUE;
	}

	/**
	 * Set a request for update email.
	 * The $email must be unique (return FALSE otherwise)
	 * and the `tmp_users` must not contain the user's ID.
	 *
	 * @param string
	 * @return boolean
	 */
	public function ask_for_update_email($email)
	{
		if ($this->_select_one('users', 'email', $email) !== FALSE)
			return FALSE;
		
		$this->tmp_email = $email;
		$this->_set_confirm_code();
		return $this->_insert_tmp();
	}

	/**
	 * Updates the email if the $confirm_code is correct.
	 *
	 * @param string
	 * @return boolean
	 */
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

	/**
	 * Updates the password if $old_pass is correct
	 *
	 * @param string
	 * @param string
	 * @return boolean
	 */
	public function update_password($old_pass, $new_pass)
	{
		if($this->_check_password($old_pass) === FALSE)
			return FALSE;
		$this->password($new_pass);
		$this->update();
		return TRUE;
	}

	/**
	 * Log in.
	 * The user_name property must be setted.
	 *
	 * Sets the userdata. 
	 *
	 * @param string
	 * @return boolean
	 */
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

	/**
	 * Check the password through the security helper.
	 *
	 * @param string
	 * @return boolean
	 * @access private
	 */
	private function _check_password($password)
	{
		$this->load->helper('security');
		return check_hash($this->password, $password);
	}

	/**
	 * Insert data in `tmp_users` table.
	 * $this->ID and $this->confirm_code must be setted.
	 *
	 * return FALSE if already exists a row with user's ID.
	 *
	 * @return boolean
	 * @access private
	 */
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

	/**
	 * Get data from `tmp_users` table.
	 * $this->ID must be setted.
	 *
	 * @return boolean
	 * @access private
	 */
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

	/**
	 * Check if $confirm code corresponds with one stored in
	 * `tmp_users` table
	 *
	 * @param string
	 * @return boolean
	 * @access private
	 */
	private function _check_confirm_code($confirm_code)
	{
		if ($this->_get_tmp() === TRUE)
		{
			return $this->confirm_code === $confirm_code;
		}
		
		return FALSE;
	}

	/**
	 * Delete from `users_tmp` the row with $this->ID
	 *
	 * @return void
	 * @access private
	 */
	private function _empty_tmp()
	{
		$this->db->where('user_id', $this->ID)->delete('tmp_users');
	}

	/**
	 * Creates the confirm link with the confirm code
	 * ($this->confirm code must be setted).
	 * 
	 * $controller point to the controller wich confirm link
	 * redirect to.
	 *
	 * $id indicates if the confirm link needs the users id.
	 *
	 * @param string
	 * @param boolean
	 * @return string
	 */
	public function get_confirm_link($controller, $id = TRUE)
	{
		return ($id === TRUE)
			? site_url("$controller/{$this->ID}/" . $this->confirm_code)
			: site_url("$controller/" . $this->confirm_code);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */ 