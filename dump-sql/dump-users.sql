CREATE TABLE IF NOT EXISTS `users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `pass` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `activation_key` varchar(15) NOT NULL DEFAULT '',
  `registration_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rights` tinyint(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; 
