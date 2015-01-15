<?php

/**
	Переиндексировать ключевые слова одного топика
*/

require_once('../../config.php');

main();
bors_exit();

function main()
{
	$topic = bors_load('balancer_board_topic', 90183);

	common_keyword_bind::add($topic, true);
	echo $topic->id() . " [{$topic->keywords_string_db()}] ... \n";
	bors()->changed_save();
	bors()->drop_all_caches();
}
