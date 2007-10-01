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
