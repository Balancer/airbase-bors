<?php

define("XHPROF_ROOT", '/var/www/www.balancer.ru/htdocs/wa/opt/xhprof');
gc_enable();


/**
	Обновить статус занесения сообщения в лучшие.
*/

require_once('../../config.php');

main();
bors_exit();

function main()
{
	echo "Nullify ...";
	driver_mysql::factory('AB_FORUMS')->query('UPDATE posts_calculated_fields SET best10_ts = NULL');
	echo " done\n";

//	xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

	$step = 1000;

	$rdbh = new driver_mysql('AB_BORS');
	$max_id = $rdbh->select('bors_thumb_votes', 'MAX(id)', array());
	echo "Votes: $max_id\n";

	$counts = array();

	for($i=0; $i<=$max_id; $i+=$step)
	{
		echo ($i-$step).".. $i\n";
		$votes = bors_find_all('bors_votes_thumb', array(
			'id BETWEEN '.($i).' AND '.($i+$step-1),
			'score>' => 0,
			'target_class_name IN' => array('balancer_board_post', 'forum_post'),
			'order' => 'create_time',
		));

		$posts = bors_find_all('balancer_board_posts_calculated', array(
			'id IN' => bors_field_array_extract($votes, 'target_object_id'),
			'by_id' => true,
		));

		foreach($votes as $vote)
		{
			$post = @$posts[$vote->target_object_id()];
			if(!$post)
				continue;

			if(!is_null($post->best10_ts()) && $post->best10_ts() <= $vote->create_time())
				continue;

			@$counts[$post->id()]++;
			if($counts[$post->id()] < 10)
				continue;

			$real_post = bors_load('balancer_board_post', $post->id());
			// Считаем только «безответные»
			if($real_post->answer_to_id())
				continue;

			$post->set_best10_ts($vote->create_time(), true);
//			echo "{$post->debug_title()} : {$counts[$post->id()]}\n";
			echo "+";
		}

		echo "$i: ".round(memory_get_usage()/1024/1024)."MB -> ";
		bors()->changed_save();
		bors_object_caches_drop();
		bors_drop_global_caches();

		gc_collect_cycles();
		echo $last = round(memory_get_usage()/1024/1024);
		echo "MB done\n";

		if($last > 100)
		{
			file_put_contents('globals-dump'.$i.'.serialize',	@serialize($GLOBALS));
			file_put_contents('globals-dump'.$i.'.json',		@json_encode($GLOBALS, JSON_PRETTY_PRINT));
			file_put_contents('globals-dump'.$i.'.print_r',		@print_r($GLOBALS, true));
			exit();
		}
/*
			$xhprof_data = xhprof_disable();
			require_once (XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php');
			require_once (XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php');
			$xhprof_runs = new XHProfRuns_Default();
			$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_testing");
//			echo "http://localhost/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n";
			echo $link = "http://www.balancer.ru/wa/opt/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n\n\n";
*/
	}
}
