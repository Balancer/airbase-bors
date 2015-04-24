<?php

use Symfony\Component\Yaml\Yaml;

//define('REPO_DIR', "/var/www/forums.balancer.ru/data2");
define('REPO_DIR', "/data/files/forums-export");

require_once('/var/www/bors/composer/vendor/autoload.php');
require_once('../config.php');

//$year = 2014;
//$month = 10;

for($year = 2007; $year <= 2039; $year++)
{
	for($month = 1; $month <= 12; $month++)
	{
		echo "*********************************\nExport for $year-$month\n*********************************\n";
		$exp = new exporter($year, $month);
		$exp->main();
	}
}

bors_exit("\nEnd\n\n");

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
		echo $cnt = bors_count('balancer_board_posts_pure', array('posted BETWEEN' => array($start, $stop))), PHP_EOL;

		if(!$cnt)
			return;

		echo "Begin...\n";

		$xx = bors_find_first('balancer_board_posts_pure', [
			'*set' => 'MIN(id) AS min_id, MAX(id) AS max_id',
			'posted BETWEEN' => array($start, $stop),
		]);

		$total = 0;
		$min_id = $xx->min_id();
		$max_id = $xx->max_id();

		echo "\noffset: $min_id .. $max_id (, ".(@$p?$p->ctime():'')."): ", bors_debug::memory_usage_ping(), PHP_EOL;

		$p = NULL;

		foreach(bors_each('balancer_board_post', array(
				'id BETWEEN' => [$min_id, $max_id],
				'posted BETWEEN' => [$start, $stop])
		) as $p)
		{
			$total += $this->post2md($p);
		}

		bors()->changed_save();
		bors_object_caches_drop();

		echo "\nTotal=$total\n";
	}

	private $categories = array();
	function make_category($forum)
	{
		if(($cat = @$this->categories[$forum->category_id()]))
			return $cat;

		$this->categories[$forum->category_id()] = $cat = $forum->category();
		$path = REPO_DIR . '/' . winfsname($cat->title()) . sprintf("/%04d-%02d", $this->year, $this->month);
		mkpath($path);
//		echo "Make $path\n";
		$cat->set_attr('repo_path', $path);

		file_put_contents($f = $path."/000-info.json", json_encode($cat->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
//		touch($f, $cat->modify_time());

		return $cat;
	}

	private $forums = [];
	function make_forum($topic)
	{
		if(!$topic)
			return NULL;

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

		file_put_contents($f = $path."/000-info.json", json_encode($forum->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		touch($f, $lt);

		return $forum;
	}

	private  $topics = array();
	function make_topic($post)
	{
		if(($topic = @$this->topics[$post->topic_id()]))
			return $topic;

		$this->topics[$post->topic_id()] = $topic = $post->topic();

		if(!$topic)
		{
			echo "\nLost topic for post ".$post->id()."\n";
			return NULL;
		}

		$forum = $this->make_forum($topic);

		if(!$topic->is_public() || !$forum->is_public())
			return $topic;

		$topic_name = date('ymd.', $topic->create_time())
			.sprintf('%03d', $topic->id()%1000).' '.winfsname($topic->title());

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

		file_put_contents($f = $path."/000-info.json", json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		touch($f, $mt);
		echo ".";

		return $topic;
	}

	function post2md($p)
	{
		$t = self::make_topic($p);
		$f = self::make_forum($t);

		if(!$t || !$f || ($p->is_public() && $t->is_public() && $f->is_public()))
		{
			if($t)
			{
				$fn_base = $t->repo_path()
					.'/'.date('ymd-His-', $p->create_time()).$p->id();
			}
			else
			{
				$fn_base = REPO_DIR . '/lost-posts/' . sprintf("/%04d-%02d", $this->year, $this->month)
					.'/'.date('ymd-His-', $p->create_time()).$p->id();
				mkpath(dirname($fn_base));
			}

			$post_file = $fn_base.' '.winfsname($p->author_name()).'.md';

			// Временно. Иначе при каждом обновлении переписывается другими данными private data
			if(file_exists($post_file) && filemtime($post_file) >= $p->modify_time())
				return 0;

			$data = $p->data;

			var_mv($data['Title'], $data['title_raw']);
			$data['Post_Id'] = 'balancer_board_post__'.popval($data, 'id');
			$data['Topic_Id'] = 'balancer_board_topic__'.popval($data, 'topic_id');

			if($ans = popval($data, 'answer_to_id'))
				$data['Answer_To_Post_Id'] = 'balancer_board_post__' . $ans;

			$data['Author'] = array(
				'Name' => $data['author_name'],
				'Id' => 'balancer_board_user__' . $data['owner_id'],
				'User_Agent' => $data['poster_ua'],
			);

			// <img src="http://s.wrk.ru/f/lt.gif" class="flag" title="Lithuania" alt="LT"/>
			var_mv($flag, $data['flag_db']);

			if(preg_match('/title="(.+?)".*alt="(.+?)"/', $flag, $m))
			{
				$data['Author']['Country'] = $m[2] != '??' ? $m[2] : NULL;
				$data['Author']['Place']   = $m[1] != '??' ? $m[1] : NULL;
			}

			$data['Date'] = date('r', popval($data, 'create_time'));

			if(!empty($data['edited']))
			{
				$data['Modify'] = date('r', popval($data, 'edited'));
				var_mv($data['Editor'], $data['edited_by']);
			}

			unset($data['topic_page'],
				$data['warning_id'], $data['author_name'], $data['owner_id'],
				$data['poster_ua'], $data['answer_to_user_id'],
				$data['answers_count_raw'], $data['answer_to_user_id'], $data['have_answers']
			);

			$data['Score'] = [
				'Sum' => popval($data, 'score'),
				'Positives' => popval($data, 'score_positive_raw'),
				'Negatives' => popval($data, 'score_negative_raw'),
			];

			var_mv($source, $data['post_source']);

			$pd = array();
			var_mv($pd['poster_ip'], $data['poster_ip']);

			openssl_public_encrypt(json_encode($pd), $encrypted_pd, file_get_contents('ssl/pub.key'));

//			$data['project_private_data'] = base64_encode($encrypted_pd);

			$data = array_cleaner($data);

			$yaml = Yaml::dump($data);

			file_put_contents($post_file, "---\n$yaml---\n\n".trim($source)."\n");
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

			return 1;
		}
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

function array_cleaner($array)
{
	foreach($array as $key => $value)
	{
		if(is_array($value))
			$array[$key] = array_cleaner($array[$key]);

		if(empty($array[$key]))
			unset($array[$key]);
	}

	return $array;
}
