<?php

define('REPO_DIR', "/var/www/forums.balancer.ru/data2");

require_once('../config.php');

$exp = new exporter(2014, 1);
$exp->main();
bors_exit();

// projects (1repo=1project) == categories
// Параметры проекта защищены ключами. Ключами же защищены данные по исходному положению сообщения, топики, форумы...

// /forums (tree)
// /users
// topics
//    attaches (рядом с posts.json)
//    posts
// /notes (score, reputation)
// /data (sites-preview, site-image-caches) (/resources?)
// /thumbnails

// По каждому пользователю:
// Публичные данные (ник и т.п.)
// Публичный uuid@host — для связи и т.п.
// Скрытый md5(uuid+salt)@host для идентификации оценок

// forum_id — уникальный только внутри проекта
// topic_id = DD.HHMMSS.uuid.title (от даты создания, чтобы легко находить по событиям)
// post_id = DD.HHMMSS.author.uuid
// attach_file = DD.HHMMSS.author.filename.ext

class exporter
{
	var $year;
	var $month;
	var $repo;

	function __construct($year, $month)
	{
		$this->year = $year;
		$this->month = $month;
		$this->repo = REPO_DIR.sprintf("/%04d-%02d/", $year, $month);
	}

	function main()
	{
		$year = $this->year;
		$month = $this->month;

		$next_month = $month+1;
		$next_year = $year;

		if($next_month>12)
		{
			$next_month = 1;
			$next_year++;
		}

		$start = strtotime("01-$month-$year 00:00:00 GMT");
		$stop  = strtotime("01-$next_month-$next_year 00:00:00 GMT")-1;

		echo date("r\n", $start);
		echo date("r\n", $stop);
		echo bors_count('balancer_board_post', array('posted BETWEEN' => array($start, $stop))), PHP_EOL;
		echo "Begin...\n";

		$total = 0;

		$last_id = 0;

		do
		{
			echo "\noffset: $last_id ($total, ".(@$p?$p->ctime():'')."): ", bors_debug::memory_usage_ping(), PHP_EOL;

			$p = NULL;

			foreach(bors_find_all('balancer_board_post', array(
					'id>' => $last_id,
					'posted BETWEEN' => array($start, $stop),
					'order' => 'id',
					'limit' => 1000)
			) as $p)
			{
				$t = self::make_topic($p);
				if($p->is_public())
				{
					$data = $p->data;
					$data['create_time'] = date('r', $data['create_time']);

					if(!empty($data['edited']))
						$data['edit_time'] = date('r', $data['edited']);

					var_mv($data['title'], $data['title_raw']);
					unset($data['topic_page']);
					unset($data['edited']);

					$pd = array();
					var_mv($pd['poster_ip'], $data['poster_ip']);

					openssl_public_encrypt(json_encode($pd), $encrypted_pd, file_get_contents('ssl/pub.key'));

					$data['project_private_data'] = base64_encode($encrypted_pd);

					$data = array_filter($data);

					$fn_base = $t->repo_path()
						.'/posts/'.date('d.His', $p->create_time())
						.'.'.$p->id();

					file_put_contents($post_file = $fn_base.'.json', json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
					touch($post_file, $p->modify_time());

					foreach($p->attaches() as $attach)
					{
						$attach_file = $fn_base.'.'.winfsname($attach->filename());
						$src = '/var/www/files.balancer.ru/files/forums/attaches/'.$attach->location();
						echo 'a';
						if(!file_exists($attach_file) || filesize($attach_file) != filesize($src))
						{
							copy($src, $attach_file);
							touch($attach_file, $p->modify_time());
						}
					}
				}

				$total++;
			}

			if($p)
				$last_id = $p->id();

			bors()->changed_save();
			bors_object_caches_drop();

		} while($p);

		echo "Total=$total\n";
	}


	function make_category($forum)
	{
		static $categories = array();
		if(($cat = @$categories[$forum->category_id()]))
			return $cat;

		$categories[$forum->category_id()] = $cat = $forum->category();
		mkpath($path = $this->repo.winfsname($cat->title()));
//		echo "Make $path\n";
		$cat->set_attr('repo_path', $path);

		file_put_contents($f = $path."/info.json", json_encode($cat->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
//		touch($f, $cat->modify_time());

		return $cat;
	}

	function make_forum($topic)
	{
		static $forums = array();
		if(($forum = @$forums[$topic->forum_id()]))
			return $forum;

		$forums[$topic->forum_id()] = $forum = $topic->forum();
		$cat = $this->make_category($forum);

		mkpath($path = $cat->repo_path()."/".winfsname($forum->title()));
		touch($path, $lt = $forum->last_post_time());
		$forum->set_attr('repo_path', $path);
		$forum->set_attr('category', $cat);

		file_put_contents($f = $path."/info.json", json_encode($forum->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		touch($f, $lt);

		return $forum;
	}

	function make_topic($post)
	{
		static $topics = array();
		if(($topic = @$topics[$post->topic_id()]))
			return $topic;

		$topics[$post->topic_id()] = $topic = $post->topic();
		$forum = $this->make_forum($topic);

		$topic_name = date('d.His.', $topic->create_time())
			.$topic->id().' '.winfsname($topic->title());

		mkpath($path = $forum->repo_path()."/".$topic_name);
		touch($path, $mt = $topic->modify_time());

		$topic->set_attr('repo_path', $path);
		$topic->set_attr('forum', $forum);

		$data = $topic->data;
		$data['create_time'] = date('r', $data['create_time']);
		$data['modify_time'] = date('r', $data['modify_time']);
		$data['last_post_create_time'] = date('r', $data['last_post_create_time']);

		var_mv($data['forum_id'], $data['forum_id_raw']);

		$data = array_filter($data);

		mkpath($f = $path.'/posts');
		touch($f, $mt);

		file_put_contents($f = $path."/info.json", json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		touch($f, $mt);
		echo ".";

		return $topic;
	}
}

function winfsname($name)
{
	static $map = array(
		'/' => '∕',
		"\\" => '-',
		':' => '.',
		'<' => '[',
		'>' => ']',
		'?' => '.',
		'*' => '×',
		'"' => '“',
		'|' => 'ǀ',
	);

	$name = preg_replace("/[\x01-\x1F\s]+/", ' ', $name);
	$name = str_replace(array_keys($map), $map, $name);
	$name = rtrim($name, '. ');
	return $name;
}

function var_mv(&$dst, &$src)
{
	$dst = $src;
	$src = NULL;
}
