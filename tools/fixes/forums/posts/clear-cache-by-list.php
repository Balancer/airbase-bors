<?php

// Очистка кеша сообщений с ID из списка posts.txt

require_once('../../../config.php');

main();
bors_exit();

function main()
{
	config_set('lcml_cache_disable_full', true);
	config_set('lcml.timeout', 999999);

	$topics = array();

	foreach(file('./posts.txt') as $pid)
	{
		$p = bors_load('balancer_board_post', intval($pid));

		if($p)
		{
			echo "\n\t{$p->id()}: ",$p->debug_title()," ",$p->url_for_igo(),"\n";
			if($b = $p->blog_entry())
				$b->recalculate();

			// Отключить проверку времени работы!
			$p->do_lcml_full_compile();
			$p->recalculate();
			$p->cache_clean();
			$topics[$p->topic_id()] = true;
		}
	}

	echo "=== topics clean ===\n";

	foreach(array_keys($topics) as $tid)
		if($topic = bors_load('balancer_board_topic', $tid))
			$topic->recalculate(false, true);

	bors()->changed_save();
	bors_object_caches_drop();
}
