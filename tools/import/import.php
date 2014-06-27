<?php

// exit();

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// Это реально занятый в системе скрипт! Не экспериментировать!

/* *juick *блоги *ЛОР *RSS *трансляции *работа
	ЛОРовский тред http://www.linux.org.ru/forum/talks/5145502 и Juick в итоге сподвигли на начало работы по реализации трансляции в 
*/

define('BORS_CORE', '/var/www/bors/composer/vendor/balancer/bors-core');
define('BORS_3RD_PARTY', '/var/www/repos/bors-third-party');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
include_once(BORS_CORE.'/init.php');

/*
$blog = object_load('balancer_board_blog', 2222866);
$blog->cache_clean_self();
bors()->changed_save();
$topic_id = common_keyword::best_topic($kws = 'АвиаПорт.Ru, Авиабаза, интеграция', 0);
$topic = object_load('balancer_board_topic', $topic_id);
echo 'Found topic for '.$kws." = {$topic->debug_title()}\n";
exit();
*/

//foreach(objects_array('bors_external_feeds_entry', array('target_object_id' => 0, 'is_suspended IS NULL')) as $entry)
//	$entry->set_pub_date(0, true);
//bors()->changed_save();

//$feed = bors_load('bors_external_feed', 14);
foreach(bors_find_all('bors_external_feed', array('is_suspended!=' => 1)) as $feed)
{
	//$feed = object_load('bors_external_feed', 4);
	echo "[".date('d.m.Y H:i')."] Feed {$feed->feed_url()}\n";
	$feed->update(false, true); // test, rss-reread
	bors()->changed_save();
	sleep(1);
}

bors_exit();
