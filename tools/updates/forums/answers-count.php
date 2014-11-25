<?php

/**
	Обновить все счётчики числа ответов на данные сообщения $post->have_answers();
*/

require_once('../../config.php');

main();
bors_exit();

function main()
{
	$step = 1000;

	$dbh = new driver_mysql('AB_FORUMS');
	$max_id = $dbh->select('posts', 'MAX(id)', array());
	echo "Total posts: $max_id\n";

	for($i=$max_id; $i>=0; $i-=$step)
	{
		echo ($i-$step).".. $i\n";
		$posts = objects_array('balancer_board_post', array(
			'id BETWEEN '.($i-$step).' AND '.($i+1),
			'order' => '-create_time',
		));

		foreach($posts as $p)
			$p->set_have_ansers($p->direct_answers_summ(), true);

		bors()->changed_save();
		bors_object_caches_drop();
	}
}
