CREATE TABLE IF NOT EXISTS `bors_uris` (
	`id` INT UNSIGNED NOT NULL auto_increment,
    `uri` varchar(255) NOT NULL default '',
	`class_name` varchar(160) NOT NULL default '',
	`class_id` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `uri` (`uri`)
);
		        