<?php

class balancer_board_topic_view extends bors_view_container
{
	function container_class() { return 'balancer_board_topic'; }
	function nested_class() { return 'balancer_board_post'; }

	function is_auto_url_mapped_class() { return true; }

	function config_class() { return 'balancer_board_config'; }

	function uri_name() { return 't'; }
	function nav_name() { return truncate($this->title(), 60); }

	function forum() { return $this->topic()->forum(); }

	function url($page = NULL) { return 'http://www.balancer.ru/board/topics/view/'.$this->topic()->id().'/'.($page && $page != 1 ? $page.'.html' : ''); }

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
		return $this->__setc($kws ? '"'.join('","', $this->keywords()) .'"' : '');
	}

	function parents() { return array($this->topic()->forum()); }

	function pre_show()
	{
		if(!$this->topic()->is_repaged() && rand(0,5) == 0)
			$this->topic()->repaging_posts();

		if($this->page() == 'new')
		{
			$me = bors()->user();

			if(!$me || $me->id() < 2)
			{
				$ref = $this->url($this->page());
				return bors_message(ec('Вы не авторизованы на этом домене. Авторизуйтесь, пожалуйста. Если не поможет - попробуйте стереть cookies вашего браузера.'), array('login_form' => true, 'login_referer' => $ref));
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
				$post = bors_load('balancer_board_post', $first_new_post_id);

				if($post = bors_load('balancer_board_post', $first_new_post_id))
					return go($post->url_in_container());
			}

			$this->set_page('last');
		}

		if($this->page() == 'last')
			return go($this->url($this->total_pages()));

		if(!$this->topic()->forum() || !$this->forum()->can_read())
		{
			template_noindex();
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");
		}

		if($this->page() > $this->topic()->total_pages())
			return go($this->url($this->topic()->total_pages()));

		if($this->topic()->moved_to())
			return go(bors_load('balancer_board_topic', $this->topic()->moved_to())->url($this->topic()->page()));

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

	function is_last_page() { return $this->page() == $this->total_pages(); }

	function on_nested_load(&$posts)
	{
		bors_objects_preload($posts, 'owner_id', 'balancer_board_user', 'owner');
	}

	function body_data()
	{

		$data = array(
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

		$data['topic'] = $this->topic();
		$data['forum'] = $this->topic()->forum();
		return array_merge(parent::body_data(), $data);
	}

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

	function keywords() { return array_map('trim', explode(',', $this->object()->keywords())); }
	function keywords_string() { return array_map('trim', explode(',', $this->object()->keywords_string())); }

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
