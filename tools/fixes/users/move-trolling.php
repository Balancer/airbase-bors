<?php

require_once('../../config.php');

$target_topic_id = 82617;

main();
bors_exit();

function main()
{
	global $target_topic_id;

	// 84463 — Lazy Rider
	// 105560 — Lumen

	$uids = [84463, 105560];
	$cat_ids = [6,8,26,27]; // 8 = Клуб

	$posts = bors_each('balancer_board_post', [
		'inner_join' => [
			'balancer_board_topic ON balancer_board_topic.id = topic_id',
//			'balancer_board_forum ON balancer_board_forum.id = forum_id',
		],
		'create_time>' => time() - 86400*7,
		'owner_id IN' => $uids,
//		'balancer_board_forum.category_id IN' => $cat_ids,
		'topic_id<>' => $target_topic_id,
	]);

	posts_move($posts);

	$neznaiko = bors_find_all('balancer_board_user', [
		'utmx IN' => [
			'96b302de414144a74f685bd3a61fdc1e', '51296554a9748a6bc21f33d1f110caba',
			'4fcb38f61c500bf2456b5558aaa8c636', '8ad78f96b01b056bc797909e4b090a07',
			'6dd62f39282dca5895918cc925ff7ade', 'a942419a003d2850ac76b84641f2fb94',
		],
		'id<>' => 107867,
		'by_id' => true,
	]);

	$uids = array_keys($neznaiko);

	$uids[] = 108377; // Чингизхан, твинк Незнайко
	$uids[] = 108394; // Марк Аврелий, твинк Незнайко

	$utmxs = [];

	// ID юзеров под снос всех клонов по utmx
	$uids_for_utmx = [
		107818, // Scientist
		108577, // wertwe, твинк mina
		108543, // V-2, твинк Незнайко
		108638, // ВоваИ, твинк Незнайко
		108660, // Кобра, твинк Незнайко
		108662, // Октябръ
		108663, // Skyfly
	];

	foreach(bors_find_all('balancer_board_user', ['id IN' => $uids_for_utmx]) as $u)
		$utmxs[] = $u->utmx();

	$twinks = bors_find_all('balancer_board_user', [
		'utmx IN' => $utmxs,
		'by_id' => true,
	]);

	$uids = array_merge($uids, array_keys($twinks));

	$posts = bors_each('balancer_board_post', [
		'owner_id IN' => $uids,
		'topic_id<>' => $target_topic_id,
	]);

	posts_move($posts);
}

function posts_move($posts)
{
	global $target_topic_id;

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
