CREATE TABLE IF NOT EXISTS `publishers` (
  `ID` varchar(7) NOT NULL DEFAULT '0000000',
  `publisher_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
