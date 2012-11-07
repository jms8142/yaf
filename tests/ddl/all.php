<?php



define('USERS_TABLE', " 
	DROP TABLE IF EXISTS `users`;
	CREATE TABLE `users` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `email` varchar(100) NOT NULL,
	  `password` varchar(50) NOT NULL,
	  `fname` varchar(100) NOT NULL,
	  `lname` varchar(100) NOT NULL,
	  UNIQUE KEY `id` (`id`),
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"
);