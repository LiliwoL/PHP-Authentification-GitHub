CREATE TABLE `users` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `oauth_provider` enum('github','facebook','google','twitter') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'github',
     `oauth_uid` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
     `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
     `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
     `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
     `location` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
     `picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
     `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
     `created` datetime NOT NULL DEFAULT current_timestamp(),
     `modified` datetime NOT NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;