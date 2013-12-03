<?php

class User_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}

	public function insert_user($data)
	{
		$this->load->database();
		//todo: escape su user_name
		//$data['user_name'] = ...
		$this->db->insert('users', $data);
	}

	public function exists($field, $value)
	{
		$this->load->database();
		$this->db->from('users')->where($field, trim($value))->limit(1);
		return (boolean) $this->db->get()->num_rows();
	}

	public function select_where($field, $value)
	{
		$this->load->database();
		$this->db->from('users')->where($field, trim($value))->limit(1);
		return $this->db->get()->row();
	}

	public function update_by_ID($ID, $data)
	{
		$this->load->database();
		$this->db->where('ID', $ID);
		$this->db->update('users', $data);
	}

	public function set_user_tmp($data)
	{
		$query = $this->db->insert_string('users_tmp', $data) . " ON DUPLICATE KEY UPDATE 
			session_id='" . $data['session_id'] . "',
				login_time='" . $data['login_time'] ."', user_ip='" . $data['user_ip'] . "'";
		$this->db->query($query);
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_models.php */ 