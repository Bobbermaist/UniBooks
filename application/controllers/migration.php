<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->library('migration');
		if ( ! $this->migration->current() )
			show_error($this->migration->error_string());
		else
			echo 'Current migration effettuata correttamente';
	}

	public function down()
	{
		$this->load->database();
		$this->db->query('DROP TABLE IF EXISTS `users`;');
		$this->db->query('DROP TABLE IF EXISTS `ci_sessions`;');
		$this->db->query('DROP TABLE IF EXISTS `links_book_author`;');
		$this->db->query('DROP TABLE IF EXISTS `links_book_category`;');
		$this->db->query('DROP TABLE IF EXISTS `books`;');
		$this->db->query('DROP TABLE IF EXISTS `languages`;');
		$this->db->query('DROP TABLE IF EXISTS `categories`;');
		$this->db->query('DROP TABLE IF EXISTS `publishers`;');
		$this->db->query('DROP TABLE IF EXISTS `authors`;');
		$this->db->query('DROP TABLE IF EXISTS `migrations`;');
		echo 'Tutte le tabelle sono state eliminate';
	}
}

/* End of file migration.php */
/* Location: ./application/controllers/migration.php */ 
 
 
