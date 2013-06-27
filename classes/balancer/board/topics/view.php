<?php

if(bors()->user_id() == 10000)
{
	//config_set('debug_mysql_queries_log', true); // — только строки запросов, без стека
//	config_set('debug_mysql_queries_log', 20);
}

class balancer_board_topics_view extends bors_view_container
{
	function container_class() { return 'balancer_board_topic'; }
	function nested_class() { return 'balancer_board_post'; }

	function auto_map() { return true; }

	function config_class() { return 'balancer_board_config'; }

	function uri_name() { return 't'; }
	function nav_name() { return truncate($this->title(), 60); }

	function use_bootstrap() { return config('is_developer'); }

	function where()
	{
		return array_merge(parent::where(), array(
			'is_deleted' => false,
		));
	}

	function order() { return 'sort_order,create_time'; }

	function forum() { return $this->topic()->forum(); }

//	function url($page = NULL) { return 'http://www.balancer.ru/board/topics/view/'.$this->topic()->id().'/'.($page && $page != 1 ? $page.'.html' : ''); }

	function keywords_linked()
	{
		if($this->__havefc())
			return $this->__lastc();

		require_once('inc/airbase_keywords.php');
		$kws = $this->keywords_string();
		return $this->__setc($kws ? airbase_keywords_linkify($kws, '', 'bootstrap') : '');
	}

	function keywords_linked_q()
	{
		if($this->__havefc())
			return $this->__lastc();

		require_once('inc/airbase_keywords.php');
		$kws = $this->keywords_string();
//		return $this->__setc($kws ? '"'.join('","', preg_split('/\s*,\s*/', addslashes(airbase_keywords_linkify($kws)))) .'"' : '');
		return $this->__setc($kws ? '"'.join('","', $this->keywords()) .'"' : '');
	}

	function parents() { return array($this->topic()->forum()); }

	function body()
	{
		$body_cache = new Cache();
		$state = $body_cache->get('bors_page_body-v3.alt', $this->internal_uri_ascii().':'.$this->page().':'.(object_property(bors()->user(), 'group')).':'.$this->modify_time());
//		if($state)
//			return $this->attr['body'] = bors_lcml::output_parse($body_cache->last().'<!-- cached -->');

		return bors_lcml::output_parse($body_cache->set(parent::body(), 86400));
	}

	function pre_show()
	{
		$topic = $this->topic();
		if($topic->page() == $topic->total_pages())
			header("X-Accel-Expires: 30");
		elseif($this->page() >= $topic->total_pages() - 2)
			header("X-Accel-Expires: 600");
		else
			header("X-Accel-Expires: 86400");

		if($this->use_bootstrap())
		{
			twitter_bootstrap::load();
			bors_use('/_bal/css/bootstrap-bb-append.css');
		}

		if(!$topic->is_repaged() && rand(0,5) == 0)
			$topic->repaging_posts();

		if($this->page() == 'new')
		{
			$me = bors()->user();

			if(!$me || $me->id() < 2)
			{
				$ref = $this->url($this->page());
				return bors_message(ec('Вы не авторизованы на этом домене. Авторизуйтесь, пожалуйста. Если не поможет - попробуйте стереть cookies вашего браузера.'), array('login_form' => true, 'login_referer' => $ref));
			}

			$uid = $me->id();
			$v = bors_find_first('balancer_board_topics_visit', array('user_id' => $uid, 'topic_id' => $this->id()));
			$last_visit = object_property($v, 'last_visit');

			if(empty($last_visit))
			{
				$x = bors_find_first('balancer_board_topics_visit', array('last_visit>' => 0, 'order' => 'last_visit'));
				if($x)
					$last_visit = $x->last_visit();
				else
					$last_visit = 0;
			}

			$first_new_post_id = intval(object_property(bors_find_first('balancer_board_post', array(
				'topic_id' => $this->id(),
				'posted>' => $last_visit,
				'order' => 'id',
			)), 'id'));

			if($first_new_post_id)
			{
				$post = bors_load('balancer_board_post', $first_new_post_id);

				if($post = bors_load('balancer_board_post', $first_new_post_id))
					return go($post->url_in_container());
			}

			$this->set_page('last');
		}

		if($this->page() == 'last')
			return go($this->url($this->total_pages()));

		if(!$topic->forum() || !$this->forum()->can_read())
		{
			template_noindex();
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");
		}

		if($this->page() > $topic->total_pages())
			return go($this->url($topic->total_pages()));

		if($topic->moved_to())
			return go(bors_load('balancer_board_topic', $topic->moved_to())->url($topic->page()));

		template_jquery();
		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".htmlspecialchars($this->title())."'\" />");

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

	function is_last_page() { return $this->page() == $this->topic()->total_pages(); }

	function body_data()
	{
		$data = array(
			'is_last_page' => $this->is_last_page(),
		);

		if($this->is_last_page())
		{
			$data['last_actions'] = array_reverse(bors_find_all('balancer_board_action', array(
				'target_class_name IN' => array($this->topic()->class_name(), $this->class_name(), $this->topic()->extends_class_name()),
				'target_object_id' => $this->id(),
				'order' => '-create_time',
				'group' => 'target_class_name, target_object_id, message',
				'limit' => 20,
			)));

			bors_objects_preload($data['last_actions'], 'owner_id', 'balancer_board_user', 'owner');
		}

		$data['topic'] = $this->topic();
		$data['forum'] = $this->topic()->forum();

		if($this->use_bootstrap())
		{
			$data['tcfg'] = bors_load('balancer_board_themes_bootstrap', NULL);
			$data['pagination'] = $this->pages_links_list(array(
				'div_css' => 'pagination pagination-centered pagination-small',
				'li_current_css' => 'active',
				'li_skip_css' => 'disabled',
				'skip_title' => true,
			));
		}
		else
		{
			$data['tcfg'] = bors_load('balancer_board_themes_default', NULL);
			$data['pagination'] = $this->pages_links_nul();
		}

		$data['use_bootstrap'] = $this->use_bootstrap();

		return array_merge(parent::body_data(), $data);
	}

	function on_items_load(&$items)
	{
		parent::on_items_load($items);
		$owners = bors_objects_preload($items, 'owner_id', 'balancer_board_user', 'owner');
		bors_objects_preload($owners, 'group_id', 'balancer_board_group', 'group');
		bors_objects_preload($items, 'answer_to_id', 'balancer_board_post', 'answer_to');

		$first_post_time = time()+1;
		$last_post_time = -1;
		foreach($items as $p)
		{
			if($p->create_time() > $last_post_time)
				$last_post_time = $p->create_time();

			if($p->create_time() < $first_post_time)
				$first_post_time = $p->create_time();
		}

		$post_ids = bors_field_array_extract($items, 'id');

		// Прописываем изменения репутации по постингам.
		$reps = bors_find_all('airbase_user_reputation', array(
			'target_class_name IN' => array('forum_post', 'balancer_board_post'),
			'target_object_id IN' => $post_ids,
			'order' => 'target_object_id, id',
		));

		foreach($reps as $r)
		{
			if($r->is_deleted())
				continue;

			$post = $items[$r->target_object_id()];
			$post_reps = $post->get('reputation_records', array());
			$post_reps[] = $r;
			$post->set_attr('reputation_records', $post_reps);
		}

		$prev_post_time = $this->page() > 1 ? $first_post_time : 0;
		$next_post = $this->is_last_page() ? NULL : bors_find_first('balancer_board_post', array('topic_id' => $this->id(), 'order' => '`order`, posted', 'create_time>' => $last_post_time));

		$next_post_time = object_property($next_post, 'create_time', time()+1);

		$actions = bors_find_all('balancer_board_action', array(
			'create_time BETWEEN' => array($prev_post_time, $next_post_time),
			'target_class_name IN' => array($this->topic()->class_name(), $this->topic()->extends_class_name(), $this->topic()->new_class_name(), $this->class_name()),
			'target_object_id' => $this->id(),
			'order' => 'create_time',
		));

		$post_values = array_values($items);

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
	}

	function model() { return bors_load('balancer_board_topic', $this->id()); }

	function items_per_page() { return 25; }
	function items_around_page() { return 12; }

	function is_public_access() { return $this->topic()->is_public_access(); }

	function cache_static() { return $this->is_public_access() && config('static_forum') ? rand(86400, 86400*3) : 0; }

	function base_url() { return $this->topic()->forum_id() && $this->forum() ? $this->forum()->category()->category_base_full() : '/'; }

	function title_url()
	{
		return "<a href=\"".$this->url()."\">".$this->title()."</a>";
	}

	function rss_url() { return $this->base_url().strftime("%Y/%m/", $this->modify_time())."topic-".$this->id()."-rss.xml"; }

	function page_by_post_id($post_id)
	{
		$post_id = intval($post_id);

		$posts = $this->db()->get_array("SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY `order`,posted");

		for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
			if($posts[$i] == $post_id)
				return intval( $i / 25) + 1;
	}

	function cache_dir()
	{
		return dirname($this->static_file());
	}

	function url_engine() { return 'url_titled'; }

	function touch($user_id, $time = NULL)
	{
		$this->topic()->touch($user_id, $time);
	}

	function visits_counting() { return true; }

	function visits_per_day() { return (86400.0*$this->visits())/($this->last_visit_time() - $this->first_visit_time() + 1); }

	function keywords() { return array_map('trim', $this->model()->keywords()); }
	function keywords_string() { return join(",", array_map('trim', explode(',', $this->object()->keywords_string()))); }
//	function keywords_string() { return $this->model()->keywords_string(); }

	function template()
	{
		if($this->use_bootstrap())
			return 'xfile:balancer/board/topic.tpl';

		if($this->forum()->category()->category_template())
		{
			$app = $this->forum()->category()->bors_append();
			if(!defined('BORS_APPEND'))
				define('BORS_APPEND', $app);

			return $this->forum()->category()->category_template();
		}

		return parent::template();
	}

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

	function page_data()
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

		return array_merge(parent::page_data(), compact('search_keywords'));
	}
}
