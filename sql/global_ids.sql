CREATE TABLE IF NOT EXISTS `global_ids` (
	`id` int(11) NOT NULL auto_increment,
	`engine` enum('unknown','post','topic','page','forum','attach','image') NOT NULL default 'unknown',
	PRIMARY KEY  (`id`),
	KEY `engine_idx` (`engine`)
) ENGINE=MyISAM;
