<?php

//class forum_topic extends balancer_board_object_db
class forum_topic extends bors_page_db
{
	function config_class() { return 'balancer_board_config'; }

	function storage_engine() { return 'bors_storage_mysql'; }
	function can_be_empty() { return false; }

	function new_class_name() { return 'balancer_board_topic'; }

	function uri_name() { return 't'; }
	function nav_name() { return truncate($this->title(), 60); }

	function db_name() { return config('punbb.database'); }
	function table_name() { return 'topics'; }

	function table_fields()
	{
		return array(
			'id',
			'forum_id_raw' => 'forum_id',
			'title'	=> 'subject',
			'description',
			'answer_notice',
			'admin_notice',
			'image_id',
			'image_time' => 'UNIX_TIMESTAMP(`image_ts`)',
			'create_time'	=> 'posted',
			'last_post_create_time'=> 'last_post',
			'sort_time',
			'modify_time',
			'is_public',
			'owner_id'=> 'poster_id',
			'last_poster_name' => 'last_poster',
			'author_name' => 'poster',
			'num_replies',
			'is_repaged',
			'visits' => 'num_views',
			'first_post_id' => 'first_pid',
			'last_post_id' => 'last_post_id',
			'first_visit_time' => 'first_visit',
			'last_visit_time' => 'last_visit',
			'last_edit_time' => 'last_edit',
			'sticky',
			'moved_to',
			'joined_to_topic_id', // id темы, к которой была присоединена данная.
			'closed',
			'keywords_string_db' => 'keywords_string',
			'bot_note',
			'topic_data_raw' => 'topic_data',
		);
	}

function set_forum_id($v, $dbup = true)
{
	if(($new_forum = bors_load('balancer_board_forum', $v)))
		$this->set_is_public($new_forum->is_public(), true);

	return $this->set('forum_id_raw', $v, $dbup);
}

function forum_id() { return @$this->data['forum_id_raw']; }

function owner_id() { return @$this->data['owner_id']; }
function set_owner_id($v, $dbup = true) { return $this->set('owner_id', $v, $dbup); }
function last_poster_name() { return @$this->data['last_poster_name']; }
function set_last_poster_name($v, $dbup = true) { return $this->set('last_poster_name', $v, $dbup); }
function author_name() { return @$this->data['author_name']; }
function set_author_name($v, $dbup = true) { return $this->set('author_name', $v, $dbup); }
function num_replies() { return @$this->data['num_replies']; }
function set_num_replies($v, $dbup = true) { return $this->set('num_replies', $v, $dbup); }
function is_repaged() { return @$this->data['is_repaged']; }
function set_is_repaged($v, $dbup = true) { return $this->set('is_repaged', $v, $dbup); }
function visits() { return @$this->data['visits']; }
function set_visits($v, $dbup = true) { return $this->set('visits', $v, $dbup); }
function first_post_id() { return @$this->data['first_post_id']; }
function set_first_post_id($v, $dbup = true) { return $this->set('first_post_id', $v, $dbup); }
function last_post_id() { return @$this->data['last_post_id']; }
function set_last_post_id($v, $dbup = true) { return $this->set('last_post_id', $v, $dbup); }
function first_visit_time() { return @$this->data['first_visit_time']; }
function set_first_visit_time($v, $dbup = true) { return $this->set('first_visit_time', $v, $dbup); }
function last_visit_time() { return @$this->data['last_visit_time']; }
function set_last_visit_time($v, $dbup = true) { return $this->set('last_visit_time', $v, $dbup); }
function last_edit_time() { return @$this->data['last_edit_time']; }
function set_last_edit_time($v, $dbup = true) { return $this->set('last_edit_time', $v, $dbup); }
function sticky() { return @$this->data['sticky']; }
function set_sticky($v, $dbup = true) { return $this->set('sticky', $v, $dbup); }
function closed() { return @$this->data['closed']; }
function set_closed($v, $dbup = true) { return $this->set('closed', $v, $dbup); }
function keywords_string_db() { return @$this->data['keywords_string_db']; }
function set_keywords_string_db($v, $dbup = true) { return $this->set('keywords_string_db', $v, $dbup); }

	function keywords_linked()
	{
		if($this->__havefc())
			return $this->__lastc();

		require_once('inc/airbase_keywords.php');
		$kws = $this->keywords_string();
		return $this->__setc($kws ? airbase_keywords_linkify($kws) : '');
	}

	function keywords_linked_q()
	{
		if($this->__havefc())
			return $this->__lastc();

		require_once('inc/airbase_keywords.php');
		$kws = $this->keywords_string();
//		return $this->__setc($kws ? '"'.join('","', preg_split('/\s*,\s*/', addslashes(airbase_keywords_linkify($kws)))) .'"' : '');
		return $this->__setc($kws ? '"'.join('","', array_map('addslashes', $this->keywords())) .'"' : '');
	}

	function folder()   { return $this->forum(); }
	function category() { return $this->forum()->category(); }

	private $forum = false;
	function forum()
	{
		if(array_key_exists('forum', $this->attr))
			return $this->attr['forum'];

		if($this->forum === false)
			if(!$this->forum_id())
				debug_exit('Empty forum_id for topic '.$this->id());
			else
				$this->forum = bors_load_ex('balancer_board_forum', $this->forum_id(), array('memcache' => 600));

		return $this->set_forum($this->forum);
	}

	function set_forum($forum) { return $this->set_attr('forum', $forum); }

	function parents() { return array("balancer_board_forum://".$this->forum_id()); }

	function is_sticky() { return $this->sticky() ? true : false; }
	function is_closed() { return $this->closed() ? true : false; }

	function is_last_page() { return $this->page() == $this->total_pages(); }

	function user_ids()
	{
		$posts = $this->posts();
		$user_ids = [];
		foreach($posts as $p)
			$user_ids[] = $p->owner_id();

		return join(',', array_unique($user_ids));
	}

	function body()
	{
		if(!$this->is_repaged() && rand(0,5) == 0)
			$this->repaging_posts();

//		$body_cache = new bors_cache();
//		if($body_cache->get('bors_page_body-v3', $this->internal_uri_ascii().':'.$this->page().':'.(object_property(bors()->user(), 'group')).':'.$this->modify_time()))
//			return $this->attr['body'] = bors_lcml::output_parse($body_cache->last().'<!-- cached -->');

		require_once("engines/smarty/assign.php");

		$posts = $this->posts();

		$first_post_time = $this->first_post_time();
		$last_post_time  = $this->last_post_time();

		$post_ids = array_keys($posts);
		$blogs = bors_find_all('balancer_board_blog', array('id IN' => $post_ids, 'by_id' => true));

		foreach($blogs as $blog_id => $blog)
		{
			$posts[$blog_id]->set_blog($blog, false);
			if($kws = $blog->keywords())
				$posts[$blog_id]->set_keyword_links(balancer_blogs_tag::linkify($kws, '', ' ', true), false);
		}

		// Прописываем изменения репутации по постингам.
		$reps = bors_find_all('airbase_user_reputation', array(
			'target_class_name IN' => array('forum_post', 'balancer_board_post'),
			'target_object_id IN' => $post_ids,
			'is_deleted' => false,
			'order' => 'target_object_id, id',
		));

		foreach($reps as $r)
		{
			if($r->is_deleted())
				continue;

			$post = $posts[$r->target_object_id()];
			$post_reps = $post->get('reputation_records', array());
			$post_reps[] = $r;
			$post->set_attr('reputation_records', $post_reps);
		}

		$prev_post_time = $this->page() > 1 ? $first_post_time : 0;
		$next_post = $this->is_last_page() ? NULL : bors_find_first('balancer_board_posts_pure', array(
			'topic_id' => $this->id(),
			'is_deleted' => 0,
//			'use_index' => 'by_tid_ordered',
			'order' => '`order`, posted, id',
			'create_time>' => $last_post_time,
		));

		$next_post_time = object_property($next_post, 'create_time', time()+1);

		$actions = bors_find_all('balancer_board_action', array(
			'create_time BETWEEN' => array($prev_post_time, $next_post_time),
			'target_class_name IN' => array($this->class_name(), $this->extends_class_name(), $this->new_class_name()),
			'target_object_id' => $this->id(),
			'order' => 'create_time',
		));

		$post_values = array_values($posts);

		// Соберём события, бывшие первого сообщения темы
		$prev_actions = array();
		if($actions)
		{
			for($action_pos = 0; $action_pos < count($actions); $action_pos++)
			{
				$x = $actions[$action_pos];

				if($x->create_time() > $first_post_time)
					break;

				$prev_actions[] = $x;
			}


			for($post_pos = 0; $post_pos<count($post_values); $post_pos++)
			{
				$post_actions = array();
				$p = $post_values[$post_pos];

				if($action_pos < count($actions))
				{
					$next_time = $post_pos+1 >= count($post_values) ? time()+1 : $post_values[$post_pos+1]->create_time();

					for($action_pos; $action_pos < count($actions); $action_pos++)
					{
						$x = $actions[$action_pos];

						if($x->create_time() > $next_time)
							break;

						$post_actions[] = $x;
					}
				}

				$p->set_attr('actions', $post_actions);
			}
		}

		$data = array(
			'posts' => $post_values,
			'is_last_page' => $this->is_last_page(),
			'prev_actions' => $prev_actions,
		);

		if($this->is_last_page())
		{
			$data['last_actions'] = array_reverse(bors_find_all('balancer_board_action', array(
				'target_class_name IN' => array($this->class_name(), $this->extends_class_name(), $this->new_class_name()),
				'target_object_id' => $this->id(),
				'order' => '-create_time',
				'group' => 'target_class_name, target_object_id, message',
				'limit' => 10,
			)));

			bors_objects_preload($data['last_actions'], 'owner_id', 'balancer_board_user', 'owner');
		}

		bors_objects_preload($data['posts'], 'owner_id', 'balancer_board_user', 'owner');

		$data['this'] = $this;
//		$html = template_assign_data("xfile:forum/topic.html", $data);
		$html = bors_templates_smarty::fetch("xfile:forum/topic.html", $data);

//		return bors_lcml::output_parse($body_cache->set($html, 86400));
		return bors_lcml::output_parse($html);
	}

	private $__all_posts_ids;
	function all_posts_ids()
	{
		if(isset($this->__all_posts_ids))
			return $this->__all_posts_ids;

		return $this->__all_posts_ids = $this->db()->select_array('posts', 'id', array(
			'topic_id' => $this->id(),
			'is_deleted' => false,
			'order' => '`order`,posted,id',
		));
	}

	protected function posts_ids($page = NULL)
	{
		if(!$page)
			$page = $this->page();

		$data = array(
			'topic_id' => $this->id(),
			'order' => '`order`,posted,id',
			'is_deleted' => false,
		);

		if($this->is_repaged())
			$data['`page` = '] = intval($page);
		else
		{
			$data['page'] = $page;
			$data['per_page'] = $this->items_per_page();
		}

		return $this->db()->select_array('posts', 'id', array($data));
	}

	protected function _posts_def($page = NULL, $paging = true)
	{
		if(!$page)
			$page = $this->page();

		if(!$page)
			$page = 1;

		$data = array(
			'topic_id' => $this->id(),
			'order' => '`order`,posted,id',
			'by_id' => true,
			'is_deleted' => false,
		);

		if($paging && $this->is_repaged())
			$data['`page` = '] = intval($page);
		else
		{
			$data['page'] = $page;
			$data['per_page'] = $this->items_per_page();
		}

//		$data['use_index'] = 'by_tid_ordered';

		return bors_find_all('balancer_board_post', $data);
	}

	function items_per_page() { return 25; }
	function items_around_page() { return 9; }

	function total_pages() { return intval($this->num_replies() / $this->items_per_page()) + 1; }

	function pages_links($css='pages_select', $text = NULL, $delim = '', $show_current = true, $use_items_numeration = false, $around_page = NULL)
	{
		if($this->total_pages() < 2)
			return "";

		include_once('inc/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), $this->items_around_page()));
	}

	function title_pages_links()
	{
		if($this->total_pages() < 2)
			return "";

		require_once BORS_CORE.'/inc/design/page_split.php';
		return join(" ", pages_show($this, $this->total_pages(), 5, false));
	}


	function all_users()
	{
		return $this->db()->get_array("SELECT DISTINCT poster_id FROM posts WHERE topic_id={$this->id}");
	}

	function is_public_access() { return $this->forum_id() && $this->forum()->is_public_access(); }

	function base_url($page=NULL)
	{
		$base = '/';
		if($this->forum_id() && $this->forum())
			$base = $this->forum()->category()->category_base_full();

		// Если последний пост на странице свежий, то откручиваем на wrk.ru
//		if($this->get('last_post_create_time') > 1388520000) // С 01.01.2014 — wrk.ru. Более старые — forums.balancer.ru
		if($this->page_modify_time($page) > time()-86400*7)
			$base = str_replace('www.balancer.ru', 'www.wrk.ru', $base);
		else
			$base = str_replace('www.balancer.ru', 'forums.balancer.ru', $base);

		return $base;
	}

	function rss_url() { return $this->base_url().strftime("%Y/%m/", $this->modify_time())."topic-".$this->id()."-rss.xml"; }

	function search_source()
	{
		$result = array();

		$start_from = ($this->page() - 1) * $this->items_per_page();

		$query = "SELECT poster, message FROM posts INNER JOIN messages ON posts.id = messages.id WHERE topic_id={$this->id()} ORDER BY posts.`order`, posts.id LIMIT $start_from, ".$this->items_per_page();

		$posts = $this->db()->get_array($query);

		$data['posts'] = array();

		foreach($posts as $x)
		{
//			$post = class_load('balancer_board_post', $pid);
			if($x['message'])
				$result[] = $x['poster'].":\n---------------\n".$x['message'];
		}

		return join("\n============================\n\n", $result);
	}

	function page_by_post_id($post_id)
	{
		$post_id = intval($post_id);

		$posts = $this->db()->get_array("SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY `order`,posted, id");

		for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
			if($posts[$i] == $post_id)
				return intval( $i / 25) + 1;
	}

	function recalculate_on_change($start_page = NULL)
	{
		if(!$start_page)
			$start_page = 1;
		elseif($start_page == -1)
			$start_page = $this->total_pages();

		$this->repaging_posts($start_page);

//		$this->recalculate();
	}

	function recalculate($full_repaging = true, $skip_any_repaging = false)
	{
		// Ставим текущее время изменения
		$this->set_modify_time(time());

		$this->set('topic_data', NULL);

		bors()->changed_save(); // Сохраняем всё. А то в памяти могут быть модифицированные объекты, с которыми сейчас будем работать.

		if(!$skip_any_repaging)
		{
			$this->set_is_public($this->forum()->is_public(), true);

			$num_replies = $this->db()->select('posts', 'COUNT(*)', array(
				'topic_id' => $this->id(),
				'is_deleted' => false,
			)) - 1;

			$this->set_num_replies($num_replies, true);

			$first_pid = $this->db()->select('posts', 'MIN(id)', array('topic_id='=>$this->id()));

			if($first_pid)
				$this->set_first_post_id($first_pid, true);

			if($first_post = bors_load('balancer_board_post', $first_pid))
			{
				$this->set_create_time($first_post->create_time(true), true);
				$this->set_author_name($first_post->author_name(), true);
				$this->set_owner_id($first_post->owner() ? $first_post->owner()->id() : NULL, true);
			}
			else
				bors_debug::syslog('post_error', "Unknown first post $first_pid in {$this}->recalculate()");

			if($last_pid = $this->db()->select('posts', 'MAX(id)', array('topic_id='=>$this->id())))
			{
				$this->set_last_post_id($last_pid, true);
				$last_post = bors_load('balancer_board_post', $last_pid);

//			Но зачем? Пока не сносить, подумать.
//			if($this->get('last_post_create_time') < $last_post->create_time(true))
					$this->set_last_post_create_time($last_post->create_time(true), true);

				$this->set_last_poster_name($last_post->author_name(), true);

			}
			else
				bors_debug::syslog('post_error', "Unknown last post $first_pid in {$this}->recalculate()");

			$this->repaging_posts($full_repaging ? 1 : -1);
		}

		foreach($this->posts() as $pid => $p)
		{
//			$p->set_body(NULL);
//			$p->body();
		}

		$this->store(false);

		$this->cache_clean_self();
	}

	function cache_children()
	{
		$res = array(
			bors_load('forum_printable', $this->id()),
			bors_load('forum_topic_rss', $this->id()),
			bors_load('balancer_board_topics_similar', $this->id()),
		);

		if($this->forum_id())
			$res[] = bors_load('balancer_board_forum', $this->forum_id());

//		TODO: убедиться, что модифицируется только автор сообщения при постинге: блоги, все сообщения и т.п.
//		foreach($this->all_users() as $user_id)
//			$res[] = bors_load('balancer_board_user', $user_id);

		return $res;
	}


	function cache_dir()
	{
		return dirname($this->static_file());
	}

	function cached_files()
	{
		$mask = $this->cache_dir().'/../../*/*/t'.$this->id().'*.html';
		$files = glob($mask);

		$mask = str_replace('htdocs', 'htdocs/cache-static', $mask);
		$files += glob($mask);

		foreach(cache_static::find(['target_class_name IN' => ['forum_topic', 'balancer_board_topic', 'forum_printable', 'forum_topic_rss'], 'target_id' => $this->id()])->all() as $x)
			$files[] = $x->id();

		if(!empty($_SERVER['HTTP_REFERER']))
		{
			$ud = parse_url($_SERVER['HTTP_REFERER']);
			if(preg_match("!^(.*/t\d+)(,\d+)?--[^/]+\.html!", $ud['path'], $m))
				$files += glob("/var/www/{$ud['host']}/htdocs/cache-static{$m[1]}*");
		}

//		if(config('is_developer'))
//			~r($this->cache_dir(), $files, $mask);

		return $files;
	}

	function cache_clean_self($page = NULL)
	{
		// Если указана только страница, то удаляется только она и всё.
		if(!$page)
			parent::cache_clean_self($page);

		//TODO: подумать на тему неполной чистки.
		foreach($this->cached_files() as $f)
		{
//			if(!preg_match("!/t\d+,(\d+)--[^/]+\.html$!", $f, $m) || ($m[1] > $this->total_pages()-3))
				if(file_exists($f))
					unlink($f);
		}
	}

	function url_engine() { return 'url_titled'; }
	function url_for_igo() { return 'http://www.balancer.ru/g/t'.$this->id(); }

	function touch_info()
	{
		return array(
			'modify_time' => $this->modify_time(),
			'pages' => $this->total_pages(),
		);
	}

	function touch($user_id, $time = NULL)
	{
		// Инкрементируем число просмотров этой темы.
		$this->visits_inc();

		// Дальше — только для зарегистрированных пользователей. Учёт визитов.
		if(!$user_id)
			return;

		$v = bors_find_first('balancer_board_topics_visit', array('user_id' => $user_id, 'target_object_id' => $this->id()));

		if(!$time)
			$time = time();

		if(!$v)
		{
			$v = bors_new('balancer_board_topics_visit', array(
				'target_class_id' => $this->class_id(),
				'target_object_id' => $this->id(),
				'user_id' => $user_id,
				'count' => 0,
				'last_visit' => $time,
				'first_visit' => $time,
				'last_post_id' => $this->last_post_id(),
				'create_time' => time(),
				'modify_time' => time(),
			));
		}
		else
		{
			if($v->last_visit() > $time)
				$time = $v->last_visit();

			if(!$v->create_time())
				$v->set_create_time(time());
		}

		$v->set_count($v->count() + 1);
		$v->set_last_visit($time);
		$v->set_last_post_id($this->last_post_id());
		$v->set_modify_time(time());
		$v->set_is_disabled(false);
	}

	function visits_counting() { return true; }

	function visits_per_day() { return (86400.0*$this->visits())/($this->last_visit_time() - $this->first_visit_time() + 1); }

	function repaging_posts($page = NULL)
	{
		bors()->changed_save();

		$this->db()->query("
			UPDATE posts AS t
				USE INDEX (`by_tid_ordered`)
				SET t.page = FLOOR((SELECT @rn:= @rn + 1 FROM (SELECT @rn:= -1) s)/{$this->items_per_page()})+1
				WHERE t.topic_id = {$this->id()} AND is_deleted = 0
				ORDER BY t.`order`, t.`posted`, t.id;
		");
		$this->set_is_repaged(1, true);
	}

	function cache_group_parents() { return parent::cache_group_parents() + array("balancer-board-forum-{$this->forum_id()}"); }
	function cache_group_provides() { return parent::cache_group_provides() + array("balancer-board-topic-{$this->id()}"); }

	function keywords_string()
	{
		$kws = $this->keywords_string_db();

		if(!$kws)
			$kws = $this->forum()->keywords_string();

		return $kws;
	}

	function set_keywords_string($words, $db_update=true)
	{
		$this->set_keywords_string_db($words, $db_update);
		if($db_update)
			common_keyword_bind::add($this);
	}

	function keywords() { return array_map('trim', explode(',', $this->keywords_string())); }
	function set_keywords($keywords, $up=true)
	{
		sort($keywords, SORT_LOCALE_STRING);
		$this->set_keywords_string(join(', ', array_unique($keywords)), $up);
		if($up)
			common_keyword_bind::add($this);
		return $keywords;
	}

	function template()
	{
		if(0 && $this->forum()->category()->category_template())
		{
			$app = $this->forum()->category()->bors_append();
			if(!defined('BORS_APPEND'))
				define('BORS_APPEND', $app);

			return $this->forum()->category()->category_template();
		}

		return parent::template();
	}

	function pre_show()
	{
		$me = bors()->user();
		if($me)
			$me->utmx_update();

		if(!$this->forum() || !$this->forum()->can_read())
		{
			template_noindex();
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");
		}

		if($this->page() == 'new')
		{
//			bors_debug::syslog('__go_new_page', "Topic = ".$this->debug_title());
			$go = bors_load('balancer_board_topics_go_new', $this->id());
			return $go->pre_show();
		}

		if($this->page() == 'last')
		{
			bors_debug::syslog('__go_last_page', "Topic = ".$this->debug_title());
			return go($this->url_ex($this->total_pages()));
		}

		if($this->page() > $this->total_pages())
			return go($this->url_ex($this->total_pages()));

		if($this->moved_to())
			return go(bors_load('balancer_board_topic', $this->moved_to())->url_ex($this->page()));

		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".htmlspecialchars($this->title())."'\" />");

		if($this->page() > 1)
			bors_page::link_rel('prev', $this->url_ex($this->page() - 1));

		if($this->page() < $this->total_pages() && $this->total_pages() > 1)
			bors_page::link_rel('next', $this->url_ex($this->page() + 1));

		template_jquery();
//		template_jquery_plugin_autocomplete();
/*
//		http://jqueryui.com/docs/Getting_Started#Click_Download.21
//		http://jqueryui.com/demos/autocomplete/#option-source
//		http://www.learningjquery.com/2010/06/autocomplete-migration-guide
//		template_css('/_bors3rdp/jquery/jquery-ui.css');
		template_css('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		template_jquery();
		template_js_include('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');

		template_js('
$(function() {
	$("#tags").autocomplete({ source: ["aaaa", "bbbbb", "ccccc"]});
});
'		);
*/
		$this->tools()->use_ajax();

		return parent::pre_show();
	}

	function auto_objects()
	{
		return array(
			'owner' => 'balancer_board_user(owner_id)',
			'first_post' => 'balancer_board_post(first_post_id)',
			'last_post' => 'balancer_board_post(last_post_id)',
		);
	}

	function last_visit_time_for_user($user)
	{
		if(!is_object($user))
			return 0;

		if($user->id() == bors()->user_id() && ($lv = $this->get('joined_last_visit')))
			return $lv;

		return intval($this->db()->select('topic_visits', 'UNIX_TIMESTAMP(`modify_ts`)', array(
			'user_id' => $user->id(),
			'topic_id' => $this->id())));
	}

	function was_updated_for_user($user, $for_post = false, $real_visits = false)
	{
		if(!$user)
		{
			bors_debug::syslog('users_error', 'Попытка определить обновлялся ли топик для нерегистрированного пользователя');
			return false;
		}

		$last = $this->last_visit_time_for_user($user);

		if(!$last && !$real_visits)
			$last = $user->previous_session_end();

		if(!$last)
			return true;

		return ($for_post ? $this->last_post_create_time() : $this->modify_time()) > $last;
	}

	function on_delete_pre()
	{
		$this->forum();
		common_keyword_bind::remove($this);
//		parent::on_delete_pre();
	}

	function on_delete_post() { $this->forum()->recalculate(); }

	function fetch_updated_from($time, $format = 'html')
	{
		$updated_posts = bors_find_all('balancer_board_post', array(
			'topic_id' => $this->id(),
			'create_time>' => $time,
			'is_deleted' => false,
			'order' => '`order` DESC, `posted` DESC, `id` DESC',
			'limit' => 25,
		));

		if($format != 'text')
		{
			$html = array();
			foreach(array_reverse($updated_posts) as $p)
			{
				$html[] = $p->html(array(
					'show_title' => false,
					'skip_forums' => true,
				));
			}

			if(!$html)
				return NULL;

			return "<dl class='box'><dt>Форум: {$this->forum()->titled_link()}</dt>
<dd><h2>{$this->title()}</h2></dd></dl>
".join("\n\n", $html);
		}

		$text = array();
		foreach(array_reverse($updated_posts) as $p)
		{
			$text[] = $p->text(array(
				));
		}

		if(!$text)
			return NULL;

		$title = "*   {$this->title()}";

		$div  = str_repeat('-', 72);
		$div2  = str_repeat('=', 72);

		return "\n$div2\n*\n$title\n*\n$div2\n"
			."Форум: {$this->forum()->title()}, {$this->forum()->url()}\n\n\n"
			.join("\n".$div."\n", $text)."\n\n";
	}

	function page_data()
	{
		$last_post = bors_find_first('balancer_board_post', array(
			'topic_id' => $this->id(),
			'`page`=' => max(1,$this->page()),
			'is_deleted' => false,
			'order' => '-sort_order,-create_time,-id',
		));

		$where = array(
			'target_class_name IN' => array('forum_topic', 'balancer_board_topic'),
			'target_object_id' => $this->id(),
		);

		if(($p = $this->args('page')) > 1)
			$where['target_page'] = $p;
		else
			$where[] = 'target_page<2';

		$search_keywords = bors_find_all('bors_referer_search', $where);

		if($this->args('page') == $this->total_pages())
			$page_last_time = time();
		else
			$page_last_time = $last_post ? $last_post->create_time()+1 : NULL;

		return array_merge(parent::page_data(), array(
//			'skip_top_ad' => true,
		), compact('page_last_time', 'search_keywords'));
	}
}
