CREATE TABLE IF NOT EXISTS `#__jumi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) NOT NULL DEFAULT '',
  `path` varchar(255) DEFAULT NULL,
  `custom_script` mediumtext,
  `access` int(11) unsigned NOT NULL DEFAULT 1,
  `checked_out` int(11) unsigned DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `published` tinyint(3) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
