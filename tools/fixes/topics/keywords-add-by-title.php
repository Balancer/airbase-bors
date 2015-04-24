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
		'subject LIKE' => '%гидронавт%',
//		'forum_id NOT IN' => [12, 37],
		'order' => '-id',
	)) as $topic)
	{
		echo $topic->debug_title()."\n";

		$topic->add_keyword('гидронавтика');

		common_keyword_bind::add($topic, true);
	}
}
