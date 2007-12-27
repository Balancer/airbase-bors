<?php

require_once('borsForumAbstract.php');
class forum_forum extends borsForumAbstract
{
	function _class_file() { return __FILE__; }

	function main_table_storage() { return 'forums'; }
	
	function field_title_storage() { return 'punbb.forums.forum_name(id)'; }

	function uri_name() { return 'forum'; }

	var $stb_parent_forum_id = '';
	function parent_forum_id() { return $this->stb_parent_forum_id; }
	function set_parent_forum_id($parent_forum_id, $db_update) { $this->set("parent_forum_id", $parent_forum_id, $db_update); }
	function field_parent_forum_id_storage() { return 'punbb.forums.parent(id)'; }

	var $stb_category_id = '';
	function category_id() { return $this->stb_category_id; }
	function set_category_id($category_id, $db_update) { $this->set("category_id", $category_id, $db_update); }
	function field_category_id_storage() { return 'punbb.forums.cat_id(id)'; }

	function category() { return class_load('forum_category', $this->category_id()); }

	function parents()
	{
		if($this->parent_forum_id())
			return array("forum_forum://" . $this->parent_forum_id());
		else
			return array("http://balancer.ru/forum-new/");
//				return array(array('forumCategory', $this->category_id() ));
	}

	function body()
	{
//			$this->cache_clean_self();
		
		global $bors;

			
		if(!$this->can_read())
			return ec("Извините, доступ к этому ресурсу закрыт для Вас");


		include_once("funcs/templates/assign.php");

		$bors->config()->set_cache_uri($this->internal_uri());
			
		$data = array();

		$db = &new DataBase('punbb');

		$topics_per_page = 50;
		$start_from = ($this->page() - 1) * $topics_per_page;
			
		$data['topics'] = $db->get_array("SELECT id FROM topics WHERE forum_id IN (".join(",", $this->all_readable_subforum_ids()).") ORDER BY last_post DESC LIMIT $start_from, $topics_per_page");

//			foreach($topics as $tid)
//				$data['topics'][] = class_load('forum/borsForumTopic', $tid);

		$data['this'] = $this;

		return template_assign_data("templates/ForumBody.html", $data);
	}

	function is_public_access()
	{
		$can_read = class_load('forum_access', "{$this->id()}:3")->can_read();
//		print_r($can_read);
//		exit();

		if($can_read === NULL)
			$can_read = class_load('forum_group', 3)->can_read();

		return $can_read;
	}

	function can_read()
	{
		$user = class_load('forum_user', -1);
		$gid = $user ? $user->group_id() : 3;
		if(!$gid)
			$gid = 3;

		$can_read = class_load('forum_access', "{$this->id()}:$gid")->can_read();
//		exit("gid = $gid, can_read = ".print_r($can_read, true));

		if($can_read === NULL)
			$can_read = class_load('forum_group', $gid)->can_read();

		return $can_read;
	}

	function cache_parents()
	{ 
		$parent_caches = array();
		if($this->parent_forum_id())
			$parent_caches[] = class_load('forum_forum', $this->parent_forum_id());

		if($this->category_id())
			$parent_caches[] = class_load('forum_category', $this->category_id());
			
		return $parent_caches;
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
		$db = &new DataBase('punbb');
			
		return $db->get_array("SELECT id FROM forums WHERE parent = {$this->id()}");
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

	function url_engine() { return 'url_titled'; }
}
