CREATE TABLE IF NOT EXISTS `bors_search_words` (
  `id` INT UNSIGNED NOT NULL auto_increment,
  `word` VARCHAR(16) NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `word` (`word`)
);

CREATE TABLE IF NOT EXISTS `bors_search_titles` (
	`word_id` INT UNSIGNED NOT NULL,
	`class_name` VARCHAR(64) CHARACTER SET BINARY NOT NULL,
	`class_id` INT UNSIGNED NOT NULL ,
	`object_create_time` INT UNSIGNED NOT NULL,
	`object_modify_time` INT UNSIGNED NOT NULL,
	PRIMARY KEY ( `word_id` , `class_name` , `class_id` ),
	KEY (`object_create_time`),
	KEY (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_0` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_1` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_2` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_3` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_4` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_5` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_6` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_7` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_8` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

CREATE TABLE IF NOT EXISTS `bors_search_source_9` (
	`word_id` int(10) unsigned NOT NULL,
	`class_name` varbinary(64) NOT NULL,
	`class_id` int(10) unsigned NOT NULL,
	`class_page` int(4) unsigned NOT NULL,
	`count` int(5) unsigned NOT NULL,
	`object_create_time` int(10) unsigned NOT NULL,
	`object_modify_time` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`word_id`,`class_name`,`class_id`,`class_page`),
	KEY `object_create_time` (`object_create_time`),
	KEY `object_modify_time` (`object_modify_time`)
);

