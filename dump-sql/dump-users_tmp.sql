CREATE TABLE IF NOT EXISTS `users_tmp` (
  `user_id` bigint(20) unsigned NOT NULL,
  `user_session_id` varchar(40) NOT NULL,
  `user_login` datetime NOT NULL,
  `user_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_session_id` (`user_session_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8; 
