DROP TABLE cache;
CREATE TABLE IF NOT EXISTS `cache` (
  `type` bigint(20) unsigned default NULL,
  `key` bigint(20) unsigned default NULL,
  `uri` bigint(20) unsigned default NULL,
  `hmd` bigint(20) unsigned NOT NULL default '0',
  `value` longblob,
  `access_time` int(10) unsigned default NULL,
  `count` int(10) unsigned default NULL,
  `create_time` int(10) unsigned default NULL,
  `expire_time` int(10) unsigned default NULL,
  `rate` float default NULL,
  `saved_time` float default NULL,
  PRIMARY KEY  (`hmd`),
  KEY `type` (`type`),
  KEY `key` (`key`),
  KEY `access_time` (`access_time`),
  KEY `rate` (`rate`),
  KEY `create_time` (`create_time`),
  KEY `expire_time` (`expire_time`),
  KEY `uri` (`uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
