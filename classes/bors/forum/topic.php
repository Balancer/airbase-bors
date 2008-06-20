<?php

class forum_topic extends forum_abstract
{
	function storage_engine() { return 'storage_db_mysql_smart'; }
	function can_be_empty() { return false; }
	
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'topics'; }

	function uri_name() { return 't'; }

	function fields() { return array($this->main_db_storage() => $this->main_db_fields()); }

	function main_db_fields()
	{
		return array(
			$this->main_table_storage() => $this->main_table_fields(),
		);
	}

	function main_table_fields()
	{
		return array(
			'id',
			'forum_id',
			'title'	=> 'subject',
			'create_time'	=> 'posted',
			'modify_time'=> 'last_post',
			'owner_id'=> 'poster_id',
			'last_poster_name' => 'last_poster',
			'author_name' => 'poster',
			'num_replies',
			'visits' => 'num_views',
			'first_post_id' => 'first_pid',
			'last_post_id' => 'last_post_id',
			'first_visit_time' => 'first_visit',
			'last_visit_time' => 'last_visit',
			'sticky',
			'closed',
		);
	}

	function forum_id() { return $this->stb_forum_id; }
	function owner_id() { return $this->stb_owner_id; }
	function last_poster_name() { return $this->stb_last_poster_name; }
	function num_replies() { return $this->stb_num_replies; }

	function set_forum_id($value, $dbupd) { $this->fset('forum_id', $value, $dbupd); }
	function set_title($value, $dbupd) { $this->fset('title', $value, $dbupd); }
	function set_create_time($value, $dbupd) { $this->fset('create_time', $value, $dbupd); }
	function set_modify_time($value, $dbupd) { $this->fset('modify_time', $value, $dbupd); }
	function set_owner_id($value, $dbupd) { $this->fset('owner_id', $value, $dbupd); }
	function set_last_poster_name($value, $dbupd) { $this->fset('last_poster_name', $value, $dbupd); }
	function set_author_name($value, $dbupd) { $this->fset('author_name', $value, $dbupd); }
	function set_num_replies($num_replies, $db_update) { $this->fset('num_replies', $num_replies, $db_update); }
	function set_visits($num_views, $db_update) { $this->fset('visits', $num_views, $db_update); }
	function set_first_visit_time($value, $db_update) { $this->fset('first_visit_time', $value, $db_update); }
	function set_last_visit_time($value, $db_update) { $this->fset('last_visit_time', $value, $db_update); }
	function set_first_post_id($first_post_id, $db_update) { $this->fset('first_post_id', $first_post_id, $db_update); }
	function set_last_post_id($last_post_id, $db_update) { $this->fset('last_post_id', $last_post_id, $db_update); }

	function set_sticky($value, $db_update) { $this->fset('sticky', $value, $db_update); }
	function set_closed($value, $db_update) { $this->fset('closed', $value, $db_update); }

	private $forum = false;
	function forum()
	{
		if($this->forum === false)
			if(!$this->forum_id())
				debug_exit('Empty forum_id for topic '.$this->id());
			else
				$this->forum = object_load('forum_forum', $this->forum_id()); 
			
		return $this->forum;
	}
	
	function first_post() { return object_load('forum_post', $this->first_post_id()); }
	function last_post() { return object_load('forum_post', $this->last_post_id()); }
		
	function parents() { return array("forum_forum://".$this->forum_id()); }

	function is_sticky() { return $this->sticky() ? true : false; }
	function is_closed() { return $this->closed() ? true : false; }

	function preParseProcess()
	{
		if($this->page() == 'new')
		{
			$me = bors()->user();
		
			if(!$me || $me->id() < 2)
				return bors_message(ec('Вы не авторизованы на этом домене. Авторизуйтесь, пожалуйста. Если не поможет - попробуйте стереть cookies вашего браузера.'), array('login_form' => true, 'login_referer' => $this->url($this->page())));

			$uid = $me->id();
			$x = $this->db()->select('topic_visits', 'last_visit, last_post_id', array('user_id='=>$uid, 'topic_id='=>$this->id()));
			$first_new_post_id = @$x['last_post_id'];
//			if(!$first_new_post_id)
			{
				$last_visit = $x['last_visit'];
				$where = array('topic_id='=>$this->id(), 'posted>' => $last_visit);
//				set_loglevel(10,NULL);
				$first_new_post_id = intval($this->db()->select('posts', 'MIN(id)', $where));
//				set_loglevel(2);
//				exit();
			}
					
			if($first_new_post_id)
			{
				$post = object_load('forum_post', $first_new_post_id);

				if($post)
					return go($post->url_in_topic());
			}

			$this->set_page('last');
		}

		if($this->page() == 'last')
			return go($this->url($this->total_pages()));

		if(!$this->forum()->can_read())
		{
			templates_noindex();
			return bors_message("Извините, доступ к этому ресурсу закрыт для Вас");
		}
		
		return false;
	}

	function body()
	{
		$GLOBALS['cms']['cache_disabled'] = true;

		require_once("engines/smarty/assign.php");
		$data = array();

		$data['posts'] = $this->posts();

		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".addslashes($this->title())."'\" />");

		$data['this'] = $this;

		return template_assign_data("templates/TopicBody.html", $data);
	}

	private $__posts_map = NULL;
	private $__pages_loades = array();
	private $__add_posts = NULL;
	private $__add_post_ids = array();
	private $__add_posts_map = array();

	private function raw_posts()
	{
		$page_id = $this->page().','.$this->items_per_page();
		if(isset($this->__pages_loaded[$page_id]))
			return $this->__pages_loaded[$page_id];

		$where = array(
			'where' => array('topic_id=' => intval($this->id())),
			'order' => 'id',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
		);

		$this->__pages_loaded[$page_id] = objects_array('forum_post', $where);
		
		return $this->__pages_loaded[$page_id];
	}

	private function add_post($pid)
	{
		if(!in_array($pid, $this->__posts_ids))
			$this->__add_post_ids[] = $pid;
	}

	private function add_posts()
	{
		if($this->__add_posts !== NULL)
			return $this->__add_posts;

		if(!$this->__add_post_ids)
			return $this->__add_posts = array();

		$this->__add_posts = objects_array('forum_post', array(
				'where' => array('post_id IN('.join(',', array_unique($this->__add_post_ids)).')'),
				'order' => 'id',
		));
		
		for($i = 0; $i<count($this->__add_posts); $i++)
		{
			$post = &$this->__add_posts[$i];
			$this->__add_posts_map[$post->id()] = &$post;
		}
		
		return $this->__add_posts;
	}

	private $__all_posts_ids;
	function all_posts_ids()
	{
		if(isset($this->__all_posts_ids))
			return $this->__all_posts_ids;
		
		return $this->__all_posts_ids = $this->db()->select_array('posts', 'id', array('topic_id='=>$this->id(), 'order' => 'posted'));
	}

	private $__posts_ids;
	function posts_ids()
	{
		$page_id = $this->page_id();
		if(isset($this->__posts_ids[$page_id]))
			return $this->__posts_ids[$page_id];

		$post_ids = array();
		$posts = $this->raw_posts();

		for($i = 0; $i < count($posts); $i++)
		{
			$post = &$posts[$i];
			$pid = $post->id();
			$post_ids[] = $pid;
			$this->__posts_map[$pid] = &$post;
		}

		return $this->__posts_ids[$page_id] = $post_ids;
	}

	function page_id() { return $this->page().','.$this->items_per_page(); }

	private $__posts;
	protected function posts()
	{
		$page_id = $this->page_id();
		if(isset($this->__posts[$page_id]))
			return $this->__posts[$page_id];

		$user_ids = array();

		foreach($this->posts_ids() as $pid)
		{
			$post = &$this->__posts_map[$pid];

			$user_ids[] = $post->owner_id();
			$this->add_post($post->answer_to_id());
		}

		if(empty($this->__posts_ids[$page_id]))
			return array();

		$post_ids = 'id IN('.join(',', $this->__posts_ids[$page_id]).')';

		foreach($this->db($this->main_db_storage())->select_array('messages', 'id,message,html', array($post_ids)) as $x)
		{
			$post = &$this->__posts_map[$x['id']];
			$post->set_source($x['message'], false);
			$post->set_body($x['html'], false);
		}

		$user_ids = 'id IN('.join(',', array_unique($user_ids)).')';
		$users = objects_array('forum_user', array($user_ids));
		$users_map = array();
		for($i = 0; $i < count($users); $i++)
		{
			$user = &$users[$i];
			$uid = $user->id();
			$users_map[$uid] = &$user;
		}

		$attaches_map = array();
		foreach(objects_array('forum_attach', array('post_'.$post_ids)) as $a)
			$attaches_map[$a->post_id()][] = $a;

		$this->add_posts();
		
		$posts = $this->raw_posts();
		for($i = 0; $i < count($posts); $i++)
		{
			$post = &$posts[$i];
			$pid = $post->id();
			$uid = $post->owner_id();
//			$post->set_owner($users_map[$uid], false);
			
			$attaches = @$attaches_map[$pid];
			$post->set_attaches($attaches ? $attaches : array(), false);
				
			if($aid = $post->answer_to_id())
			{
				$answer = @$this->__add_posts_map[$aid];
//				if(!$answer)
//					$answer = $this->__posts_map[$aid];

				if($answer)
					$post->set_answer_to($answer, false);
			}
		}

		return $this->__posts[$page_id] = $this->raw_posts();
	}

	function items_per_page() { return 25; }

	function total_pages() { return intval($this->num_replies() / $this->items_per_page()) + 1; }

	function pages_links()
	{
		if($this->total_pages() < 2)
			return "";

		include_once('funcs/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), 15));
	}

	function title_pages_links()
	{
		if($this->total_pages() < 2)
			return "";

		include_once('funcs/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), 5, false));
	}


	function cache_children()
	{
		$res = array(
			object_load('forum_forum', $this->forum_id()),
			object_load('forum_printable', $this->id()),
			object_load('forum_topic_rss', $this->id()),
		);

		foreach($this->all_users() as $user_id)
			$res[] = object_load('forum_user', $user_id);
			
		return $res;
	}

	function all_users()
	{
		$db = &new DataBase('punbb');
		return $db->get_array("SELECT DISTINCT poster_id FROM posts WHERE topic_id={$this->id}");
	}
		
	function cache_static() { return $this->forum()->is_public_access() ? 86400*30 : 0; }
	function base_url() { return $this->forum()->category()->category_base_full(); }
		
	function title_url()
	{
		return "<a href=\"".$this->url()."\">".$this->title()."</a>";
	}

	function rss_url() { return $this->base_url().strftime("%Y/%m/", $this->modify_time())."topic-".$this->id()."-rss.xml"; }

	function search_source()
	{
		$result = array();
	
		$db = &new DataBase('punbb');

		$start_from = ($this->page() - 1) * $this->items_per_page();

		$query = "SELECT poster, message FROM posts INNER JOIN messages ON posts.id = messages.id WHERE topic_id={$this->id()} ORDER BY posts.id LIMIT $start_from, ".$this->items_per_page();
			
		$posts = $db->get_array($query);

		$data['posts'] = array();

		foreach($posts as $x)
		{
//			$post = class_load('forum_post', $pid);
			if($x['message'])
				$result[] = $x['poster'].":\n---------------\n".$x['message'];
		}
		
		return join("\n============================\n\n", $result);
	}
	
	function page_by_post_id($post_id)
	{
		$post_id = intval($post_id);
	
		$db = &new DataBase('punbb');

		$posts = $db->get_array("SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY posted");

		for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
			if($posts[$i] == $post_id)
				return intval( $i / 25) + 1;
	}

	function recalculate()
	{
		bors()->changed_save();
		
		$db = &new driver_mysql('punbb');
		$num_replies = $db->select('posts', 'COUNT(*)', array('topic_id='=>$this->id())) - 1;
//		echo "Num repl of {$this->id()} =   $num_replies<br />\n";
		$this->set_num_replies($num_replies, true);
		$last_pid = $db->select('posts', 'MAX(id)', array('topic_id='=>$this->id()));
		$this->set_last_post_id($last_pid, true);
		$last_post = object_load('forum_post', $last_pid);
		$this->set_modify_time($last_post->create_time(true), true);
		$this->set_last_poster_name($last_post->owner()->title(), true);

		bors()->changed_save();

		$this->cache_clean_self();
		
		if($printable = object_load('forum_printable', $this->id()))
			$printable->cache_clean_self();
	}

	function url_engine() { return 'url_titled'; }

	function touch($user_id)
	{
		$visits = intval($this->db()->select('topic_visits', 'count', array('user_id=' => $user_id, 'topic_id=' => $this->id()))) + 1;

		$data = array(
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
			$this->db()->update('topic_visits', "user_id=".intval($user_id)." AND topic_id=".intval($this->id()), $data);
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
}
