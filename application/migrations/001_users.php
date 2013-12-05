<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Users extends CI_Migration {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
	}

	public function up()
	{
		$fields = array(
			'ID' => array(
				'type'						=> 'INT',
				'constraint'			=> 9,
				'unsigned'				=> TRUE,
				'auto_increment'	=> TRUE,
				'null'						=> FALSE
			),
			'user_name' => array(
				'type'				=> 'VARCHAR',
				'constraint'	=> 20,
				'null'				=> FALSE,
				'default'			=> ''
			),
			'pass' => array(
				'type'				=> 'VARCHAR',
				'constraint'	=> 40,
				'null'				=> FALSE,
				'default'			=> ''
			),
			'email' => array(
				'type'				=> 'VARCHAR',
				'constraint'	=> 64,
				'null'				=> FALSE,
				'default'			=> ''
			),
			'activation_key' => array(
				'type'				=> 'VARCHAR',
				'constraint'	=> 15,
				'default'			=> NULL
			),
			'registration_time' => array(
				'type'		=> 'DATETIME',
				'null'		=> FALSE,
				'default'	=> '0000-00-00 00:00:00'
			),
			'rights' => array(
				'type'				=> 'INT',
				'constraint'	=> 1,
				'null'				=> FALSE,
				'default'			=> -1
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('ID', TRUE);
		$this->dbforge->add_key('user_name');
		$this->dbforge->add_key('email');
		$this->dbforge->create_table('users');
	}

	public function down()
	{
		$this->dbforge->drop_table('users');
	}
}

/* End of file 001_users.php */
/* Location: ./application/migrations/001_users.php */ 
 
