<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
include_once(BORS_CORE.'/init.php');

forum_clean(190, 3, 'АвиаПорт.Ru');
forum_clean(189, 5, 'АвиаПорт.Ru');
forum_clean(8, 7, 'АвиаПорт.Ru');

echo "\n";

function forum_clean($forum_id, $days, $owner)
{
	foreach(objects_array('balancer_board_topic', array(
			'forum_id' => $forum_id, 
			'create_time<' => time() - $days*86400, 
			'num_replies' => 0,
			'author_name' => $owner,
		)) as $topic)
	{
		$posts = objects_array('balancer_board_post', array('topic_id' => $topic->id()));
		if(count($posts) > 1)
		{
			$topic->reclaculate();
			echo "Incorrect num posts in {$topic->debug_title()} {$topic->url()} (".count($posts).")\n";
			continue;
		}

		if(count($posts))
			$posts[0]->delete();

		$topic->delete();
//	echo "delete {$posts[0]->debug_title()} in {$topic->debug_title()}\n";
		echo '-';
	}
}
