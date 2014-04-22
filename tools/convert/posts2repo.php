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
			make_topic($p);
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
	if(($cat = @$categories[$forum->category_id()]))
		return $cat;

	$categories[$forum->category_id()] = $cat = $forum->category();
	mkpath($path = REPO_DIR.'/'.winfsname($cat->title()));
	$cat->set_attr('repo_path', $path);

	file_put_contents($path."/info.json", json_encode($cat->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

	return $cat;
}

function make_forum($topic)
{
	static $forums = array();
	if(($forum = @$forums[$topic->forum_id()]))
		return $forum;

	$forums[$topic->forum_id()] = $forum = $topic->forum();
	$cat = make_category($forum);

	mkpath($path = $cat->repo_path()."/".winfsname($forum->title()));
	$forum->set_attr('repo_path', $path);
	$forum->set_attr('category', $cat);

	file_put_contents($path."/info.json", json_encode($forum->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

	return $forum;
}

function make_topic($post)
{
	static $topics = array();
	if(($topic = @$topics[$post->topic_id()]))
		return $topic;

	$topics[$post->topic_id()] = $topic = $post->topic();
	$forum = make_forum($topic);

	mkpath($path = $forum->repo_path()."/".date('mdHis', $topic->last_post_create_time()).' '.winfsname($topic->title()));
	$topic->set_attr('repo_path', $path);
	$topic->set_attr('forum', $forum);

	file_put_contents($path."/info.json", json_encode($topic->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	echo ".";

	return $topic;
}
