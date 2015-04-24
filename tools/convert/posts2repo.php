<?php

define('REPO_DIR', "/var/www/forums.balancer.ru/data2");

require_once('../config.php');

for($year = date('Y'); $year >= 2013; $year--)
{
	for($month = ($year == date('Y') ? date('m') : 12); $month > 0; $month--)
	{
		echo "*********************************\nExport for $year-$month\n*********************************\n";
		$exp = new exporter($year, $month);
		$exp->main();
	}
}

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


//git remote add origin git@github.com:Balancer/forums-2014-10.git
//git push -u origin master

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

		$last_id = bors_find_first('balancer_board_posts_pure', [
			'*set' => 'MIN(id) AS min_id',
			'posted BETWEEN' => array($start, $stop),
		])->min_id() - 1;

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
				$f = self::make_forum($t);
				if($p->is_public() && $t->is_public() && $f->is_public())
				{
					$fn_base = $t->repo_path()
						.'/posts/'.date('d.His', $p->create_time())
						.'.'.$p->id();

					$post_file = $fn_base.'.json';

					// Временно. Иначе при каждом обновлении переписывается другими данными private data
					if(file_exists($post_file) && filemtime($post_file) >= $p->modify_time())
						continue;

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

//					$data['project_private_data'] = base64_encode($encrypted_pd);

					$data = array_filter($data);

					file_put_contents($post_file, json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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


	private $categories = array();
	function make_category($forum)
	{
		if(($cat = @$this->categories[$forum->category_id()]))
			return $cat;

		$this->categories[$forum->category_id()] = $cat = $forum->category();
		mkpath($path = $this->repo . winfsname($cat->title()));
//		echo "Make $path\n";
		$cat->set_attr('repo_path', $path);

		file_put_contents($f = $path."/info.json", json_encode($cat->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
//		touch($f, $cat->modify_time());

		return $cat;
	}

	private $forums = [];
	function make_forum($topic)
	{
		$this->forums = array();
		if(($forum = @$this->forums[$topic->forum_id()]))
			return $forum;

		$this->forums[$topic->forum_id()] = $forum = $topic->forum();

		$cat = $this->make_category($forum);

		if(!$forum->is_public())
			return $forum;

		mkpath($path = $cat->repo_path()."/".winfsname($forum->title()));
		touch($path, $lt = $forum->last_post_time());
		$forum->set_attr('repo_path', $path);
		$forum->set_attr('category', $cat);

		file_put_contents($f = $path."/info.json", json_encode($forum->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		touch($f, $lt);

		return $forum;
	}

	private  $topics = array();
	function make_topic($post)
	{
		if(($topic = @$this->topics[$post->topic_id()]))
			return $topic;

		$this->topics[$post->topic_id()] = $topic = $post->topic();

		$forum = $this->make_forum($topic);

		if(!$topic->is_public())
			return $topic;

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
