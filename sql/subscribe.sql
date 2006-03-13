CREATE TABLE IF NOT EXISTS `hts_data_subscribe` (
  `id` varchar(166) NOT NULL default '',
  `value` varchar(255) character set utf8 NOT NULL default '',
  `visited` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`value`),
  KEY `id` (`id`),
  KEY `updated` (`visited`),
  FULLTEXT KEY `value` (`value`)
);
