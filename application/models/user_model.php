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
 * UniBooks User_model class.
 *
 * Extends User_base class and provides all 
 * functionalities to manage users
 *
 * @package UniBooks
 * @category Models
 * @author Emiliano Bovetti
 */
class User_model extends User_base {

	/**
	 * Constructor
	 *
	 * Loads the db
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
	 * Set user rights to UNCONFIRMED_ACCOUNT, the email must be confirmed before
	 * log in.
	 *
	 * @return void
	 */
	public function insert()
	{
		$this->_set_confirm_code();
		$this->_set_time();
		$this->rights = UNCONFIRMED_ACCOUNT;

		$this->db->insert('users', array(
			'user_name'					=> $this->user_name,
			'password'					=> $this->password,
			'email'							=> $this->email,
			'registration_time'	=> $this->registration_time,
			'rights'						=> $this->rights,
		));
		$this->ID = (int) $this->db->insert_id();

		$this->_insert_tmp(TRUE);
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
	 * Activate an account. Sets user rights to standard USER_RIGHTS.
	 * The user can now log in.
	 *
	 * *Throws an exception* if the activation code is incorrect or if
	 * the account is already activated
	 *
	 * @param string  $activation_key the key needed to activate the account
	 * @return void
	 * @throws Custom_exception(ACCOUNT_ALREADY_CONFIRMED)
	 *    if user rights are greater than UNCONFIRMED_ACCOUNT
	 * @throws Custom_exception(WRONG_CONFIRM_CODE)
	 *    if the $actiovation_key does not match with the stored one
	 */
	public function activate($activation_key)
	{
		if ($this->rights > UNCONFIRMED_ACCOUNT)
		{
			throw new Custom_exception(ACCOUNT_ALREADY_CONFIRMED);
		}

		if ($this->_check_confirm_code($activation_key) === FALSE)
		{
			throw new Custom_exception(WRONG_CONFIRM_CODE);
		}

		// updating rights
		$this->rights = USER_RIGHTS;
		$this->update();
		$this->_empty_tmp();
	}

	/**
	 * Set a reset password request.
	 * The request can be done by user name or email
	 * (because both are unique fields)
	 *
	 * *Throws an exception* if the $user_or_email parameter
	 * does not match neither a user name nor email address.
	 *
	 * The `_insert_tmp()` method will throws an exception
	 * if the user ha not confirmed his account yet.
	 *
	 * @param string  $user_or_email user name or email address
	 * @return void
	 * @throws Custom_exception(NEITHER_USER_NOR_EMAIL) if the provided
	 *    parameter does not match with any user name or email
	 */
	public function ask_for_reset_password($user_or_email)
	{
		try
		{
			$this->set_email($user_or_email);
			$this->select_by('email');
		}
		catch (Custom_exception $e)
		{
			try
			{
				$this->set_user_name($user_or_email);
				$this->select_by('user_name');
			}
			catch (Custom_exception $e)
			{
				throw new Custom_exception(NEITHER_USER_NOR_EMAIL);
			}
		}

		$this->_set_confirm_code();
		$this->_insert_tmp();
	}

	/**
	 * Reset the password if $confirm_code is correct.
	 * The password property must be setted with the new password.
	 *
	 * *Throws an exception* if the confirm code is wrong
	 *
	 * @param string  $confirm_code the code needed to confirm the reset password
	 * @param strin  $new_pass new password (not hashed)
	 * @return void
	 * @throws Custom_exception(WRONG_CONFIRM_CODE)
	 *    if $confirm_code does not match with the stored one
	 */
	public function reset_password($confirm_code, $new_pass)
	{
		if ($this->_check_confirm_code($confirm_code) === FALSE)
		{
			throw new Custom_exception(WRONG_CONFIRM_CODE);
		}

		$this->set_password($new_pass);
		$this->update();
		$this->_empty_tmp();
	}

	/**
	 * Updates the user's name if this does not exists.
	 *
	 * @param string  $user_name new user name
	 * @return void
	 * @throws Custom_exception(EXISTING_USER_NAME) if $user_name
	 *    already exists
	 */
	public function update_user_name($user_name)
	{
		if ($this->_select_one('users', 'user_name', $user_name) !== FALSE)
		{
			throw new Custom_exception(EXISTING_USER_NAME);
		}

		$this->set_user_name($user_name);
		$this->update();
	}

	/**
	 * Set a request for update email.
	 *
	 * The $email must be unique (return FALSE otherwise)
	 * and the `tmp_users` must not contain the user's ID.
	 *
	 * `_insert_tmp()` method will throws an exception if
	 * an user not confirmed yet tries to update his email
	 *
	 * @param string  $email new email address (to confirm)
	 * @return void
	 * @throws throw new Custom_exception(EXISTING_EMAIL) if $email
	 *    already exists
	 */
	public function ask_for_update_email($email)
	{
		if ($this->_select_one('users', 'email', $email) !== FALSE)
		{
			throw new Custom_exception(EXISTING_EMAIL);
		}
		
		$this->tmp_email = $email;
		$this->_set_confirm_code();
		$this->_insert_tmp();
	}

	/**
	 * Updates the email if the $confirm_code is correct.
	 *
	 * @param string  $confirm_code the code needed to confirm the email address
	 * @return void
	 * @throws Custom_exception(WRONG_CONFIRM_CODE) if the provided
	 *    confirm code does not match
	 */
	public function update_email($confirm_code)
	{
		if ($this->_check_confirm_code($confirm_code) === FALSE)
		{
			throw new Custom_exception(WRONG_CONFIRM_CODE);
		}

		$this->_get_tmp();
		$this->set_email($this->tmp_email);
		$this->update();
		$this->_empty_tmp();
	}

	/**
	 * Updates the password if $old_pass is correct
	 *
	 * @param string  $old_pass old account password
	 * @param string  $new_pass new password
	 * @return void
	 * @throws Custom_exception(WRONG_PASSWORD) if the
	 *    provided password does not match
	 */
	public function update_password($old_pass, $new_pass)
	{
		if($this->_check_password($old_pass) === FALSE)
		{
			throw new Custom_exception(WRONG_PASSWORD);
		}

		$this->set_password($new_pass);
		$this->update();
	}

	/**
	 * Log trough user name and password.
	 * Sets the userdata. 
	 *
	 * @param string  $user_name the user name
	 * @param string  $password password
	 * @return void
	 * @throws Custom_exception(ACCOUNT_NOT_CONFIRMED) if
	 *    a non confirmed user tries to log in
	 * @throws Custom_exception(WRONG_PASSWORD) if the
	 *    provided password does not match
	 */
	public function login($user_name, $password)
	{
		$this->set_user_name($user_name);
		$this->select_by('user_name');
		if ($this->rights < USER_RIGHTS)
		{
			throw new Custom_exception(ACCOUNT_NOT_CONFIRMED);
		}
		if ($this->_check_password($password) === FALSE)
		{
			throw new Custom_exception(WRONG_PASSWORD);
		}

		$this->add_userdata('user_id', $this->ID);
	}

	/**
	 * Log out.
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->del_userdata();
		$this->unset_all();
		redirect('/');
	}

	/**
	 * Add an item to userdata.
	 *
	 * The first parameter can be an associative array or a string.
	 * 
	 * If an associative array is passed to this method,
	 * the second parameter should be NULL (default value).
	 *
	 * Otherwise $value should contain the value 
	 * to add.
	 *
	 * @param mixed  $data associative array or a string indicating the name of the data to add
	 * @param mixed  $value if $data is a string contains the value to add
	 * @return void
	 */
	public function add_userdata($data, $value = NULL)
	{
		if ( ! is_array($data))
		{
			$data = array($data => $value);
		}

		$this->session->set_userdata($data);
	}

	/**
	 * Retrieve userdata.
	 *
	 * If invoked without parameters returns an associative
	 * array with all userdata. FALSE if not setted.
	 *
	 * Otherwise if called with a string as parameter
	 * return the userdata relative to this string. (or FALSE)
	 *
	 * @param mixed  $item the item to retrieve or NULL if all items needed
	 * @return mixed 
	 */
	public function userdata($item = NULL)
	{
		if ($item === NULL)
		{
			return $this->session->all_userdata();
		}
		return $this->session->userdata($item);
	}

	/**
	 * Delete userdata.
	 *
	 * If the parametere is NULL, this method will destroy all
	 * userdata.
	 * If is a string will delete the element required.
	 * If is an array will delete all elements.
	 *
	 * @param mixed  $items the item to delete or NULL if all items should be deleted
	 * @return void
	 */
	public function del_userdata($items = NULL)
	{
		if ($items === NULL)
		{
			$this->session->sess_destroy();
		}
		elseif(is_string($items))
		{
			$this->session->unset_userdata($items);
		}
		else
		{
			$array_items = array();
			foreach ($items as $item)
			{
				$array_items[$item] = '';
			}
			$this->session->unset_userdata($array_items);
		}
	}

	/**
	 * Check the password through the security helper.
	 *
	 * @param string  $password the password (not hashed) to compare with 'password' property
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
	 * The parameter $registration shuld be TRUE
	 * only for registration.
	 *
	 * @param boolean  $registration indicates whether you are making a registration
	 * @return void
	 * @access private
	 * @throws Custom_exception(ACCOUNT_NOT_CONFIRMED) if an user not confirmed
	 *    tries to overwrite tmp data (e.g. tries to reset his password)
	 */
	private function _insert_tmp($registration = FALSE)
	{
		// Account not confirmed. Won't overwrite user tmp
		if ($this->rights === UNCONFIRMED_ACCOUNT AND $registration === FALSE)
		{
			throw new Custom_exception(ACCOUNT_NOT_CONFIRMED);
		}

		$data = array(
			'user_id'				=> $this->ID,
			'confirm_code'	=> $this->confirm_code,
		);
		if (isset($this->tmp_email))
		{
			$data['tmp_email'] = $this->tmp_email;
		}

		$this->_insert_on_duplicate('tmp_users', $data);
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
		if ($query->num_rows === 0)
		{
			return FALSE;
		}

		$tmp = $query->row();
		$this->confirm_code = $tmp->confirm_code;
		$this->tmp_email = $tmp->tmp_email;
		return TRUE;
	}

	/**
	 * Check if $confirm code corresponds with one stored in
	 * `tmp_users` table
	 *
	 * @param string  $confirm_code the code to compare with 'confirm_code' property
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
	 * @param string  $controller the controller wich confirm link redirect to
	 * @param boolean  $id indicates whether the link should contains the user id
	 * @return string
	 */
	public function get_confirm_link($controller, $id = TRUE)
	{
		return ($id === TRUE)
			? site_url("$controller/{$this->ID}/" . $this->confirm_code)
			: site_url("$controller/" . $this->confirm_code);
	}
}

// END User_model class

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */ 