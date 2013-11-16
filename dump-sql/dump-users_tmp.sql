CREATE TABLE IF NOT EXISTS `users_tmp` (
  `user_id` bigint(20) unsigned NOT NULL,
  `session_id` varchar(40) NOT NULL,
  `login_time` datetime NOT NULL,
  `user_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8; 
