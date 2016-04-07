<?php

require_once('../../../config.php');

main();
bors_exit();

//config_set('debug_stop_on', 2);
config_set('is_debug', true);

function main()
{
	config_set('lcml_cache_disable_full', true);
	config_set('lcml.timeout', 999999);

	$step = 1000;

	$rdbh = new driver_mysql('AB_FORUMS');
	$max_id = $rdbh->select('posts', 'MAX(id)', []);
	$min_id = 0; // $rdbh->select('posts', 'MIN(id)', ['posted>' => strtotime('2016-04-03 06:00')]);
	echo "Total posts: $max_id\n";
//	for($i=$max_id; $i>=$min_id; $i-=$step)
	for($i=1160866; $i>=$min_id; $i-=$step)
	{
		$topics = [];
		$begin = max($i-$step, $min_id);

		echo $begin.".. $i\n";
		$pcs = bors_find_all('balancer_board_posts_cache', array(
			'id BETWEEN '.$begin.' AND '.($i+1),
			'body_ts>=' => strtotime('2016-04-03 00:00:00'),
			"body LIKE \"%http%\"",
			'order' => '-id',
			'by_id' => true,
		));

		$posts = bors_find_all('balancer_board_post', ['id IN' => array_keys($pcs), 'by_id' => true]);

		foreach($pcs as $pid => $pc)
		{
			$p = $posts[$pid];

			echo "\n\t{$p->id()}: ",$p->debug_title()," ",$p->url_for_igo(),"\n";
			if($b = $p->blog_entry())
				$b->recalculate();

			// Отключить проверку времени работы!
			$p->do_lcml_full_compile();
			$p->recalculate();
			$p->cache_clean();
			$topics[$p->topic_id()] = true;
		}

		echo "=== topics clean ===\n";

		foreach(array_keys($topics) as $tid)
			if($topic = bors_load('balancer_board_topic', $tid))
				$topic->recalculate(false, true);

		bors()->changed_save();
		bors_object_caches_drop();
		usleep(300000);

	}
}
