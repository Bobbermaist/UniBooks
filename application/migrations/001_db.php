<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Db extends CI_Migration {

	public function __construct()
	{
		//parent::__construct();
		$this->load->database();
		//$this->load->dbforge();
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
		$query = "CREATE TABLE IF NOT EXISTS `users` (
  						`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
  						`user_name` varchar(20) NOT NULL DEFAULT '',
  						`pass` varchar(40) NOT NULL DEFAULT '',
 							`email` varchar(64) NOT NULL DEFAULT '',
  						`activation_key` varchar(15) DEFAULT NULL,
  						`registration_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  						`rights` tinyint(1) NOT NULL DEFAULT '-1',
  						PRIMARY KEY (`ID`),
  						UNIQUE KEY `user_name` (`user_name`),
  						UNIQUE KEY `email` (`email`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function users_down()
	{
		$this->db->query('DROP TABLE `users`;');
	}

	private function ci_sessions_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `ci_sessions` (
  						`session_id` varchar(40) NOT NULL DEFAULT '0',
  						`ip_address` varchar(45) NOT NULL DEFAULT '0',
  						`user_agent` varchar(120) NOT NULL,
  						`last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  						`user_data` text NOT NULL,
  						PRIMARY KEY (`session_id`),
  						KEY `last_activity_idx` (`last_activity`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($query);
	}

	private function ci_sessions_down()
	{
		$this->db->query('DROP TABLE `ci_sessions`;');
	}

}

/* End of file 001_db.php */
/* Location: ./application/migrations/001_db.php */ 
