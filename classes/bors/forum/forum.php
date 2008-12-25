<?php

class forum_forum extends base_page_db
{
	function storage_engine() { return 'storage_db_mysql'; }
	
	function main_table_storage() { return 'forums'; }

	function field_title_storage() { return 'punbb.forums.forum_name(id)'; }

	function __construct($id)
	{
		if(!$id)
			debug_exit('Try to load empty forum');
			
		parent::__construct($id);
	}

	function init()
	{
		if(!$this->id())
			debug_exit('Try to init empty forum');
			
		return parent::init();
	}

	function uri_name() { return 'forum'; }

	var $stb_parent_forum_id = '';
	function parent_forum_id() { return $this->stb_parent_forum_id; }
	function set_parent_forum_id($parent_forum_id, $db_update) { $this->set("parent_forum_id", $parent_forum_id, $db_update); }
	function field_parent_forum_id_storage() { return 'punbb.forums.parent(id)'; }

	var $stb_category_id = '';
	function category_id() { return $this->stb_category_id; }
	function set_category_id($category_id, $db_update) { $this->set("category_id", $category_id, $db_update); }
	function field_category_id_storage() { return 'punbb.forums.cat_id(id)'; }

	private $__category = 0;
	function category()
	{
		if($this->__category !== 0)
			return $this->__category;
	
		$f = $this;
		while($f->category_id() == 0)
			$f = object_load('forum_forum', $f->parent_forum_id());

		return $this->__category = object_load('forum_category', $f->category_id());
	}

	var $stb_keywords_string = '';
	function keywords_string() { return $this->stb_keywords_string; }
	function set_keywords_string($keywords_string, $db_update) { $this->set("keywords_string", $keywords_string, $db_update); }
	function field_keywords_string_storage() { return 'punbb.forums.keywords(id)'; }

function parents()
{
	if($this->parent_forum_id())
		return array("forum_forum://" . $this->parent_forum_id());
		
	if($this->category())
		return array("forum_category://" . $this->category_id());
		
	return array("http://balancer.ru/forum/");
}

	function body()
	{
//			$this->cache_clean_self();
		
		if(!$this->can_read())
			return ec("Извините, доступ к этому ресурсу закрыт для Вас");

		include_once("engines/smarty/assign.php");

		$data = array();

		$topics_per_page = 50;
		$start_from = ($this->page() - 1) * $topics_per_page;
			
		$data['topics'] = $this->db()->get_array("SELECT id FROM topics WHERE forum_id IN (".join(",", $this->all_readable_subforum_ids()).") ORDER BY last_post DESC LIMIT $start_from, $topics_per_page");

//			foreach($topics as $tid)
//				$data['topics'][] = class_load('forum/borsForumTopic', $tid);

		$data['this'] = $this;

		return template_assign_data("templates/ForumBody.html", $data);
	}

	function is_public_access()
	{
		$access = object_load('airbase_forum_access', "{$this->id()}:3");
		if($access)
			return $access->can_read();

		return object_load('forum_group', 3)->can_read();
	}

	function can_read()
	{
		$user = bors()->user();
		$gid = $user ? $user->group_id() : 3;
		if(!$gid)
			$gid = 3;

		$access = object_load('airbase_forum_access', "{$this->id()}:{$gid}");
		if($access)
			return $access->can_read();

		return object_load('forum_group', $gid)->can_read();
	}

	function cache_children()
	{ 
		$children_caches = array();
		if($this->parent_forum_id())
			$children_caches[] = class_load('forum_forum', $this->parent_forum_id());

		if($this->category_id())
			$children_caches[] = class_load('forum_category', $this->category_id());
			
		return $children_caches;
	}
		
/*		function subforums_html()
		{
			include_once('other/punbb-modified-forum/include/subforums.php');
			global $pun_user;
			$pun_user['g_id'] = 1;
		}
*/	
	function direct_subforums_ids()
	{
		// Получаем одни forum_id для дочерних форумов первого уровня
		return $this->db('punbb')->get_array("SELECT id FROM forums WHERE parent = {$this->id()}");
	}

	function direct_subforums()
	{
		$subforums = array();
		foreach($this->direct_subforums_ids() as $forum_id)
			$subforums[] = class_load('forum_forum', $forum_id);
		return $subforums;
	}
		
	function all_subforums(&$processed = array())
	{
		$forums = array();
			
		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = $forums[] = class_load('forum_forum', $forum_id);
			$forums = array_merge($forums, $subforum->all_subforums(&$processed));
		}
			
		return $forums;
	}

	function all_readable_subforum_ids(&$processed = array())
	{
		$forums = array($this->id());
			
		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = object_load('forum_forum', $forum_id);
			$forums = array_merge($forums, $subforum->all_readable_subforum_ids(&$processed));
		}
			
		return $forums;
	}

	private $all_public_subforums_ids = false;
	function all_public_subforum_ids(&$processed = array(), $root = true)
	{
		if($root && $this->all_public_subforums_ids !== false)
			return $this->all_public_subforums_ids;
			
		$forums = array($this->id());
			
		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = object_load('forum_forum', $forum_id);
			if($subforum && $subforum->is_public_access())
				$forums = array_merge($forums, $subforum->all_readable_subforum_ids(&$processed, false));
		}
			
		if($root)
			$this->all_public_subforums_ids = $forums;

		return $forums;
	}

	var $stb_num_topics = '';
	function num_topics() { return $this->stb_num_topics; }
	function set_num_topics($num_topics, $db_update) { $this->set("num_topics", $num_topics, $db_update); }
	function field_num_topics_storage() { return 'punbb.forums.num_topics(id)'; }

	var $stb_num_posts = '';
	function num_posts() { return $this->stb_num_posts; }
	function set_num_posts($num_posts, $db_update) { $this->set("num_posts", $num_posts, $db_update); }
	function field_num_posts_storage() { return 'punbb.forums.num_posts(id)'; }

	function cache_static() { return $this->is_public_access() ? 600 : 0; }

	function topic_update()
	{
		require_once('/var/www/balancer.ru/htdocs/cms/other/punbb-modified-forum/include/functions.php');
//		update_
	}

//	function url_engine() { return 'url_titled'; }
	function url() { return $this->category()->category_base_full().'viewforum.php?id='.$this->id(); }
	function cache_static_can_be_dropped() { return false; }
}
