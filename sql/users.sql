CREATE TABLE `users` (
    `id` int(11) NOT NULL ,
    `oauth_provider` TEXT CHECK( oauth_provider IN ('github', 'facebook', 'google', 'twitter')) NOT NULL DEFAULT 'github',
    `oauth_uid` varchar(50) NOT NULL,
    `name` varchar(50) NOT NULL,
    `username` varchar(50) NOT NULL,
    `email` varchar(100)  NOT NULL,
    `location` varchar(50)  DEFAULT NULL,
    `picture` varchar(255)  DEFAULT NULL,
    `link` varchar(255)  DEFAULT NULL,
    `created` TEXT NOT NULL DEFAULT (DATETIME('now')),
    `modified` TEXT NOT NULL DEFAULT (DATETIME('now')),
    PRIMARY KEY (`id`)
);