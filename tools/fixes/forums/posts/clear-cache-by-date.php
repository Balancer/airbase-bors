<?php

require_once('../../../config.php');

main();
bors_exit();

config_set('debug_stop_on', 2);

function main()
{
	config_set('lcml_cache_disable_full', true);
	config_set('lcml.timeout', 999999);

	$step = 1000;

	$rdbh = new driver_mysql('AB_FORUMS');
	$max_id = $rdbh->select('posts', 'MAX(id)', array());
	echo "Total posts: $max_id\n";
//	for($i=$max_id; $i>=0; $i-=$step)
	for($i=226750; $i>=0; $i-=$step)
	{
		$topics = array();

		echo ($i-$step).".. $i\n";
		$pcs = bors_find_all('balancer_board_posts_cache', array(
			'id BETWEEN '.($i-$step).' AND '.($i+1),
			'body_ts>=' => strtotime('Fri Sep 05 06:43:35 2014 +0400'),
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
