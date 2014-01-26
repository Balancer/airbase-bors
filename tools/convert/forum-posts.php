<?php

require_once('../config.php');

main();
bors_exit();

function main()
{
	$step = 1000;

	$rdbh = new driver_mysql('AB_FORUMS');
	$max_id = $rdbh->select('posts', 'MAX(id)', array());
	echo "Total posts: $max_id\n";
	for($i=$max_id; $i>=0; $i-=$step)
//	for($i=669030; $i>=0; $i-=$step)
	{
		$topics = array();

		echo ($i-$step).".. $i\n";
		$posts = array_reverse(bors_find_all('balancer_board_post', array(
			'id BETWEEN '.($i-$step).' AND '.($i+1),
			'`html` IS NOT NULL',
			'by_id' => true,
		)));

		$caches = bors_find_all('balancer_board_posts_cache', ['id IN' => array_keys($posts)]);

		foreach($posts as $p)
		{
			if($html = $p->data['post_body'])
			{
				$p->cache_make([
					'body' => $html,
					'body_ts' => time(),
				]);
				$p->set('post_body', NULL, true);
				echo '.';
			}
			else
				echo '-';
		}

		echo "!\n";

		bors()->changed_save();
		bors_object_caches_drop();
		usleep(300000);
	}
}
