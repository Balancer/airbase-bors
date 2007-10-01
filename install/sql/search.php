<?php

$_SERVER['DOCUMENT_ROOT'] = "/var/www/localhost/htdocs/";
define('BORS_INCLUDE', '/var/www/localhost/bors/');
define('BORS_INCLUDE_LOCAL', '/var/www/localhost/bors-local/');

include_once('../../config.php');

$db = &new DataBase(config('search_db'));

for($i=0; $i<10; $i++)
{
	$db->query("CREATE TABLE IF NOT EXISTS `bors_search_source_$i` (
		`word_id` INT UNSIGNED NOT NULL,
		`class_name` VARCHAR(64) CHARACTER SET BINARY NOT NULL,
		`class_id` INT UNSIGNED NOT NULL ,
		`count` INT( 5 ) UNSIGNED NOT NULL ,
		`object_create_time` INT UNSIGNED NOT NULL ,
		`object_modify_time` INT UNSIGNED NOT NULL ,
		PRIMARY KEY ( `word_id` , `class_name` , `class_id` ),
		KEY (`object_create_time`),
		KEY (`object_modify_time`)
	)");
}
