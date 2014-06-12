<?php

/*
	Проход по всем аттачам и загрузка их в список объектов, связынных
	с сообщением
*/

require_once('../../../config.php');

main();
bors_exit();

function main()
{
	config_set('lcml_cache_disable_full', true);

	$step = 100;

	$rdbh = new driver_mysql('AB_FORUMS');
	$max_id = $rdbh->select('attach_2_files', 'MAX(id)', array());
	echo "Total attaches: $max_id\n";
	for($i=$max_id; $i>=0; $i-=$step)
//	for($i=35198; $i>=0; $i-=$step)
//	$i = 388783;
	{
//		$topics = array();

		echo ($i-$step).".. $i\n";
		$attaches = array_reverse(bors_find_all('balancer_board_attach', array(
			'id BETWEEN '.($i-$step).' AND '.($i+1),
		)));

		foreach($attaches as $x)
		{
			if($img = $x->image())
			{
				balancer_board_posts_object::register_object($x->post(),  $img);
				echo '+';
			}
			else
				echo '-';
		}

		echo "\n";
/*
		echo "=== topics clean ===\n";

		foreach(array_keys($topics) as $tid)
			if($topic = bors_load('balancer_board_topic', $tid))
				$topic->recalculate(false, true);
*/
		bors()->changed_save();
		bors_object_caches_drop();
		usleep(50000);
	}
}
