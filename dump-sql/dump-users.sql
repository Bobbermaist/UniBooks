CREATE TABLE IF NOT EXISTS `users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(64) NOT NULL DEFAULT '',
  `tmp_pass` varchar(64) DEFAULT NULL,
  `user_email` varchar(64) NOT NULL DEFAULT '',
  `user_activation_key` varchar(15) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_rights` tinyint(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `users_login_key` (`user_login`),
  UNIQUE KEY `users_email` (`user_email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; 
