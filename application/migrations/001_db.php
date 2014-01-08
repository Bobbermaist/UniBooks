<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('CHARSET', 'DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

class Migration_Db extends CI_Migration {

	public function __construct()
	{
		//parent::__construct();
		$this->load->database();
		//$this->load->dbforge();
	}

	public function up()
	{
		$this->set_db_utf8();
		$this->users_up();
		$this->tmp_users_up();
		$this->ci_sessions_up();
		$this->languages_up();
		$this->categories_up();
		$this->publishers_up();
		$this->authors_up();
		$this->books_up();
		$this->links_author_up();
		$this->links_category_up();
		$this->books_for_sale_up();
		$this->books_requested_up();
		$this->language_groups_up();
		$this->publisher_codes_up();
		$this->google_search_keys_up();
		$this->google_results_up();
	}

	public function down()
	{
		$this->books_for_sale_down();
		$this->books_requested_down();
		$this->users_down();
		$this->tmp_users_down();
		$this->ci_sessions_down();
		$this->links_author_down();
		$this->links_category_down();
		$this->books_down();
		$this->languages_down();
		$this->categories_down();
		$this->publishers_down();
		$this->authors_down();
		$this->language_groups_down();
		$this->publisher_codes_down();
		$this->google_search_keys_down();
		$this->google_results_down();
	}

		/* UTF-8 */
	private function set_db_utf8()
	{
		$query = 'ALTER DATABASE `' . $this->db->database . '`
								CHARACTER SET utf8
								DEFAULT CHARACTER SET utf8
								COLLATE utf8_unicode_ci
								DEFAULT COLLATE utf8_unicode_ci;';
		$this->db->query($query);
	}

		/* Users database */
	private function users_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `users` (
							`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
							`user_name` varchar(20) NOT NULL DEFAULT '',
							`pass` varchar(60) NOT NULL DEFAULT '',
							`email` varchar(64) NOT NULL DEFAULT '',
							`registration_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
							`rights` tinyint(1) NOT NULL DEFAULT '-1',
							PRIMARY KEY (`ID`),
							UNIQUE KEY `user_name` (`user_name`),
							UNIQUE KEY `email` (`email`)
						) ENGINE=InnoDB " . CHARSET . " AUTO_INCREMENT=1;";
		$this->db->query($query);

		$query = "INSERT INTO `users` (`ID`, `user_name`, `pass`, `email`, `registration_time`, `rights`)
							VALUES (1, 'bob', '\$2a\$08\$HIRyxB7T8zohpHt25DPKSu.AOuUKkjl2ImYTj9NEanT/IYRR.JP3G', 'emilianobovetti@hotmail.it', '2013-12-18 12:53:40', 1);";
		$this->db->query($query);
	}

	private function users_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `users`;');
	}

	private function tmp_users_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `tmp_users` (
							`user_id` int(9) unsigned NOT NULL DEFAULT 0,
							`confirm_code` varchar(15) DEFAULT NULL,
							`tmp_email` varchar(64) DEFAULT NULL,
							PRIMARY KEY (`user_id`)
						) ENGINE=MyISAM " . CHARSET . ";";
		$this->db->query($query);
	}

	private function tmp_users_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `tmp_users`;');
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
						) ENGINE=MyISAM " . CHARSET . ";";
		$this->db->query($query);
	}

	private function ci_sessions_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `ci_sessions`;');
	}

		/* Books database */
	private function books_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `books` (
							`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
							`ISBN` varchar(9) NOT NULL DEFAULT '000000000',
							`google_id` varchar(12) DEFAULT NULL,
							`title` varchar(255) NOT NULL DEFAULT '',
							`publisher_id` int(9) unsigned NOT NULL DEFAULT 0,
							`publication_year` int(4) DEFAULT NULL,
							`pages` int(5) unsigned DEFAULT NULL,
							`language_id` int(5) unsigned NOT NULL DEFAULT 0,
							PRIMARY KEY (`ID`),
							FOREIGN KEY (publisher_id) REFERENCES publishers(ID)
								ON DELETE NO ACTION
								ON UPDATE NO ACTION,
							FOREIGN KEY (language_id) REFERENCES languages(ID)
								ON DELETE NO ACTION
								ON UPDATE NO ACTION
						) ENGINE=InnoDB " . CHARSET . " AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function books_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `books`;');
	}

	private function authors_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `authors` (
							`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
							`name` varchar(255) NOT NULL DEFAULT '',
							PRIMARY KEY (`ID`)
						) ENGINE=InnoDB " . CHARSET . " AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function authors_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `authors`;');
	}

	private function links_author_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `links_book_author` (
							`book_id` int(9) unsigned NOT NULL  DEFAULT 0,
							`author_id` int(9) unsigned NOT NULL DEFAULT 0,
							UNIQUE KEY `author` (`book_id`, `author_id`),
							FOREIGN KEY (book_id) REFERENCES books(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE,
							FOREIGN KEY (author_id) REFERENCES authors(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE
						) ENGINE=InnoDB " . CHARSET . ";";
		$this->db->query($query);
	}

	private function links_author_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `links_book_author`;');
	}

	private function publishers_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `publishers` (
							`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
							`name` varchar(255) NOT NULL DEFAULT '',
							PRIMARY KEY (`ID`)
						) ENGINE=InnoDB " . CHARSET . " AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function publishers_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `publishers`;');
	}

	private function categories_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `categories` (
							`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
							`name` varchar(255) NOT NULL DEFAULT '',
							PRIMARY KEY (`ID`)
						) ENGINE=InnoDB " . CHARSET . " AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function categories_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `categories`;');
	}

	private function links_category_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `links_book_category` (
							`book_id` int(9) unsigned NOT NULL  DEFAULT 0,
							`category_id` int(9) unsigned NOT NULL DEFAULT 0,
							UNIQUE KEY `category` (`book_id`, `category_id`),
							FOREIGN KEY (book_id) REFERENCES books(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE,
							FOREIGN KEY (category_id) REFERENCES categories(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE
						) ENGINE=InnoDB " . CHARSET . ";";
		$this->db->query($query);
	}

	private function links_category_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `links_book_category`;');
	}

	private function languages_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `languages` (
							`ID` int(5) unsigned NOT NULL AUTO_INCREMENT,
							`name` varchar(255) NOT NULL DEFAULT '',
							PRIMARY KEY (`ID`)
						) ENGINE=InnoDB " . CHARSET . " AUTO_INCREMENT=1;";
		$this->db->query($query);
	}

	private function languages_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `languages`;');
	}

	private function books_for_sale_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `books_for_sale` (
							`user_id` int(9) unsigned NOT NULL DEFAULT 0,
							`book_id` int(9) unsigned NOT NULL DEFAULT 0,
							`price` float(4,2) NOT NULL DEFAULT '0.00',
							UNIQUE KEY `selling` (`user_id`, `book_id`),
							FOREIGN KEY (user_id) REFERENCES users(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE,
							FOREIGN KEY (book_id) REFERENCES books(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE
						) ENGINE=InnoDB " . CHARSET . ";";
		$this->db->query($query);
	}

	private function books_for_sale_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `books_for_sale`;');
	}

	private function books_requested_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `books_requested` (
							`user_id` int(9) unsigned NOT NULL DEFAULT 0,
							`book_id` int(9) unsigned NOT NULL DEFAULT 0,
							FOREIGN KEY (user_id) REFERENCES users(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE,
							FOREIGN KEY (book_id) REFERENCES books(ID)
								ON DELETE CASCADE
								ON UPDATE CASCADE
						) ENGINE=InnoDB " . CHARSET . ";";
		$this->db->query($query);
	}

	private function books_requested_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `books_requested`;');
	}

	private function language_groups_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `language_groups` (
							`code` varchar(5) NOT NULL DEFAULT '0',
							`name` varchar(128) NOT NULL DEFAULT '',
							PRIMARY KEY (`code`)
						) ENGINE=InnoDB " . CHARSET . ";";
		$this->db->query($query);
		$pop_path = APPPATH . 'migrations/populate_language_groups.sql';
		$pop_open = fopen($pop_path, 'r');
		$populate = fread($pop_open, filesize($pop_path));
		fclose($pop_open);
		$this->db->query($populate);
	}

	private function language_groups_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `language_groups`;');
	}

	private function publisher_codes_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `publisher_codes` (
							`code` varchar(7) NOT NULL DEFAULT '0',
							`name` varchar(255) NOT NULL DEFAULT '',
							PRIMARY KEY (`code`)
						) ENGINE=InnoDB " . CHARSET . ";";
		$this->db->query($query);
		$pop_path = APPPATH . 'migrations/populate_publisher_codes_from_wikipedia.sql';
		$pop_open = fopen($pop_path, 'r');
		$populate_from_wikipedia = fread($pop_open, filesize($pop_path));
		fclose($pop_open);
		$pop_path = APPPATH . 'migrations/populate_publisher_codes_from_books-by-isbn.com.sql';
		$pop_open = fopen($pop_path, 'r');
		$populate_from_books = fread($pop_open, filesize($pop_path));
		fclose($pop_open);

		$this->db->query($populate_from_wikipedia);
		$this->db->query($populate_from_books);
	}

	private function publisher_codes_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `publisher_codes`;');
	}

	private function google_search_keys_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `google_search_keys` (
							`ID` int(9) unsigned NOT NULL AUTO_INCREMENT,
							`search_key` varchar(255) NOT NULL DEFAULT '',
							`total_items` int(5) unsigned NOT NULL DEFAULT 0,
							PRIMARY KEY (`ID`),
							UNIQUE KEY `search_key` (`search_key`)
						) ENGINE=MyISAM " . CHARSET . ";";
		$this->db->query($query);
	}

	private function google_search_keys_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `google_search_keys`;');
	}

	private function google_results_up()
	{
		$query = "CREATE TABLE IF NOT EXISTS `google_results` (
							`search_id` int(9) unsigned NOT NULL DEFAULT 0,
							`index` int(5) unsigned NOT NULL DEFAULT 0,
							`results` text NOT NULL
						) ENGINE=MyISAM " . CHARSET . ";";
		$this->db->query($query);
	}

	private function google_results_down()
	{
		$this->db->query('DROP TABLE IF EXISTS `google_results`;');
	}
}

/* End of file 001_db.php */
/* Location: ./application/migrations/001_db.php */ 
