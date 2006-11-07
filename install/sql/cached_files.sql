CREATE TABLE IF NOT EXISTS `cached_files` (
  `file` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `uri` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `original_uri` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `last_compile` int(11) NOT NULL default '0',
  `first_access` int(11) NOT NULL default '0',
  `last_access` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`file`),
  KEY `first_access` (`first_access`),
  KEY `last_access` (`last_access`),
  KEY `count` (`count`),
  KEY `uri` (`uri`),
  KEY `original_uri` (`original_uri`),
  KEY `last_compile` (`last_compile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
