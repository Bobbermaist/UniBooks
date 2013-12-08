CREATE TABLE IF NOT EXISTS `books` (
  `ISBN` varchar(9) NOT NULL DEFAULT '000000000',
  `title` varchar(255) NOT NULL DEFAULT '',
  `publisher_id` varchar(7) NOT NULL DEFAULT '0000000',
  `price` float(4,2) NOT NULL DEFAULT '0.00',
  `publication_year` year(4) DEFAULT NULL,
  `edition` smallint(3) NOT NULL DEFAULT 1,
  `volume` varchar(3) NOT NULL DEFAULT 'U',
  `subject_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ISBN`),
  FOREIGN KEY (id_publisher) REFERENCES publishers(ID)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  FOREIGN KEY (id_subject) REFERENCES subjects(ID)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
