<?php

class forum_topic extends base_page_db
{
	function config_class() { return 'balancer_board_config'; }

	function storage_engine() { return 'storage_db_mysql_smart'; }
//	function storage_engine() { return 'bors_storage_mysql'; }
	function can_be_empty() { return false; }

	function new_class_name() { return 'balancer_board_topic'; }

	function uri_name() { return 't'; }
	function nav_name() { return truncate($this->title(), 60); }

	function main_db() { return config('punbb.database', 'punbb'); }
	function main_table() { return 'topics'; }

	function main_table_fields()
	{
		return array(
			'id',
			'forum_id_raw' => 'forum_id',
			'title'	=> 'subject',
			'description',
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
		);
	}

function set_forum_id($v, $dbup)
{
	if(($new_forum = object_load('balancer_board_forum', $v)))
		$this->set_is_public($new_forum->is_public(), true);

	return $this->set('forum_id_raw', $v, $dbup);
}

function forum_id() { return @$this->data['forum_id_raw']; }

function owner_id() { return @$this->data['owner_id']; }
function set_owner_id($v, $dbup) { return $this->set('owner_id', $v, $dbup); }
function last_poster_name() { return @$this->data['last_poster_name']; }
function set_last_poster_name($v, $dbup) { return $this->set('last_poster_name', $v, $dbup); }
function author_name() { return @$this->data['author_name']; }
function set_author_name($v, $dbup) { return $this->set('author_name', $v, $dbup); }
function num_replies() { return @$this->data['num_replies']; }
function set_num_replies($v, $dbup) { return $this->set('num_replies', $v, $dbup); }
function is_repaged() { return @$this->data['is_repaged']; }
function set_is_repaged($v, $dbup) { return $this->set('is_repaged', $v, $dbup); }
function visits() { return @$this->data['visits']; }
function set_visits($v, $dbup) { return $this->set('visits', $v, $dbup); }
function first_post_id() { return @$this->data['first_post_id']; }
function set_first_post_id($v, $dbup) { return $this->set('first_post_id', $v, $dbup); }
function last_post_id() { return @$this->data['last_post_id']; }
function set_last_post_id($v, $dbup) { return $this->set('last_post_id', $v, $dbup); }
function first_visit_time() { return @$this->data['first_visit_time']; }
function set_first_visit_time($v, $dbup) { return $this->set('first_visit_time', $v, $dbup); }
function last_visit_time() { return @$this->data['last_visit_time']; }
function set_last_visit_time($v, $dbup) { return $this->set('last_visit_time', $v, $dbup); }
function last_edit_time() { return @$this->data['last_edit_time']; }
function set_last_edit_time($v, $dbup) { return $this->set('last_edit_time', $v, $dbup); }
function sticky() { return @$this->data['sticky']; }
function set_sticky($v, $dbup) { return $this->set('sticky', $v, $dbup); }
function closed() { return @$this->data['closed']; }
function set_closed($v, $dbup) { return $this->set('closed', $v, $dbup); }
function keywords_string_db() { return @$this->data['keywords_string_db']; }
function set_keywords_string_db($v, $dbup) { return $this->set('keywords_string_db', $v, $dbup); }

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
				$this->forum = object_load('balancer_board_forum', $this->forum_id()); 

		return $this->set_forum($this->forum);
	}

	function set_forum($forum) { return $this->set_attr('forum', $forum); }

	function parents() { return array("forum_forum://".$this->forum_id()); }

	function is_sticky() { return $this->sticky() ? true : false; }
	function is_closed() { return $this->closed() ? true : false; }

	function pre_parse()
	{
		if($this->page() == 'new')
		{
			$me = bors()->user();

			if(!$me || $me->id() < 2)
			{
				$ref = $this->url($this->page());
				return bors_message(ec('Вы не авторизованы на этом домене. Авторизуйтесь, пожалуйста. Если не поможет - попробуйте стереть cookies вашего браузера.'), array(
					'login_form' => true,
					'login_referer' => $ref,
					'template' => $this->template(),
				));
			}

			$uid = $me->id();
			$x = $this->db()->select('topic_visits', 'last_visit, last_post_id', array('user_id='=>$uid, 'topic_id='=>$this->id()));
			$last_visit = @$x['last_visit'];

			if(empty($last_visit))
				$last_visit = $this->db()->select('topic_visits', 'MIN(last_visit)', array('last_visit>' => 0));

			$first_new_post_id = intval($this->db()->select('posts', 'MIN(id)', array(
				'topic_id' => $this->id(),
				'posted>' => $last_visit,
			)));

			if($first_new_post_id)
			{
				$post = object_load('balancer_board_post', $first_new_post_id);

				if($post = object_load('balancer_board_post', $first_new_post_id))
					return go($post->url_in_container());
			}

			$this->set_page('last');
		}

		if($this->page() == 'last')
			return go($this->url($this->total_pages()));

		if(!$this->forum() || !$this->forum()->can_read())
		{
			template_noindex();
			return bors_message("Извините, доступ к этому ресурсу закрыт для Вас");
		}

		return false;
	}

	function is_last_page() { return $this->page() == $this->total_pages(); }

	function body()
	{
		if(!$this->is_repaged() && rand(0,5) == 0)
			$this->repaging_posts();

		$body_cache = new Cache();
		if($body_cache->get('bors_page_body-v2', $this->internal_uri_ascii().':'.$this->page().':'.(object_property(bors()->user(), 'group')).':'.$this->modify_time()))
			return $this->attr['body'] = $body_cache->last().'<!-- cached -->';

		$GLOBALS['cms']['cache_disabled'] = true;

		require_once("engines/smarty/assign.php");

		$posts = $this->posts();
		$post_ids = array_keys($posts);
		$blogs = bors_find_all('balancer_board_blog', array('id IN' => $post_ids, 'by_id' => true));

		foreach($blogs as $blog_id => $blog)
		{
			$posts[$blog_id]->set_blog($blog, false);
			if($kws = $blog->keywords())
				$posts[$blog_id]->set_keyword_links(balancer_blogs_tag::linkify($kws, '', ' | ', true), false);
		}

		$data = array(
			'posts' => array_values($posts),
			'is_last_page' => $this->is_last_page(),
		);

		if($this->is_last_page())
		{
			$data['last_actions'] = array_reverse(objects_array('balancer_board_action', array(
				'target_class_name' => $this->class_name(),
				'target_object_id' => $this->id(),
				'order' => '-create_time',
				'group' => 'target_class_name, target_object_id, message',
				'limit' => 20,
			)));

			bors_objects_preload($data['last_actions'], 'owner_id', 'balancer_board_user', 'owner');
		}

		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".htmlspecialchars($this->title())."'\" />");

		bors_objects_preload($data['posts'], 'owner_id', 'balancer_board_user', 'owner');

		$data['this'] = $this;
		return $body_cache->set(template_assign_data("xfile:forum/topic.html", $data), 86400);
	}

	private $__all_posts_ids;
	function all_posts_ids()
	{
		if(isset($this->__all_posts_ids))
			return $this->__all_posts_ids;

		return $this->__all_posts_ids = $this->db()->select_array('posts', 'id', array('topic_id' => $this->id(), 'order' => '`order`,posted'));
	}

	protected function posts_ids($page = NULL)
	{
		if(!$page)
			$page = $this->page();

		$data = array(
			'topic_id' => $this->id(),
			'order' => '`order`,posted',
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

	protected function posts($page = NULL, $paging = true)
	{
		if(!$page)
			$page = $this->page();

		$data = array(
			'topic_id' => $this->id(),
			'order' => '`order`,posted',
			'by_id' => true,
		);

		if($paging && $this->is_repaged())
			$data['`page` = '] = intval($page);
		else
		{
			$data['page'] = $page;
			$data['per_page'] = $this->items_per_page();
		}

		return objects_array('balancer_board_post', $data);
	}

	function items_per_page() { return 25; }
	function items_around_page() { return 12; }

	function total_pages() { return intval($this->num_replies() / $this->items_per_page()) + 1; }

	function pages_links()
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

		include_once('inc/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), 5, false));
	}


	function all_users()
	{
		return $this->db()->get_array("SELECT DISTINCT poster_id FROM posts WHERE topic_id={$this->id}");
	}

	function is_public_access() { return $this->forum_id() && $this->forum()->is_public_access(); }

	function cache_static() { return $this->is_public_access() && config('static_forum') ? rand(86400, 86400*3) : 0; }

	function base_url() { return $this->forum_id() && $this->forum() ? $this->forum()->category()->category_base_full() : '/'; }

	function title_url()
	{
		return "<a href=\"".$this->url()."\">".$this->title()."</a>";
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

		$posts = $this->db()->get_array("SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY `order`,posted");

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

	function recalculate($full_repaging = true)
	{
		bors()->changed_save(); // Сохраняем всё. А то в памяти могут быть модифицированные объекты, с которыми сейчас будем работать.

		$this->set_is_public($this->forum()->is_public(), true);

		$num_replies = $this->db()->select('posts', 'COUNT(*)', array('topic_id='=>$this->id())) - 1;
		$this->set_num_replies($num_replies, true);
		$first_pid = $this->db()->select('posts', 'MIN(id)', array('topic_id='=>$this->id()));
		$this->set_first_post_id($first_pid, true);
		if($first_post = object_load('balancer_board_post', $first_pid))
		{
			$this->set_create_time($first_post->create_time(true), true);
			$this->set_author_name($first_post->author_name(), true);
			$this->set_owner_id($first_post->owner() ? $first_post->owner()->id() : NULL, true);
		}
		else
			debug_hidden_log('post_error', "Unknown first post $first_pid in {$this}->recalculate()");

		if($last_pid = $this->db()->select('posts', 'MAX(id)', array('topic_id='=>$this->id())))
		{
			$this->set_last_post_id($last_pid, true);
			$last_post = object_load('balancer_board_post', $last_pid);
			if($this->get('last_post_create_time') < $last_post->create_time(true))
				$this->set_last_post_create_time($last_post->create_time(true), true);
			$this->set_last_poster_name($last_post->author_name(), true);

		}
		else
			debug_hidden_log('post_error', "Unknown last post $first_pid in {$this}->recalculate()");

		$this->repaging_posts($full_repaging ? 1 : -1);

		foreach($this->posts() as $pid => $p)
			$p->set_body(NULL, true);

		$this->set_modify_time(time(), true);
		$this->store(false);

		$this->cache_clean_self();
	}

	function cache_children()
	{
		$res = array(
			object_load('forum_printable', $this->id()),
			object_load('forum_topic_rss', $this->id()),
			bors_load('balancer_board_topics_similar', $this->id()),
		);

		if($this->forum_id())
			$res[] = object_load('forum_forum', $this->forum_id());

//		TODO: убедиться, что модифицируется только автор сообщения при постинге: блоги, все сообщения и т.п.
//		foreach($this->all_users() as $user_id)
//			$res[] = object_load('balancer_board_user', $user_id);

		return $res;
	}


	function cache_dir()
	{
		return dirname($this->static_file());
	}

	function cache_clean_self($page = NULL)
	{
		parent::cache_clean_self($page);

		//TODO: подумать на тему неполной чистки.
		foreach(glob($this->cache_dir().'/t'.$this->id().'*.html') as $f)
			@unlink($f);
	}

	function url_engine() { return 'url_titled'; }
	function url_for_igo() { return 'http://balancer.ru/g/t'.$this->id(); }

	function touch($user_id)
	{
		$visits = intval($this->db()->select('topic_visits', 'count', array('user_id=' => $user_id, 'topic_id=' => $this->id()))) + 1;

		$data = array(
			'target_class_id' => $this->class_id(),
			'topic_id' => $this->id(),
			'user_id' => $user_id,
			'count' => $visits,
			'last_visit' => time(),
			'last_post_id' => $this->last_post_id(),
		);

		if($visits == 1)
		{
			$data['first_visit'] = time();
			$this->db()->replace('topic_visits', $data);
		}
		else
		{
			$this->db()->update('topic_visits', array(
					'user_id' => intval($user_id),
					'topic_id' => intval($this->id())
				), $data);
		}
	}

	function visits_counting() { return true; }

	function visits_per_day() { return (86400.0*$this->visits())/($this->last_visit_time() - $this->first_visit_time() + 1); }

	private $owner = false;
	function owner()
	{
		if($this->owner === false)	
			$this->owner = object_load('bors_user', $this->owner_id());
		
		return $this->owner;
	}

	function repaging_posts($page = NULL)
	{
		bors()->changed_save();

		$this->db()->query("
			UPDATE posts AS t 
				SET t.page = FLOOR((SELECT @rn:= @rn + 1 FROM (SELECT @rn:= -1) s)/{$this->items_per_page()})+1 
				WHERE t.topic_id = {$this->id()}
				ORDER BY t.`order`, t.`posted`;
		");
		$this->set_is_repaged(1, true);
	}

//	function cache_groups() { return parent::cache_groups()." airbase-forum-topic-{$this->id()}"; }
	function cache_groups_parent() { return parent::cache_groups_parent()." airbase-board-forum-{$this->forum_id()}"; }

	function keywords_string()
	{
		if($kws = $this->keywords_string_db())
			return $kws;
		else
			return $this->forum()->keywords_string();
	}

	function set_keywords_string($words, $db_update)
	{
		$this->set_keywords_string_db($words, $db_update);
		if($db_update)
			common_keyword_bind::add($this);
	}

	function keywords() { return array_map('trim', explode(',', $this->keywords_string())); }
	function set_keywords($keywords, $up)
	{
		sort($keywords, SORT_LOCALE_STRING);
		$this->set_keywords_string(join(', ', array_unique($keywords)), $up);
		if($up)
			common_keyword_bind::add($this);
		return $keywords;
	}

	function template()
	{
		if($this->forum()->category()->category_template())
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

		if($this->page() > $this->total_pages())
			return go($this->url($this->total_pages()));

		if($this->moved_to())
			return go(object_load('balancer_board_topic', $this->moved_to())->url($this->page()));

		template_jquery();
//		template_jquery_plugin_lazyload();
		template_jquery_plugin_lazyload_ad();
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
			'first_post' => 'balancer_board_post(first_post_id)',
			'last_post' => 'balancer_board_post(last_post_id)',
		);
	}

	function last_visit_time_for_user($user)
	{
		if(!is_object($user))
			return 0;

		return intval($this->db()->select('topic_visits', 'last_visit', array(
			'user_id=' => $user->id(),
			'topic_id=' => $this->id())));
	}

	function was_updated_for_user($user, $for_post = false)
	{
		if(!$user)
		{
			debug_hidden_log('users_error', 'Попытка определить обновлялся ли топик для нерегистрированного пользователя');
			return false;
		}

		$last = $this->last_visit_time_for_user($user);
		if(!$last)
			$last = $user->previous_session_end();

		return ($for_post ? $this->last_post_create_time() : $this->modify_time()) > $last;
	}

	function on_delete_pre() { $this->forum(); }
	function on_delete_post() { $this->forum()->recalculate(); }

	function fetch_updated_from($time, $format = 'html')
	{
		$updated_posts = bors_find_all('balancer_board_post', array(
			'topic_id' => $this->id(),
			'create_time>' => $time,
			'is_deleted' => false,
			'order' => '`order` DESC, `posted` DESC',
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

	function global_data()
	{
		$where = array(
			'target_class_name IN' => array('forum_topic', 'balancer_board_topic'),
			'target_object_id' => $this->id(),
		);

		if(($p = $this->args('page')) > 1)
			$where['target_page'] = $p;
		else
			$where[] = 'target_page<2';

		$search_keywords = bors_find_all('bors_referer_search', $where);

		return array_merge(parent::global_data(), compact('search_keywords'));
	}
}
