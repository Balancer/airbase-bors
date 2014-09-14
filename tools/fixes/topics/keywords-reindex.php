<?php

/**
	Переиндексировать все ключевые слова топиков
*/

require_once('../../config.php');

main();
bors_exit();

function main()
{
	$count = 0;
	foreach(bors_each('balancer_board_topic', array(
//		'modify_time>' => time() - 86400*365,
		'forum_id>' => 0,
//		'id<56755',
//		'keywords_string_db<>' => '',
		'order' => '-id',
	)) as $topic)
	{
		if($topic->keywords_string() == $topic->forum()->keywords_string() && !preg_match('/новости/', $topic->keywords_string()))
			continue;

		common_keyword_bind::add($topic, true);
		if(($count++%10) == 0)
		{
			echo $topic->id() . " [{$topic->keywords_string_db()}] ... ";
			bors()->changed_save();
			echo " cs ";
			bors()->drop_all_caches();
			echo " dr ";
			echo " done [".bors()->memory_usage()."]\n";
		}
	}
}
