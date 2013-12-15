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
		$this->languages_up();
		$this->categories_up();
		$this->publishers_up();
		$this->authors_up();
		$this->books_up();
		$this->links_author_up();
		$this->links_category_up();
	}

	public function down()
	{
		$this->users_down();
		$this->ci_sessions_down();
		$this->links_author_down();
		$this->links_category_down();
		$this->books_down();
		$this->languages_down();
		$this->categories_down();
		$this->publishers_down();
		$this->authors_down();
	}

		/* Users database */
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

		/* Sessions database */
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

		/* Books database */
	private function books_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `books` (
  						`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
  						`ISBN` varchar(9) NOT NULL DEFAULT '000000000',
  						`title` varchar(255) NOT NULL DEFAULT '',
  						`publisher_id` int(9) unsigned NOT NULL  DEFAULT 0,
  						`publication_year` year(4) DEFAULT NULL,
  						`pages` int(5) unsigned DEFAULT NULL,
  						`language_id` int(5) unsigned NOT NULL  DEFAULT 0,
  						PRIMARY KEY (`ID`),
 							FOREIGN KEY (publisher_id) REFERENCES publishers(ID)
    						ON DELETE NO ACTION
    						ON UPDATE NO ACTION,
  						FOREIGN KEY (language_id) REFERENCES languages(ID)
    						ON DELETE NO ACTION
    						ON UPDATE NO ACTION
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function books_down()
	{
		$this->db->query('DROP TABLE `books`;');
	}

	private function authors_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `authors` (
  						`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
  						`name` varchar(255) NOT NULL DEFAULT '',
 							PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function authors_down()
	{
		$this->db->query('DROP TABLE `authors`;');
	}

	private function links_author_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `links_book_author` (
  						`book_id` int(9) unsigned NOT NULL  DEFAULT 0,
  						`author_id` int(9) unsigned NOT NULL DEFAULT 0,
  						FOREIGN KEY (book_id) REFERENCES books(ID)
    						ON DELETE CASCADE
    						ON UPDATE CASCADE,
  						FOREIGN KEY (author_id) REFERENCES authors(ID)
    						ON DELETE CASCADE
    						ON UPDATE CASCADE
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($query);
	}

	private function links_author_down()
	{
		$this->db->query('DROP TABLE `links_book_author`;');
	}

	private function publishers_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `publishers` (
  						`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
  						`name` varchar(255) NOT NULL DEFAULT '',
  						PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function publishers_down()
	{
		$this->db->query('DROP TABLE `publishers`;');
	}

	private function categories_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `categories` (
  						`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
  						`name` varchar(255) NOT NULL DEFAULT '',
  						PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function categories_down()
	{
		$this->db->query('DROP TABLE `categories`;');
	}

	private function links_category_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `links_book_category` (
  						`book_id` int(9) unsigned NOT NULL  DEFAULT 0,
  						`category_id` int(9) unsigned NOT NULL DEFAULT 0,
  						FOREIGN KEY (book_id) REFERENCES books(ID)
    						ON DELETE CASCADE
    						ON UPDATE CASCADE,
  						FOREIGN KEY (category_id) REFERENCES categories(ID)
    						ON DELETE CASCADE
    						ON UPDATE CASCADE
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($query);
	}

	private function links_category_down()
	{
		$this->db->query('DROP TABLE `links_book_category`;');
	}

	private function languages_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `languages` (
  						`ID` int(5) unsigned NOT NULL AUTO_INCREMENT,
  						`name` varchar(255) NOT NULL DEFAULT '',
  						PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function languages_down()
	{
		$this->db->query('DROP TABLE `languages`;');
	}
}

/* End of file 001_db.php */
/* Location: ./application/migrations/001_db.php */ 
