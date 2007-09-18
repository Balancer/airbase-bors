CREATE TABLE IF NOT EXISTS `cache_groups` (
  `id` int(10) unsigned NOT NULL,
  `cache_group` varchar(128) NOT NULL,
  `class_name` varchar(64) NOT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cache_group` (`cache_group`,`class_name`,`class_id`)
);
