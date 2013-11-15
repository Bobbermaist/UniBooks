CREATE TABLE IF NOT EXISTS `authors` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_first_name` varchar(255) NOT NULL DEFAULT '',
  `author_last_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;