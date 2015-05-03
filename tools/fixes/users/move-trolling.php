<?php

require_once('../../config.php');

main();
bors_exit();

function main()
{
	$uids = [84463, 105560];
	$cat_ids = [6,26,27];
	$target_topic_id = 82617;

	$posts = bors_each('balancer_board_post', [
		'inner_join' => [
			'balancer_board_topic ON balancer_board_topic.id = topic_id',
			'balancer_board_forum ON balancer_board_forum.id = forum_id',
		],
		'owner_id IN' => $uids,
		'balancer_board_forum.category_id IN' => $cat_ids,
		'topic_id<>' => $target_topic_id,
	]);

	$topics = [];

	foreach($posts as $p)
	{
		echo "\n\t{$p->id()}: ",$p->debug_title()," ",$p->url_for_igo(),"\n";

		$p->move_tree_to_topic($target_topic_id);
		if($b = $p->blog_entry())
			$b->recalculate();

		$topics[$p->topic_id()] = true;
	}

	if(!$topics)
		return;

	echo "=== topics clean ===\n";

	foreach(array_keys($topics) as $tid)
		if($topic = bors_load('balancer_board_topic', $tid))
			$topic->recalculate();

	bors_load('balancer_board_topic', $target_topic_id)->recalculate();
}
