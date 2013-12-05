<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Db extends CI_Migration {

	public function __construct()
	{
		//parent::__construct();
		$this->load->database();
		$this->load->dbforge();
	}

	public function up()
	{
		$this->users_up();
		$this->ci_sessions_up();
	}

	public function down()
	{
		$this->users_down();
		$this->ci_sessions_down();
	}

	private function users_up()
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

	private function users_down()
	{
		$this->dbforge->drop_table('users');
	}

	private function ci_sessions_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `ci_sessions` (
  						`session_id` varchar(40) NOT NULL DEFAULT '0',
  						`ip_address` varchar(45) NOT NULL DEFAULT '0',
  						`user_agent` varchar(120) NOT NULL,
  						`last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  						`user_data` varchar(255) DEFAULT NULL,
  						PRIMARY KEY (`session_id`),
  						KEY `last_activity_idx` (`last_activity`)
						) ENGINE=MEMORY DEFAULT CHARSET=utf8;";
		$this->db->query($query);
	}

	private function ci_sessions_down()
	{
		$this->db->query('DROP TABLE `ci_sessions`;');
	}

}

/* End of file 001_db.php */
/* Location: ./application/migrations/001_db.php */ 
