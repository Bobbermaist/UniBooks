<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Book_model extends Book_base {

	public function __construct()
	{
		parent::__construct();
	}

	public function search()
	{
		if (isset($this->ISBN))
		{
			return $this->_search_by_ISBN();
		}
	}

	private function _search_by_ISBN()
	{
		if ($this->select_by('ISBN') === FALSE)
		{
			$this->load->library('google_books');
			$this->google_books->get_by_isbn($this->ISBN);
			if ($this->google_books->total_items === 0)
			{
				return FALSE;
			}
			$this->_set_from_google();
			$this->insert();
		}
		return TRUE;
	}

	private function _set_from_google()
	{
		$book_data = $this->google_books->volumes[0];

		if ( ! isset($this->ISBN))
		{
			$this->ISBN = $book_data['ISBN'];
		}
		$this->google_id = $book_data['google_id'];
		$this->title = $book_data['title'];
		$this->authors = $book_data['authors'];
		if ($book_data['publisher'] === NULL)
		{
			$this->publisher = $this->_get_publisher();
		}
		else
		{
			$this->publisher = $book_data['publisher'];
		}
		$this->publication_year = $book_data['publication_year'];
		$this->pages = $book_data['pages'];
		$this->language = $book_data['language'];
		$this->categories = $book_data['categories'];
	}
}

/* End of file book_model.php */
/* Location: ./application/models/book_model.php */
