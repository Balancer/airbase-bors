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
		'keywords_string LIKE' => '%новост%',
		'forum_id>' => 0,
		'order' => '-id',
	)) as $topic)
	{

		common_keyword_bind::add($topic, true);
		if(($count++%10) == 0)
		{
			echo $topic->id() . " [".($topic->keywords_string_db() ? $topic->keywords_string_db() : '*'.$topic->keywords_string()) ."] ... ";
			bors()->changed_save();
			echo " cs ";
			bors()->drop_all_caches();
			echo " dr ";
			echo " done [".bors()->memory_usage()."]\n";
		}
	}
}
