<?php

class airbase_board_topic extends base_page_db
{
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'topics'; }

	function uri_name() { return 't'; }

	function fields()
	{
		return array('punbb' => array(
			'topics' => array(
				'id',
				'forum_id',
				'title'	=> 'subject',
				'description',
				'create_time'	=> 'posted',
				'modify_time'=> 'last_post',
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
				'closed',
			),
			
//			'inner forums(forum_id)' => array(
//				'forum_title' => 'forum_name',
//			),
		));
	}

	function forum_id() { return $this->stb_forum_id; }
	function owner_id() { return $this->stb_owner_id; }
	function last_poster_name() { return $this->stb_last_poster_name; }
	function num_replies() { return $this->stb_num_replies; }

	function set_forum_id($value, $dbupd) { return $this->fset('forum_id', $value, $dbupd); }
	function set_title($value, $dbupd) { return $this->fset('title', $value, $dbupd); }
	function set_create_time($value, $dbupd) { return $this->fset('create_time', $value, $dbupd); }
	function set_modify_time($value, $dbupd) { return $this->fset('modify_time', $value, $dbupd); }
	function set_owner_id($value, $dbupd) { return $this->fset('owner_id', $value, $dbupd); }
	function set_last_poster_name($value, $dbupd) { return $this->fset('last_poster_name', $value, $dbupd); }
	function set_author_name($value, $dbupd) { return $this->fset('author_name', $value, $dbupd); }
	function set_num_replies($num_replies, $db_update) { return $this->fset('num_replies', $num_replies, $db_update); }
	function set_visits($num_views, $db_update) { return $this->fset('visits', $num_views, $db_update); }
	function set_first_visit_time($value, $db_update) { return $this->fset('first_visit_time', $value, $db_update); }
	function set_last_visit_time($value, $db_update) { return $this->fset('last_visit_time', $value, $db_update); }
	function set_first_post_id($first_post_id, $db_update) { return $this->fset('first_post_id', $first_post_id, $db_update); }
	function last_post_id() { return $this->stb_last_post_id; }
	function set_last_post_id($last_post_id, $db_update) { return $this->fset('last_post_id', $last_post_id, $db_update); }
	function set_is_repaged($value, $db_update) { return $this->fset('is_repaged', $value, $db_update); }

	function set_sticky($value, $db_update) { return $this->fset('sticky', $value, $db_update); }
	function set_closed($value, $db_update) { return $this->fset('closed', $value, $db_update); }

	private $forum = false;
	function forum()
	{
		if($this->forum === false)
			if(!$this->forum_id())
				debug_exit('Empty forum_id for topic '.$this->id());
			else
				$this->forum = object_load('airbase_board_forum', $this->forum_id()); 
			
		return $this->forum;
	}
	
	function first_post() { return object_load('airbase_board_post', $this->first_post_id()); }
	function last_post() { return object_load('airbase_board_post', $this->last_post_id()); }
		
	function parents() { return array("airbase_board_forum://".$this->forum_id()); }

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
				return bors_message(ec('Вы не авторизованы на этом домене. Авторизуйтесь, пожалуйста. Если не поможет - попробуйте стереть cookies вашего браузера.'), array('login_form' => true, 'login_referer' => $ref));
			}
			
			$uid = $me->id();
			$x = $this->db()->select('topic_visits', 'last_visit, last_post_id', array('user_id='=>$uid, 'topic_id='=>$this->id()));
			$first_new_post_id = @$x['last_post_id'];
//			exit($first_new_post_id);
//			if(!$first_new_post_id)
			{
				$last_visit = $x['last_visit'];
				$where = array('topic_id='=>$this->id(), 'posted>' => $last_visit);
//				set_loglevel(10,NULL);
				$first_new_post_id = intval($this->db()->select('posts', 'MIN(id)', $where));
//				set_loglevel(2);
//				exit();
			}
					
//			exit("f=".$first_new_post_id);
			if($first_new_post_id)
			{
				$post = object_load('airbase_board_post', $first_new_post_id);

				if($post)
					return go($post->url_in_topic());
			}

//			exit('lkmnj');

			$this->set_page('last');
		}

//		exit('tp='.$this->total_pages());

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
		if(!$this->is_repaged() && rand(0,5) == 0)
			$this->repaging_posts();
	
		$GLOBALS['cms']['cache_disabled'] = true;

		require_once("engines/smarty/assign.php");
		$data = array();

		$data['posts'] = $this->posts();

		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".addslashes($this->title())."'\" />");

		$data['this'] = $this;

		return template_assign_data("templates/TopicBody.html", $data);
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
		);
		
		if($paging && $this->is_repaged())
			$data['`page` = '] = intval($page);
		else
		{
			$data['page'] = $page;
			$data['per_page'] = $this->items_per_page();
		}
			
		return objects_array('airbase_board_post', $data);
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


	function all_users()
	{
		return $this->db()->get_array("SELECT DISTINCT poster_id FROM posts WHERE topic_id={$this->id}");
	}
		
	function cache_static() { return ($this->forum_id() && $this->forum()->is_public_access()) ? rand(86400*7, 86400*30): 0; }
	function base_url() { return $this->forum_id() ? $this->forum()->category()->category_base_full() : '/'; }
		
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
//			$post = class_load('forum_post', $pid);
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
			
		$this->repaging_posts($page);
		
//		$this->recalculate();
	}

	function recalculate()
	{
		$this->store(false);
		
		$num_replies = $this->db()->select('posts', 'COUNT(*)', array('topic_id='=>$this->id())) - 1;
		$this->set_num_replies($num_replies, true);
		$first_pid = $this->db()->select('posts', 'MIN(id)', array('topic_id='=>$this->id()));
		$this->set_first_post_id($first_pid, true);
		$first_post = object_load('airbase_board_post', $first_pid);
		$this->set_create_time($first_post->create_time(true), true);
		$this->set_author_name($first_post->owner()->title(), true);
		$this->set_owner_id($first_post->owner()->id(), true);
		$last_pid = $this->db()->select('posts', 'MAX(id)', array('topic_id='=>$this->id()));
		$this->set_last_post_id($last_pid, true);
		$last_post = object_load('airbase_board_post', $last_pid);
		$this->set_modify_time($last_post->create_time(true), true);
		$this->set_last_poster_name($last_post->owner()->title(), true);

		$this->repaging_posts(1);
		$this->store(false);

		foreach($this->posts() as $p)
			$p->set_body(NULL, true);

		$this->cache_clean_self();
	}

	function cache_children()
	{
		$res = array(
			object_load('airbase_board_topic_printable', $this->id()),
			object_load('airbase_board_topic_rss', $this->id()),
		);

		if($this->forum_id())
			$res[] = object_load('airbase_board_forum', $this->forum_id());

//		TODO: убедиться, что модифицируется только автор сообщения при постинге: блоги, все сообщения и т.п.
//		foreach($this->all_users() as $user_id)
//			$res[] = object_load('forum_user', $user_id);
			
		return $res;
	}


	function cache_dir()
	{
		return dirname($this->static_file());
	}

	function cache_clean_self($page = NULL)
	{
		parent::cache_clean_self($page);
		
//		$this->db('punbb')->query('UPDATE posts SET source_html=NULL WHERE topic_id = '.$this->id());
//		if($posts = $this->all_posts_ids())
//			$this->db('punbb')->query('UPDATE messages SET html=\'\' WHERE id IN (' . join(',', $posts) . ')');
		
		//TODO: подумать на тему неполной чистки.
		foreach(glob($this->cache_dir().'/t'.$this->id().'*.html') as $f)
			@unlink($f);
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

	function repaging_posts($page = NULL)
	{
		if(!$page || !$this->is_repaged())
			$page = 1;
		elseif($page == -1)
			$page = $this->total_pages();
	
		while($page <= $this->total_pages())
		{
			foreach($this->posts($page, false) as $post)
				$post->set_topic_page($page, true);
				
			$this->store(false);
			$page++;
		}
		
		$this->set_is_repaged(1, true);
	}
}
