CREATE TABLE IF NOT EXISTS `links_book_category` (
  `book_id` int(9) unsigned NOT NULL  DEFAULT 0,
  `category_id` int(9) unsigned NOT NULL DEFAULT 0,
  FOREIGN KEY (book_id) REFERENCES books(ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(ID)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;