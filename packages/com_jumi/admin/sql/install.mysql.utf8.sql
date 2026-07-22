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

INSERT IGNORE INTO `#__jumi` (`id`, `title`, `alias`, `path`, `custom_script`, `access`, `published`) VALUES
(1, 'Hello Jumi!', 'hello-jumi', '', '<!-- Jumi intro including some php code (sitename, username) - see below. -->\r\n\r\n<?php\r\n$jumiApp = \\Joomla\\CMS\\Factory::getApplication();\r\n$jumiUser = $jumiApp->getIdentity();\r\n?>\r\n\r\n<h3>Hello in the world of Jumi!</h3>\r\n<p>Jumi is a set of Joomla! extensions enabling to include custom codes (html, php, css, js, ...) into Joomla!</p>\r\n<ul>\r\n<li>Jumi <b>module</b> includes codes into Joomla! module positions,</li>\r\n<li>Jumi <b>plugin</b> includes codes into Joomla! articles,</li>\r\n<li>Jumi <b>component</b> creates separate Joomla! components from custom codes.</li>\r\n</ul>\r\n<p>We hope Jumi will be useful for your <strong><?php echo htmlspecialchars((string) $jumiApp->get(\'sitename\')); ?></strong> site.</p>\r\n<p>Dear \r\n<?php\r\necho ($jumiUser && $jumiUser->name) ? htmlspecialchars($jumiUser->name) : \'unknown, not logged, friend\';\r\n?>\r\n!<br />Have a nice day, weeks, months and years with Jumi!</p>', 1, 1),
(2, 'Blogspot', 'blogspot', 'components/com_jumi/files/blogger.php', '<?php\r\n// Display a blog feed.\r\n// You can change following variables so you can display your own blog.\r\n$blogId = \'1748567850225926498\';\r\n$login = \'joomla-jumi\';\r\n$cacheTime = 86400;\r\n?>', 1, 1);
