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

	public function select_user($user_name)
	{
		$this->load->database();
		$this->db->from('users')->where('user_name', $user_name)->limit(1);
		//$select = $this->db->get_where('users', array('user_name' => $user_name), 1);
		//if( $select->num_rows() == 0 )
		//	return NULL;
		return $this->db->get()->row();
	}

	public function update_rights($ID, $rights)
	{
		$this->load->database();
		$this->db->where('ID', $ID);
		$this->db->update('users', array( 'rights' => $rights ));
	}

	public function update_activation_key($ID, $activation_key)
	{
		$this->load->database();
		$this->db->where('ID', $ID);
		$this->db->update('users', array( 'activation_key' => $activation_key ));
	}
}

/* End of file user_model.php */
/* Location: ./application/models/user_models.php */ 