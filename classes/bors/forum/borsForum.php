<?
	require_once('borsForumAbstract.php');
	class borsForum extends borsForumAbstract
	{
		function uri_name() { return 'forum'; }
		function class_name() { return 'forum/borsForum'; }

		function field_title_storage() { return 'punbb.forums.forum_name(id)'; }

		var $stb_parent_forum_id = '';
		function parent_forum_id() { return $this->stb_parent_forum_id; }
		function set_parent_forum_id($parent_forum_id, $db_update = false) { $this->set("parent_forum_id", $parent_forum_id, $db_update); }
		function field_parent_forum_id_storage() { return 'punbb.forums.parent(id)'; }

		var $stb_category_id = '';
		function category_id() { return $this->stb_category_id; }
		function set_category_id($category_id, $db_update = false) { $this->set("category_id", $category_id, $db_update); }
		function field_category_id_storage() { return 'punbb.forums.cat_id(id)'; }

        function parents()
		{
			if($this->parent_forum_id())
				return array("forum.borsForum" . $this->parent_forum_id());
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

			if($this->is_public_access())
				$GLOBALS['cms']['cache_static'] = true;

			include_once("funcs/templates/assign.php");

			$bors->config()->set_cache_uri($this->internal_uri());
			
			$data = array();

			$db = &new DataBase('punbb');

			$topics_per_page = 50;
			$start_from = ($this->page() - 1) * $topics_per_page;
			
			$data['topics'] = $db->get_array("SELECT id FROM topics WHERE forum_id={$this->id()} ORDER BY last_post DESC LIMIT $start_from, $topics_per_page");

//			foreach($topics as $tid)
//				$data['topics'][] = class_load('forum/borsForumTopic', $tid);

			$data['this'] = $this;

			return template_assign_data("templates/ForumBody.html", $data);
		}

		function is_public_access()
		{
			$access = class_load('forum/borsForumAccess', "{$this->id()}:3");
			return $access->can_read();
		}

		function can_read()
		{
			$access = class_load('forum/borsForumAccess', "{$this->id()}:" . class_load('borsUser', -1)->group_id());
			return $access->can_read();
		}

		function cache_parents()
		{ 
			$parent_caches = array();
			if($this->parent_forum_id())
				$parent_caches[] = class_load('forum.borsForum', $this->parent_forum_id());

			if($this->category_id())
				$parent_caches[] = class_load('forum.borsForumCategory', $this->category_id());
			
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
				$subforums[] = class_load('forum/borsForum', $forum_id);
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
				$subforum = $forums[] = class_load('forum/borsForum', $forum_id);
				$forums = array_merge($forums, $subforum->all_subforums(&$processed));
			}
			
			return $forums;
		}

		var $stb_num_topics = '';
		function num_topics() { return $this->stb_num_topics; }
		function set_num_topics($num_topics, $db_update = false) { $this->set("num_topics", $num_topics, $db_update); }
		function field_num_topics_storage() { return 'punbb.forums.num_topics(id)'; }

		var $stb_num_posts = '';
		function num_posts() { return $this->stb_num_posts; }
		function set_num_posts($num_posts, $db_update = false) { $this->set("num_posts", $num_posts, $db_update); }
		function field_num_posts_storage() { return 'punbb.forums.num_posts(id)'; }
	}
