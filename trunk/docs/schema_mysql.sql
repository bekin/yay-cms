CREATE TABLE IF NOT EXISTS `sitecontent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `authorid` varchar(255) DEFAULT NULL,
	`language` varchar(5) NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `keywords` varchar(255) NULL,
  `description` varchar(255) NULL,
  `title_url` varchar(80) NOT NULL,
  `title_browser` varchar(80) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`, `language`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

