<?php

define('YEAR', 2013);
define('REPO_DIR', "/var/www/forums.balancer.ru/data2/".YEAR);

require_once('../config.php');

main();
bors_exit();

function main()
{
	$start = strtotime('01-01-2013 00:00:00 GMT');
	$stop  = strtotime('31-12-2013 23:59:59 GMT');
	echo date("r\n", $start);
	echo date("r\n", $stop);
	echo bors_count('balancer_board_post', array('posted BETWEEN' => array($start, $stop))), PHP_EOL;
	echo "Begin...\n";
	$topics = array();
	$forums = array();
	$categories = array();
	$loop = 0;


	$last_id = 0;

	do
	{
		echo "offset: $last_id: ", bors_debug::memory_usage_ping(), PHP_EOL;

		$p = NULL;

		foreach(bors_find_all('balancer_board_post', array(
				'id>' => $last_id,
				'posted BETWEEN' => array($start, $stop),
				'order' => 'id',
				'limit' => 1000)
		) as $p)
		{
			if(!($t = @$topics[$p->topic_id()]))
			{
				$topics[$p->topic_id()] = $t = $p->topic();
				echo "{$t->id()} [{$p->id()}, {$p->ctime()}]: {$t->title()}\n";
				$forum = $t->forum();
				make_category($forum);
			}

		}

		if($p)
			$last_id = $p->id();

		bors()->changed_save();
		bors_object_caches_drop();

	} while($p);
}

function winfsname($name)
{
	static $map = array(
		'/' => '-',
		':' => '.',
		'<' => '[',
		'>' => ']',
	);

	return str_replace(array_keys($map), $map, $name);
}

function make_category($forum)
{
	static $categories = array();
	if(($cat = $categories[$forum->category_id()]))
		return $cat;

	$categories[$forum->category_id()] = $cat = $forum->category();
	mkpath($path = REPO_DIR."/".sprintf("%04d", $cat->id())." ".winfsname($cat->title()));

	$cat->set_attr('repo_path', $path);
	return $cat;
}

function make_forum($topic)
{
	static $forums = array();
	if(($forum = $forums[$topic->forum_id()]))
		return $forum;

	$forums[$topic->forum_id()] = $forum = $topic->forum();
	$cat_path = make_category($forum);

	mkpath($path = $cat_path."/".sprintf("%04d", $forum->id())." ".winfsname($forum->title()));
	$forum->set_attr('repo_path', $path);
	return $forum;
}
